<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ProductController extends Controller
{
    /**
     * Show the product detail page.
     *
     * The original static frontend selects the product to display via
     * a `?id=` query string, read client-side in public/js/product.js.
     * That behaviour is preserved as-is for now so nothing breaks.
     * Phase 3 replaces this with a proper `/product/{slug}` route
     * backed by the products table, real image gallery, variants,
     * stock and category-based related products.
     */
    public function show(): View
    {
        return view('product');
    }
}
