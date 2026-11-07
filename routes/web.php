<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [ShopController::class, 'index'])->name('shop');

// Phase 2: was a `?id=` query-string lookup against the hardcoded JS
// catalogue. Now a proper slug route with implicit route-model binding
// against the products table (route name kept stable: product.show).
Route::get('/product/{product:slug}', [ProductController::class, 'show'])->name('product.show');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// Phase 3: database-backed cart actions, scoped to the current session's
// cart (see Cart::current()/findCurrent() and the ownership checks in
// CartController). No checkout/order/payment routes here — just the cart.
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/contact', [PageController::class, 'contact'])->name('contact');
