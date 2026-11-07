<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class ShopController extends Controller
{
    /**
     * Show the shop listing page.
     *
     * The grid, search box, category filter and sort dropdown are still
     * driven client-side (public/js/shop.js) against the static
     * catalogue at this stage. Server-side filtering, search, sorting
     * and pagination against the products table are implemented in
     * Phase 3.
     */
    public function index(): View
    {
        return view('shop');
    }
}
