@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3"><div class="admin-stat"><div class="admin-stat-value">{{ $stats['total_products'] }}</div><span class="admin-stat-label">Total Products</span></div></div>
  <div class="col-6 col-lg-3"><div class="admin-stat"><div class="admin-stat-value">{{ $stats['active_products'] }}</div><span class="admin-stat-label">Active Products</span></div></div>
  <div class="col-6 col-lg-3"><div class="admin-stat"><div class="admin-stat-value">{{ $stats['low_stock_products'] }}</div><span class="admin-stat-label">Low Stock</span></div></div>
  <div class="col-6 col-lg-3"><div class="admin-stat"><div class="admin-stat-value">{{ $stats['total_customers'] }}</div><span class="admin-stat-label">Customers</span></div></div>
  <div class="col-6 col-lg-3"><div class="admin-stat"><div class="admin-stat-value">{{ $stats['total_orders'] }}</div><span class="admin-stat-label">Total Orders</span></div></div>
  <div class="col-6 col-lg-3"><div class="admin-stat"><div class="admin-stat-value">{{ $stats['pending_orders'] }}</div><span class="admin-stat-label">Pending Orders</span></div></div>
  <div class="col-6 col-lg-3"><div class="admin-stat"><div class="admin-stat-value">{{ $stats['processing_orders'] }}</div><span class="admin-stat-label">Confirmed/Processing</span></div></div>
</div>

<div class="row g-4">
  <div class="col-lg-7">
    <div class="admin-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h6 mb-0 text-gold">Recent Orders</h2>
        <a href="{{ route('admin.orders.index') }}" class="admin-topbar-link">View all</a>
      </div>
      @if($recentOrders->isEmpty())
        <p class="text-muted-lux mb-0" style="font-size:.85rem">No orders yet.</p>
      @else
        <div style="overflow-x:auto">
          <table class="admin-table">
            <thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Total</th><th>Date</th></tr></thead>
            <tbody>
              @foreach($recentOrders as $order)
                <tr>
                  <td><a href="{{ route('admin.orders.show', $order) }}" class="text-gold">{{ $order->order_number }}</a></td>
                  <td>{{ $order->contact_name }}</td>
                  <td><span class="admin-badge">{{ ucfirst($order->status) }}</span></td>
                  <td>${{ number_format((float) $order->total) }}</td>
                  <td>{{ $order->created_at->format('M d') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>

  <div class="col-lg-5">
    <div class="admin-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h6 mb-0 text-gold">Low Stock</h2>
        <a href="{{ route('admin.products.index') }}" class="admin-topbar-link">View all</a>
      </div>
      @if($lowStockProducts->isEmpty())
        <p class="text-muted-lux mb-0" style="font-size:.85rem">Nothing low on stock.</p>
      @else
        <div style="overflow-x:auto">
          <table class="admin-table">
            <thead><tr><th>Product</th><th>SKU</th><th>Stock</th></tr></thead>
            <tbody>
              @foreach($lowStockProducts as $product)
                <tr>
                  <td><a href="{{ route('admin.products.edit', $product) }}" class="text-gold">{{ $product->name }}</a></td>
                  <td>{{ $product->sku }}</td>
                  <td>{{ $product->stock_quantity }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
</div>

@endsection
