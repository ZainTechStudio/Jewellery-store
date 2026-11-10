@extends('layouts.admin')

@section('title', 'Order '.$order->order_number)

@section('content')

<div class="row g-4">
  <div class="col-lg-8">
    <div class="admin-card mb-4">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
          <h2 class="h5 text-gold mb-1">{{ $order->order_number }}</h2>
          <p class="text-muted-lux mb-0" style="font-size:.85rem">Placed {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
        </div>
        <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="d-flex gap-2 admin-form">
          @csrf @method('PATCH')
          <select name="status" class="form-select form-select-sm">
            @foreach($statuses as $s)
              <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
          </select>
          <button class="btn btn-gold btn-sm" type="submit">Update Status</button>
        </form>
      </div>
    </div>

    <div class="admin-card mb-4">
      <h2 class="h6 text-gold mb-3">Order Items</h2>
      <div style="overflow-x:auto">
        <table class="admin-table">
          <thead><tr><th>Product</th><th>SKU</th><th>Options</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr></thead>
          <tbody>
            @foreach($order->items as $item)
              <tr>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->sku ?? '—' }}</td>
                <td>{{ $item->product_options ? collect($item->product_options)->map(fn($v,$k) => "$k: $v")->implode(', ') : '—' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format((float) $item->unit_price) }}</td>
                <td>${{ number_format((float) $item->subtotal) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    @if($order->notes)
      <div class="admin-card">
        <h2 class="h6 text-gold mb-2">Notes</h2>
        <p class="text-muted-lux mb-0" style="font-size:.85rem">{{ $order->notes }}</p>
      </div>
    @endif
  </div>

  <div class="col-lg-4">
    <div class="admin-card mb-4">
      <h2 class="h6 text-gold mb-3">Customer</h2>
      <p class="mb-1" style="font-size:.85rem">{{ $order->contact_name }}</p>
      <p class="text-muted-lux mb-1" style="font-size:.85rem">{{ $order->email }}</p>
      <p class="text-muted-lux mb-0" style="font-size:.85rem">{{ $order->phone }}</p>
      @if($order->user)
        <a href="{{ route('admin.customers.show', $order->user) }}" class="text-gold d-inline-block mt-2" style="font-size:.78rem">View customer account</a>
      @endif
    </div>

    @if($order->shippingAddress)
      <div class="admin-card mb-4">
        <h2 class="h6 text-gold mb-3">Shipping Address</h2>
        <p class="text-muted-lux mb-0" style="font-size:.85rem">
          {{ $order->shippingAddress->address_line_1 }}@if($order->shippingAddress->address_line_2), {{ $order->shippingAddress->address_line_2 }}@endif<br>
          {{ $order->shippingAddress->city }}@if($order->shippingAddress->state), {{ $order->shippingAddress->state }}@endif @if($order->shippingAddress->postal_code) {{ $order->shippingAddress->postal_code }}@endif<br>
          {{ $order->shippingAddress->country }}
        </p>
      </div>
    @endif

    <div class="admin-card">
      <h2 class="h6 text-gold mb-3">Totals</h2>
      <div class="summary-row"><span>Subtotal</span><span>${{ number_format((float) $order->subtotal) }}</span></div>
      <div class="summary-row"><span>Shipping</span><span>${{ number_format((float) $order->shipping_fee) }}</span></div>
      <div class="summary-row"><span>Tax</span><span>${{ number_format((float) $order->tax) }}</span></div>
      @if((float) $order->discount > 0)<div class="summary-row"><span>Discount</span><span>-${{ number_format((float) $order->discount) }}</span></div>@endif
      <hr style="border-color:var(--line)">
      <div class="d-flex justify-content-between"><strong>Total</strong><strong class="text-gold">${{ number_format((float) $order->total) }}</strong></div>
      <p class="text-muted-lux mt-3 mb-0" style="font-size:.8rem">Payment: Cash on Delivery</p>
    </div>
  </div>
</div>

@endsection
