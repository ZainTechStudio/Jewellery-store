<?php

namespace App\Providers;

use App\Models\Cart;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Phase 3: the navbar bag icon shows the database cart's total item
         * quantity. Scoped to the navbar partial only (not shared globally)
         * so this is exactly one lightweight query per page load — not an
         * extra query bolted onto every view, and it never creates a cart
         * row just because someone is browsing (findCurrent() is read-only).
         */
        View::composer('partials.navbar', function ($view) {
            $cart = Cart::findCurrent();

            $view->with('cartCount', $cart ? (int) $cart->items()->sum('quantity') : 0);
        });
    }
}
