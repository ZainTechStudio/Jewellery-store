<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Show the shopping bag page.
     *
     * Fully database-backed. The cart is resolved by Cart::findCurrent()
     * — the authenticated user's cart when logged in, otherwise the guest
     * session's cart (see app/Models/Cart.php). Read-only lookup: just
     * viewing the page never creates a cart row.
     */
    public function index(): View
    {
        $cart = Cart::findCurrent();

        return view('cart', $this->cartData($cart));
    }

    /**
     * Add a product (optionally a variant) to the current cart (the
     * authenticated user's cart, or the guest session's cart).
     *
     * Price, product name/image/SKU and stock are never trusted from the
     * client — only product_id, variant_id and quantity are accepted, and
     * everything else is looked up from the database.
     */
    public function add(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'variant_id' => ['nullable', 'integer'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::where('is_active', true)->find($validated['product_id']);

        if (! $product) {
            return $this->cartResponse($request, false, 'This item is no longer available.');
        }

        $variant = null;

        if (! empty($validated['variant_id'])) {
            $variant = ProductVariant::where('product_id', $product->id)
                ->where('is_active', true)
                ->find($validated['variant_id']);

            if (! $variant) {
                return $this->cartResponse($request, false, 'That option is no longer available.');
            }
        }

        $stock = $variant ? $variant->stock_quantity : $product->stock_quantity;

        if ($stock <= 0) {
            return $this->cartResponse($request, false, $product->name.' is currently out of stock.');
        }

        $cart = Cart::current();

        // Same product + same variant (including "no variant" for both) →
        // merge into the existing row. Different variant → separate row.
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variant?->id)
            ->first();

        $unitPrice = ($variant && $variant->price !== null)
            ? ($variant->sale_price ?? $variant->price)
            : ($product->sale_price ?? $product->price);

        $requestedQty = $validated['quantity'] ?? 1;
        $newQty = ($cartItem->quantity ?? 0) + $requestedQty;
        $capped = $newQty > $stock;
        $newQty = min($newQty, $stock);

        if ($cartItem) {
            $cartItem->update(['quantity' => $newQty, 'unit_price' => $unitPrice]);
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'product_variant_id' => $variant?->id,
                'quantity' => $newQty,
                'unit_price' => $unitPrice,
            ]);
        }

        $message = $capped
            ? $product->name.' added — only '.$stock.' in stock, quantity adjusted.'
            : $product->name.' added to bag';

        return $this->cartResponse($request, true, $message);
    }

    /**
     * Update a cart item's quantity. Ownership of the item by the current
     * cart (user's or guest session's) is verified before any change
     * (implicit route-model binding resolves the row, but binding alone
     * does not check ownership — that happens explicitly here).
     */
    public function update(Request $request, CartItem $cartItem): RedirectResponse|JsonResponse
    {
        $cart = Cart::findCurrent();

        if (! $cart || $cartItem->cart_id !== $cart->id) {
            abort(404);
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cartItem->loadMissing(['variant', 'product']);

        $stock = $cartItem->variant
            ? $cartItem->variant->stock_quantity
            : ($cartItem->product->stock_quantity ?? 0);

        $capped = $validated['quantity'] > $stock;
        $qty = min($validated['quantity'], max($stock, 1));

        $cartItem->update(['quantity' => $qty]);

        $message = $capped ? 'Only '.$stock.' in stock — quantity adjusted.' : 'Quantity updated';

        return $this->cartResponse($request, true, $message);
    }

    /**
     * Remove a single item from the current cart (ownership verified the
     * same way as update()).
     */
    public function remove(Request $request, CartItem $cartItem): RedirectResponse|JsonResponse
    {
        $cart = Cart::findCurrent();

        if (! $cart || $cartItem->cart_id !== $cart->id) {
            abort(404);
        }

        $cartItem->delete();

        return $this->cartResponse($request, true, 'Item removed from bag');
    }

    /**
     * Empty the current cart only — never touches anyone else's cart.
     */
    public function clear(Request $request): RedirectResponse|JsonResponse
    {
        $cart = Cart::findCurrent();

        if ($cart) {
            $cart->items()->delete();
        }

        return $this->cartResponse($request, true, 'Bag emptied');
    }

    /**
     * Shared response builder for every cart action: AJAX/fetch callers
     * (Accept: application/json) get the fresh cart state back as JSON so
     * JS can update the page without a reload; normal form submissions get
     * redirected back to the cart page with a flash message, per the
     * existing Laravel convention.
     */
    private function cartResponse(Request $request, bool $success, string $message): RedirectResponse|JsonResponse
    {
        $cart = Cart::findCurrent();
        $data = $this->cartData($cart);

        if ($request->wantsJson()) {
            return response()->json(
                array_merge(['success' => $success, 'message' => $message], $data),
                $success ? 200 : 422
            );
        }

        return redirect()->route('cart.index')->with($success ? 'success' : 'error', $message);
    }

    /**
     * Build the cart view-model (rows + totals) shared by the Blade page
     * render and the JSON responses, from a single eager-loaded query —
     * no per-item product lookups.
     *
     * @return array{items: \Illuminate\Support\Collection, count: int, subtotal: float, formatted_subtotal: string, tax: float, formatted_tax: string, total: float, formatted_total: string}
     */
    private function cartData(?Cart $cart): array
    {
        $items = $cart
            ? $cart->items()
                ->with([
                    'product:id,category_id,name,slug,price,sale_price,stock_quantity',
                    'product.images' => fn ($q) => $q->orderBy('sort_order'),
                    'variant:id,product_id,name,options,stock_quantity',
                ])
                ->get()
            : collect();

        $rows = $items->map(function (CartItem $item) {
            $product = $item->product;
            $image = $product?->images->firstWhere('is_primary', true) ?? $product?->images->first();
            $lineSubtotal = (float) $item->unit_price * $item->quantity;

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'variant_id' => $item->product_variant_id,
                'name' => $product->name ?? 'Product no longer available',
                'variant_label' => $item->variant->name ?? null,
                'image' => $image ? asset($image->image_path) : asset('img/p1.jpg'),
                'url' => $product ? route('product.show', $product) : '#',
                'unit_price' => (float) $item->unit_price,
                'formatted_unit_price' => '$'.number_format((float) $item->unit_price),
                'quantity' => $item->quantity,
                'line_subtotal' => $lineSubtotal,
                'formatted_line_subtotal' => '$'.number_format($lineSubtotal),
            ];
        })->values();

        // Same cosmetic 8%-of-subtotal estimate the cart page has always
        // shown (not real checkout tax logic, which is out of scope here).
        $subtotal = (float) $rows->sum('line_subtotal');
        $tax = round($subtotal * 0.08);
        $total = $subtotal + $tax;

        return [
            'items' => $rows,
            'count' => (int) $items->sum('quantity'),
            'subtotal' => $subtotal,
            'formatted_subtotal' => '$'.number_format($subtotal),
            'tax' => $tax,
            'formatted_tax' => '$'.number_format($tax),
            'total' => $total,
            'formatted_total' => '$'.number_format($total),
        ];
    }
}
