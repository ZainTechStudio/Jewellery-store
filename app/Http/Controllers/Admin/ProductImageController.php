<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Upload a new image for a product. The original filename is never
     * trusted or used — Storage::putFile() (via store()) generates a
     * random name itself. MIME/type and size are validated by Laravel's
     * `image`/`mimes`/`max` rules before the file ever touches disk.
     *
     * image_path is stored as "storage/products/<generated>.jpg", which
     * — like the seeded "/img/pX.jpg" paths — resolves correctly through
     * the same asset($image->image_path) call already used everywhere
     * on the storefront, so no template changes were needed for this.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $path = $request->file('image')->store('products', 'public');

        $nextSort = ((int) $product->images()->max('sort_order')) + 10;
        $isFirst = $product->images()->count() === 0;

        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => 'storage/'.$path,
            'alt_text' => $validated['alt_text'] ?? $product->name,
            'sort_order' => $nextSort,
            'is_primary' => $isFirst,
        ]);

        return back()->with('success', 'Image added.');
    }

    public function setPrimary(ProductImage $image): RedirectResponse
    {
        ProductImage::where('product_id', $image->product_id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image updated.');
    }

    public function destroy(ProductImage $image): RedirectResponse
    {
        // Only remove the file from disk if it's one we stored ourselves
        // (under storage/products/...) — the seeded "/img/pX.jpg" placeholder
        // paths point at public/img directly and must never be deleted here.
        if (str_starts_with($image->image_path, 'storage/products/')) {
            Storage::disk('public')->delete(str_replace('storage/', '', $image->image_path));
        }

        $wasPrimary = $image->is_primary;
        $productId = $image->product_id;
        $image->delete();

        if ($wasPrimary) {
            $next = ProductImage::where('product_id', $productId)->orderBy('sort_order')->first();
            $next?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Image removed.');
    }
}
