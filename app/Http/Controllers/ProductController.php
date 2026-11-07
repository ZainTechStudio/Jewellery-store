<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    /**
     * Show the product detail page.
     *
     * Phase 2: replaces the old `?id=` query-string lookup against the
     * hardcoded JS catalogue with a real `/product/{slug}` route using
     * implicit route-model binding (see routes/web.php). Laravel
     * automatically 404s for an unknown slug (and for soft-deleted
     * products); inactive products are explicitly 404'd here too, since
     * binding alone doesn't know about the is_active flag.
     *
     * Related products are now genuinely category-based (Phase 0's
     * audit flagged the old behaviour — "related" was really just
     * "other random products" — as a bug; this fixes it using existing
     * data only, no schema change).
     */
    public function show(Product $product): View
    {
        if (! $product->is_active) {
            abort(404);
        }

        $product->load([
            'category:id,name,slug',
            'images' => fn ($q) => $q->orderBy('sort_order'),
            'variants' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
            'metalType:id,name,slug',
            'gemstone:id,name,slug',
            'occasion:id,name,slug',
        ]);

        $relatedProducts = Product::query()
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with([
                'category:id,name,slug',
                'images' => fn ($q) => $q->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        return view('product', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }
}
