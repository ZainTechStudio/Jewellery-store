@extends('layouts.admin')

@section('title', 'Orders')

@section('content')

<form method="GET" action="{{ route('admin.orders.index') }}" class="d-flex gap-2 flex-wrap admin-form mb-3">
  <input type="text" name="q" value="{{ $filters['q'] }}" class="form-control form-control-sm" placeholder="Order #, customer, phone" style="width:240px">
  <select name="status" class="form-select form-select-sm" style="width:170px" onchange="this.form.submit()">
    <option value="">All status</option>
    @foreach($statuses as $s)
      <option value="{{ $s }}" {{ $filters['status'] === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
    @endforeach
  </select>
  <button class="btn btn-ghost btn-sm" type="submit">Filter</button>
</form>

<div class="admin-card">
  <div style="overflow-x:auto">
    <table class="admin-table">
      <thead><tr><th>Order</th><th>Customer</th><th>Phone</th><th>Status</th><th>Total</th><th>Date</th><th></th></tr></thead>
      <tbody>
        @forelse($orders as $order)
          <tr>
            <td>{{ $order->order_number }}</td>
            <td>{{ $order->contact_name }}</td>
            <td>{{ $order->phone }}</td>
            <td><span class="admin-badge">{{ ucfirst($order->status) }}</span></td>
            <td>${{ number_format((float) $order->total) }}</td>
            <td>{{ $order->created_at->format('M d, Y') }}</td>
            <td><a href="{{ route('admin.orders.show', $order) }}" class="text-gold" style="font-size:.78rem">View</a></td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-muted-lux text-center py-4">No orders found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-3">{{ $orders->links('pagination::bootstrap-5') }}</div>

@endsection
