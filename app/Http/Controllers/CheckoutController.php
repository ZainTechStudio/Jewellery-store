<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Show the checkout review page: the authenticated customer's cart
     * (re-read from the database, not trusted from any prior page),
     * their saved addresses to choose from, and contact details
     * pre-filled from their profile.
     */
    public function index(): View|RedirectResponse
    {
        $cart = Cart::findCurrent();

        if (! $cart || $cart->items()->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your bag is empty — add something before checking out.');
        }

        $user = Auth::user();

        return view('checkout.index', [
            'cart' => $this->cartSummary($cart),
            'addresses' => $user->addresses()->orderByDesc('is_default')->orderByDesc('id')->get(),
            'user' => $user,
        ]);
    }

    /**
     * Place the order.
     *
     * Everything that matters is re-derived server-side inside a single
     * DB transaction: the address must belong to the authenticated
     * customer, every cart line is re-validated against the live
     * product/variant (active, in stock), prices are recalculated fresh
     * (never trusted from the cart's possibly-stale snapshot or from the
     * request), stock is decremented atomically, and the cart is only
     * cleared once the order has actually been created. Any failure
     * rolls the whole thing back — no partial orders.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_address_id' => ['required', 'integer'],
            'phone' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = Auth::user();

        // Ownership check — never trust a raw address id from the client.
        $address = Address::where('id', $validated['shipping_address_id'])
            ->where('user_id', $user->id)
            ->first();

        if (! $address) {
            return redirect()->route('checkout.index')->with('error', 'Please choose a valid delivery address.');
        }

        try {
            $order = DB::transaction(function () use ($user, $address, $validated) {
                // Lock the cart row for the duration of the transaction as a
                // safety net against a near-simultaneous double submit
                // (the obvious case — not a full idempotency-key system).
                $cart = Cart::where('user_id', $user->id)->lockForUpdate()->first();

                if (! $cart) {
                    throw new \RuntimeException('EMPTY_CART');
                }

                $items = $cart->items()->with(['product', 'variant'])->get();

                if ($items->isEmpty()) {
                    throw new \RuntimeException('EMPTY_CART');
                }

                $lines = [];
                $subtotal = 0.0;

                foreach ($items as $item) {
                    $product = Product::where('is_active', true)->find($item->product_id);

                    if (! $product) {
                        throw new \RuntimeException("UNAVAILABLE:{$item->id}");
                    }

                    $variant = null;

                    if ($item->product_variant_id) {
                        $variant = ProductVariant::where('product_id', $product->id)
                            ->where('is_active', true)
                            ->find($item->product_variant_id);

                        if (! $variant) {
                            throw new \RuntimeException("UNAVAILABLE:{$item->id}");
                        }
                    }

                    $qty = $item->quantity;

                    if ($qty < 1) {
                        throw new \RuntimeException("UNAVAILABLE:{$item->id}");
                    }

                    $stock = $variant ? $variant->stock_quantity : $product->stock_quantity;

                    if ($qty > $stock) {
                        throw new \RuntimeException("STOCK:{$product->name}:{$stock}");
                    }

                    // Current server-side price — never the cart's stored
                    // snapshot, which may be stale by now.
                    $unitPrice = ($variant && $variant->price !== null)
                        ? ($variant->sale_price ?? $variant->price)
                        : ($product->sale_price ?? $product->price);

                    $lineSubtotal = (float) $unitPrice * $qty;
                    $subtotal += $lineSubtotal;

                    $lines[] = [
                        'product' => $product,
                        'variant' => $variant,
                        'qty' => $qty,
                        'unit_price' => $unitPrice,
                        'subtotal' => $lineSubtotal,
                    ];
                }

                // Atomic, race-safe stock decrement for every line — if any
                // one fails (stock moved under us) the whole order aborts.
                foreach ($lines as $line) {
                    if ($line['variant']) {
                        $updated = ProductVariant::where('id', $line['variant']->id)
                            ->where('stock_quantity', '>=', $line['qty'])
                            ->decrement('stock_quantity', $line['qty']);
                    } else {
                        $updated = Product::where('id', $line['product']->id)
                            ->where('stock_quantity', '>=', $line['qty'])
                            ->decrement('stock_quantity', $line['qty']);
                    }

                    if (! $updated) {
                        throw new \RuntimeException("STOCK:{$line['product']->name}:0");
                    }
                }

                // Same cosmetic 8%-of-subtotal estimate the cart page has
                // always shown, and shipping is "Complimentary" (0) exactly
                // as the storefront has always stated — not new rules, just
                // the existing ones applied server-side at order time. No
                // coupon system is wired up yet, so discount stays 0.
                $tax = round($subtotal * 0.08, 2);
                $shippingFee = 0.0;
                $discount = 0.0;
                $total = $subtotal + $tax + $shippingFee - $discount;

                $order = Order::create([
                    'order_number' => $this->generateOrderNumber(),
                    'user_id' => $user->id,
                    'contact_name' => $user->name,
                    'phone' => $validated['phone'],
                    'email' => $user->email,
                    'status' => 'pending',
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'shipping_fee' => $shippingFee,
                    'discount' => $discount,
                    'total' => $total,
                    'currency' => 'USD',
                    'shipping_address_id' => $address->id,
                    'payment_method' => 'cod',
                    'coupon_code' => null,
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($lines as $line) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $line['product']->id,
                        'product_variant_id' => $line['variant']?->id,
                        'product_name' => $line['product']->name,
                        'sku' => $line['variant']?->sku ?? $line['product']->sku,
                        'product_options' => $line['variant']?->options,
                        'quantity' => $line['qty'],
                        'unit_price' => $line['unit_price'],
                        'subtotal' => $line['subtotal'],
                    ]);
                }

                // Only clear the cart once the order has actually committed.
                $cart->items()->delete();

                return $order;
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('checkout.index')->with('error', $this->friendlyErrorMessage($e->getMessage()));
        }

        return redirect()->route('checkout.success', $order)->with('success', 'Order placed.');
    }

    /**
     * Order confirmation page. Ownership is checked explicitly — implicit
     * route-model binding resolves any order by id regardless of owner.
     */
    public function success(Order $order): View
    {
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }

        $order->load(['items', 'shippingAddress']);

        return view('checkout.success', ['order' => $order]);
    }

    /**
     * AUR-YYYYMMDD-XXXX — no existing order-number convention was in use
     * anywhere in the project before this (no order has ever been
     * created), so this is a fresh, reasonable, unique-checked format.
     */
    private function generateOrderNumber(): string
    {
        do {
            $candidate = 'AUR-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
        } while (Order::where('order_number', $candidate)->exists());

        return $candidate;
    }

    private function friendlyErrorMessage(string $code): string
    {
        if ($code === 'EMPTY_CART') {
            return 'Your bag is empty — add something before checking out.';
        }

        if (str_starts_with($code, 'STOCK:')) {
            [, $name, $stock] = explode(':', $code, 3);

            return $stock > 0
                ? "Only {$stock} of \"{$name}\" left in stock — please update the quantity in your bag."
                : "\"{$name}\" just sold out — please remove it from your bag.";
        }

        if (str_starts_with($code, 'UNAVAILABLE:')) {
            return 'One of the items in your bag is no longer available — please remove it and try again.';
        }

        return 'We could not place your order — please try again.';
    }

    /**
     * @return array{items: \Illuminate\Support\Collection, count: int, formatted_subtotal: string, formatted_tax: string, formatted_total: string}
     */
    private function cartSummary(Cart $cart): array
    {
        $items = $cart->items()
            ->with([
                'product:id,name,slug,price,sale_price,stock_quantity',
                'product.images' => fn ($q) => $q->orderBy('sort_order'),
                'variant:id,product_id,name,options,price,sale_price,stock_quantity',
            ])
            ->get();

        $rows = $items->map(function ($item) {
            $product = $item->product;
            $variant = $item->variant;
            $image = $product?->images->firstWhere('is_primary', true) ?? $product?->images->first();

            $unitPrice = ($variant && $variant->price !== null)
                ? ($variant->sale_price ?? $variant->price)
                : ($product ? ($product->sale_price ?? $product->price) : $item->unit_price);

            $lineSubtotal = (float) $unitPrice * $item->quantity;

            return [
                'name' => $product->name ?? 'Product no longer available',
                'variant_label' => $variant->name ?? null,
                'image' => $image ? asset($image->image_path) : asset('img/p1.jpg'),
                'quantity' => $item->quantity,
                'formatted_unit_price' => '$'.number_format((float) $unitPrice),
                'formatted_line_subtotal' => '$'.number_format($lineSubtotal),
                'line_subtotal' => $lineSubtotal,
            ];
        })->values();

        $subtotal = (float) $rows->sum('line_subtotal');
        $tax = round($subtotal * 0.08, 2);
        $total = $subtotal + $tax;

        return [
            'items' => $rows,
            'count' => (int) $items->sum('quantity'),
            'formatted_subtotal' => '$'.number_format($subtotal),
            'formatted_tax' => '$'.number_format($tax),
            'formatted_total' => '$'.number_format($total),
        ];
    }
}
