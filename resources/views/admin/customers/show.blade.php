@extends('layouts.admin')

@section('title', $customer->name)

@section('content')

<div class="row g-4">
  <div class="col-lg-4">
    <div class="admin-card">
      <h2 class="h6 text-gold mb-3">{{ $customer->name }}</h2>
      <p class="text-muted-lux mb-1" style="font-size:.85rem">{{ $customer->email }}</p>
      <p class="text-muted-lux mb-1" style="font-size:.85rem">{{ $customer->phone ?? 'No phone on file' }}</p>
      <p class="text-muted-lux mb-3" style="font-size:.85rem">Joined {{ $customer->created_at->format('M d, Y') }} &middot; Role: {{ ucfirst($customer->role) }}</p>
      <form method="POST" action="{{ route('admin.customers.toggle-active', $customer) }}">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-ghost btn-sm">{{ $customer->is_active ? 'Deactivate Account' : 'Activate Account' }}</button>
      </form>
    </div>

    @if($addresses->isNotEmpty())
      <div class="admin-card mt-4">
        <h2 class="h6 text-gold mb-3">Addresses</h2>
        @foreach($addresses as $address)
          <p class="text-muted-lux mb-2" style="font-size:.82rem">
            {{ $address->contact_name }}@if($address->is_default) <span class="text-gold">(default)</span>@endif<br>
            {{ $address->address_line_1 }}, {{ $address->city }}, {{ $address->country }}
          </p>
        @endforeach
      </div>
    @endif
  </div>

  <div class="col-lg-8">
    <div class="admin-card">
      <h2 class="h6 text-gold mb-3">Recent Orders</h2>
      @if($orders->isEmpty())
        <p class="text-muted-lux mb-0" style="font-size:.85rem">No orders yet.</p>
      @else
        <div style="overflow-x:auto">
          <table class="admin-table">
            <thead><tr><th>Order</th><th>Status</th><th>Total</th><th>Date</th><th></th></tr></thead>
            <tbody>
              @foreach($orders as $order)
                <tr>
                  <td>{{ $order->order_number }}</td>
                  <td><span class="admin-badge">{{ ucfirst($order->status) }}</span></td>
                  <td>${{ number_format((float) $order->total) }}</td>
                  <td>{{ $order->created_at->format('M d, Y') }}</td>
                  <td><a href="{{ route('admin.orders.show', $order) }}" class="text-gold" style="font-size:.78rem">View</a></td>
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
