<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AuthController;
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

// Phase 3: database-backed cart actions, scoped to the current cart
// (guest session or, as of Phase 4, the authenticated user — see
// Cart::current()/findCurrent()) and the ownership checks in
// CartController. No checkout/order/payment routes here — just the cart.
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/contact', [PageController::class, 'contact'])->name('contact');

// Phase 4: authentication. GET/POST pairs on the same URI get distinct
// route names (login / login.attempt, register / register.store) so
// both are addressable without ambiguity.
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Phase 4: customer account area — profile, password, addresses only.
// Order history is explicitly Phase 5+, not added here.
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');

    Route::get('/profile', [AccountController::class, 'editProfile'])->name('profile.edit');
    Route::patch('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');

    Route::get('/password', [AccountController::class, 'editPassword'])->name('password.edit');
    Route::patch('/password', [AccountController::class, 'updatePassword'])->name('password.update');

    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::get('/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::patch('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
});
