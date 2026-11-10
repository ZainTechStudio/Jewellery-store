<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Low-stock threshold — a simple constant rather than a configurable
     * setting, since no such setting exists anywhere in this project yet.
     */
    private const LOW_STOCK_THRESHOLD = 5;

    public function index(): View
    {
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'low_stock_products' => Product::where('is_active', true)
                ->where('stock_quantity', '<=', self::LOW_STOCK_THRESHOLD)
                ->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'processing_orders' => Order::whereIn('status', ['confirmed', 'processing'])->count(),
            'total_customers' => User::where('role', 'customer')->count(),
        ];

        $recentOrders = Order::query()
            ->orderByDesc('created_at')
            ->take(8)
            ->get(['id', 'order_number', 'contact_name', 'status', 'total', 'created_at']);

        $lowStockProducts = Product::where('is_active', true)
            ->where('stock_quantity', '<=', self::LOW_STOCK_THRESHOLD)
            ->orderBy('stock_quantity')
            ->take(8)
            ->get(['id', 'name', 'slug', 'stock_quantity', 'sku']);

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }
}
