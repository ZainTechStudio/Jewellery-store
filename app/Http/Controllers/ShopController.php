<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Whitelisted sort values → [column, direction]. Never interpolate
     * the request's sort value directly into orderBy().
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const SORTS = [
        'featured' => ['sort_order', 'asc'],
        'low' => ['price', 'asc'],
        'high' => ['price', 'desc'],
        'name' => ['name', 'asc'],
    ];

    /**
     * Show the shop listing page.
     *
     * Phase 2: the grid, search, category filter and sort are now
     * server-side against the products table (GET query params so the
     * URL represents filter state, e.g. /shop?category=rings&sort=low).
     * The old client-side array filter in public/js/shop.js is gone —
     * shop.js now only auto-submits this filter form.
     *
     * The existing UI only ever had category + search + sort controls
     * (no metal/gemstone/occasion/gender/price filters), so per the
     * Phase 2 instructions those are not implemented here — there is no
     * UI for them to wire up without introducing a new filter design.
     */
    public function index(Request $request): View
    {
        $sort = $request->query('sort');
        $sort = array_key_exists($sort, self::SORTS) ? $sort : 'featured';

        $categorySlug = $request->query('category');
        $search = trim((string) $request->query('q', ''));

        $query = Product::query()
            ->where('is_active', true)
            ->with([
                'category:id,name,slug',
                'images' => fn ($q) => $q->orderBy('sort_order'),
            ]);

        $selectedCategory = null;

        if ($categorySlug) {
            $selectedCategory = Category::where('slug', $categorySlug)
                ->where('is_active', true)
                ->first(['id', 'name', 'slug']);

            if ($selectedCategory) {
                // A parent category (e.g. "Rings") should also match
                // products filed directly under its children (e.g.
                // "Engagement Rings"), since the shop filter only lists
                // top-level categories.
                $categoryIds = $selectedCategory->children()->pluck('id')
                    ->push($selectedCategory->id)
                    ->all();

                $query->whereIn('category_id', $categoryIds);
            } else {
                // Unknown category slug in the URL — show no results
                // rather than silently falling back to the full catalogue.
                $query->whereIn('id', []);
            }
        }

        if ($search !== '') {
            // Storefront search matches the product name/title only (not
            // description, short_description, sku, category, or gemstone).
            $query->where('name', 'like', "%{$search}%");
        }

        if ($sort === 'featured') {
            $query->orderByDesc('is_featured')->orderBy('sort_order');
        } else {
            [$column, $direction] = self::SORTS[$sort];
            $query->orderBy($column, $direction);
        }

        $products = $query->get();

        $categories = Category::query()
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug']);

        return view('shop', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'search' => $search,
            'sort' => $sort,
        ]);
    }
}
