<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class CartController extends Controller
{
    /**
     * Show the shopping bag page.
     *
     * Still backed by the browser's localStorage cart (public/js/cart.js)
     * at this stage. Phase 4 replaces this with a session-based cart for
     * guests and a database-backed cart for logged-in customers, with
     * server-side price/total calculation.
     */
    public function index(): View
    {
        return view('cart');
    }
}
