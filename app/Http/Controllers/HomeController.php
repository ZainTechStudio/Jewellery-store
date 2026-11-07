<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Show the storefront home page.
     *
     * Featured products are still rendered client-side from the static
     * catalogue in public/js/main.js at this stage of the migration.
     * This will be replaced with real data from the products table
     * in Phase 3.
     */
    public function index(): View
    {
        return view('home');
    }
}
