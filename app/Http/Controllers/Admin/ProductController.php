<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Gemstone;
use App\Models\MetalType;
use App\Models\Occasion;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $categoryId = $request->query('category');

        $query = Product::query()->with('category:id,name');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'inactive') {
            $query->where('is_active', false);
        }

        if ($categoryId) {
            $query->where('category_id', (int) $categoryId);
        }

        $products = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return view('admin.products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'filters' => ['q' => $search, 'status' => $status, 'category' => $categoryId],
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'metalTypes' => MetalType::orderBy('name')->get(['id', 'name']),
            'gemstones' => Gemstone::orderBy('name')->get(['id', 'name']),
            'occasions' => Occasion::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        // Checkboxes: absent in the POST body when unchecked, so a plain
        // 'nullable|boolean' rule would store NULL instead of false.
        // $request->boolean() resolves that correctly either way.
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        $product = Product::create($validated);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product created. You can now add images and variants below.');
    }

    public function edit(Product $product): View
    {
        $product->load([
            'images' => fn ($q) => $q->orderBy('sort_order'),
            'variants' => fn ($q) => $q->orderBy('sort_order'),
        ]);

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(['id', 'name']),
            'metalTypes' => MetalType::orderBy('name')->get(['id', 'name']),
            'gemstones' => Gemstone::orderBy('name')->get(['id', 'name']),
            'occasions' => Occasion::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validated($request, $product);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');

        $product->update($validated);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product updated.');
    }

    /**
     * Soft-delete (the products table already has deleted_at — this never
     * hard-deletes, so historical order_items referencing this product via
     * its nullable/nullOnDelete FK stay intact either way, and the product
     * can be restored later if needed).
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product removed.');
    }

    public function toggleActive(Product $product): RedirectResponse
    {
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('success', $product->is_active ? 'Product activated.' : 'Product deactivated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('products', 'slug')->ignore($product?->id)],
            'sku' => ['required', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($product?->id)],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', function ($attribute, $value, $fail) use ($request) {
                if ($value !== null && (float) $value >= (float) $request->input('price', 0)) {
                    $fail('The sale price must be less than the regular price.');
                }
            }],
            'gender' => ['nullable', 'string', 'max:20'],
            'metal_type_id' => ['nullable', 'integer', 'exists:metal_types,id'],
            'gemstone_id' => ['nullable', 'integer', 'exists:gemstones,id'],
            'metal_purity' => ['nullable', 'string', 'max:255'],
            'occasion_id' => ['nullable', 'integer', 'exists:occasions,id'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
