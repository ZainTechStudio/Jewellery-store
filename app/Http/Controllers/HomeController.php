<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Show the storefront home page.
     *
     * Phase 2: featured products and category tiles are now read from
     * the database (products/categories tables) instead of the
     * hardcoded JS catalogue. Only active + featured products are
     * shown, ordered by sort_order, images eager-loaded to avoid N+1.
     */
    public function index(): View
    {
        $featuredProducts = Product::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->with([
                'category:id,name,slug',
                'images' => fn ($q) => $q->orderBy('sort_order'),
            ])
            ->orderBy('sort_order')
            ->take(4)
            ->get();

        // The homepage only ever shows 3 category tiles — take the top 3
        // active top-level categories by sort_order (Rings/Necklaces/
        // Earrings in the seeded data, matching the original static markup).
        $topCategories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->take(3)
            ->get(['id', 'name', 'slug']);

        return view('home', [
            'featuredProducts' => $featuredProducts,
            'topCategories' => $topCategories,
        ]);
    }
}
