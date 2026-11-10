<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validated($request);

        $nextSort = ((int) $product->variants()->max('sort_order')) + 10;

        ProductVariant::create($validated + [
            'product_id' => $product->id,
            'sort_order' => $nextSort,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Variant added.');
    }

    public function update(Request $request, ProductVariant $variant): RedirectResponse
    {
        $validated = $this->validated($request, $variant);
        $validated['is_active'] = $request->boolean('is_active');

        $variant->update($validated);

        return back()->with('success', 'Variant updated.');
    }

    public function destroy(ProductVariant $variant): RedirectResponse
    {
        $variant->delete();

        return back()->with('success', 'Variant removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?ProductVariant $variant = null): array
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('product_variants', 'sku')->ignore($variant?->id)],
            'option_key' => ['required', 'string', 'max:50'],
            'option_value' => ['required', 'string', 'max:50'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
        ]);

        // The schema stores variant options as a single JSON column
        // (e.g. {"ring_size":"7"}) — same design as Phase 1/3, no new
        // attribute system. The admin form submits the key/value pair
        // separately only because that's simpler to edit than raw JSON.
        $validated['options'] = [$validated['option_key'] => $validated['option_value']];
        unset($validated['option_key'], $validated['option_value']);

        if (empty($validated['name'])) {
            $validated['name'] = ucfirst(str_replace('_', ' ', array_key_first($validated['options']))).' '.array_values($validated['options'])[0];
        }

        return $validated;
    }
}
