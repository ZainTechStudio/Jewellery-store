<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\AttributeController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
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

    // Phase 6 Part A
    Route::get('/orders', [AccountController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{order}', [AccountController::class, 'orderShow'])->name('orders.show');

    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::get('/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::patch('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
});

// Phase 5: checkout requires authentication — an unauthenticated visitor
// hitting these is bounced to /login by the 'auth' middleware and, since
// AuthController::login() already uses redirect()->intended(), lands back
// here automatically after signing in. The guest cart they had is merged
// into their account cart by the existing Phase 4 login flow before they
// even see this page again.
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
});

// Phase 6 Part B — Admin panel. 'auth' first (unauthenticated → /login,
// same convention as every other protected area), then 'admin' (see
// AdminMiddleware) checks Auth::user()->role server-side — never a UI-only
// hidden-link scheme. No public "become an admin" path exists anywhere;
// the only way to get the admin role is the dedicated AdminUserSeeder.
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::patch('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::patch('/products/{product}/toggle-active', [AdminProductController::class, 'toggleActive'])->name('products.toggle-active');

    Route::post('/products/{product}/images', [ProductImageController::class, 'store'])->name('products.images.store');
    Route::patch('/product-images/{image}/primary', [ProductImageController::class, 'setPrimary'])->name('product-images.primary');
    Route::delete('/product-images/{image}', [ProductImageController::class, 'destroy'])->name('product-images.destroy');

    Route::post('/products/{product}/variants', [ProductVariantController::class, 'store'])->name('products.variants.store');
    Route::patch('/product-variants/{variant}', [ProductVariantController::class, 'update'])->name('product-variants.update');
    Route::delete('/product-variants/{variant}', [ProductVariantController::class, 'destroy'])->name('product-variants.destroy');

    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
    Route::patch('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Three structurally identical lookup tables, one shared controller —
    // see AttributeController's own docblock. Still 3 distinct URL
    // prefixes as required, not a generic/open wildcard.
    foreach (['metal-types', 'gemstones', 'occasions'] as $attrType) {
        Route::get("/{$attrType}", [AttributeController::class, 'index'])->name("{$attrType}.index")->defaults('type', $attrType);
        Route::get("/{$attrType}/create", [AttributeController::class, 'create'])->name("{$attrType}.create")->defaults('type', $attrType);
        Route::post("/{$attrType}", [AttributeController::class, 'store'])->name("{$attrType}.store")->defaults('type', $attrType);
        Route::get("/{$attrType}/{attribute}/edit", [AttributeController::class, 'edit'])->name("{$attrType}.edit")->defaults('type', $attrType);
        Route::patch("/{$attrType}/{attribute}", [AttributeController::class, 'update'])->name("{$attrType}.update")->defaults('type', $attrType);
        Route::delete("/{$attrType}/{attribute}", [AttributeController::class, 'destroy'])->name("{$attrType}.destroy")->defaults('type', $attrType);
    }

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

    Route::get('/customers', [AdminCustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{user}', [AdminCustomerController::class, 'show'])->name('customers.show');
    Route::patch('/customers/{user}/toggle-active', [AdminCustomerController::class, 'toggleActive'])->name('customers.toggle-active');

    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::patch('/reviews/{review}/approve', [AdminReviewController::class, 'approve'])->name('reviews.approve');
    Route::patch('/reviews/{review}/reject', [AdminReviewController::class, 'reject'])->name('reviews.reject');
    Route::delete('/reviews/{review}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');

    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/create', [AdminCouponController::class, 'create'])->name('coupons.create');
    Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::get('/coupons/{coupon}/edit', [AdminCouponController::class, 'edit'])->name('coupons.edit');
    Route::patch('/coupons/{coupon}', [AdminCouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');
});
