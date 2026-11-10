@extends('layouts.app')

@section('title', 'Order '.$order->order_number.' | AURELIA')

@section('content')

<section class="page-head">
  <div class="container">
    <p class="eyebrow">Client Care</p>
    <h1>My Account</h1>
    <div class="rule"></div>
    <p class="breadcrumb-lux"><a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; <a href="{{ route('account.index') }}">Account</a> &nbsp;/&nbsp; <a href="{{ route('account.orders.index') }}">My Orders</a> &nbsp;/&nbsp; {{ $order->order_number }}</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-4">
        @include('partials.account-nav')
      </div>
      <div class="col-lg-8">
        <div class="summary-box mb-4">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
              <p class="eyebrow mb-1">Order {{ $order->order_number }}</p>
              <p class="text-muted-lux mb-0" style="font-size:.85rem">Placed {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
            </div>
            <span class="text-gold" style="font-size:.9rem">{{ ucfirst($order->status) }}</span>
          </div>
        </div>

        <div class="summary-box mb-4">
          <p class="eyebrow mb-3">Order Items</p>
          @foreach($order->items as $item)
            <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid rgba(255,255,255,.06)">
              <div class="flex-grow-1">
                @if($item->product_options)
                  <span class="card-cat">{{ collect($item->product_options)->values()->first() }}</span>
                @endif
                <h6 class="mb-0">{{ $item->product_name }}</h6>
                @if($item->sku)<span class="text-muted-lux" style="font-size:.76rem">SKU: {{ $item->sku }}</span><br>@endif
                <span class="text-muted-lux" style="font-size:.8rem">Qty {{ $item->quantity }} &times; ${{ number_format((float) $item->unit_price) }}</span>
              </div>
              <div class="price">${{ number_format((float) $item->subtotal) }}</div>
            </div>
          @endforeach
        </div>

        @if($order->shippingAddress)
          <div class="summary-box mb-4">
            <p class="eyebrow mb-3">Delivery Address</p>
            <p class="text-muted-lux mb-0" style="font-size:.88rem">
              {{ $order->shippingAddress->contact_name }}<br>
              {{ $order->shippingAddress->address_line_1 }}@if($order->shippingAddress->address_line_2), {{ $order->shippingAddress->address_line_2 }}@endif<br>
              {{ $order->shippingAddress->city }}@if($order->shippingAddress->state), {{ $order->shippingAddress->state }}@endif @if($order->shippingAddress->postal_code) {{ $order->shippingAddress->postal_code }}@endif<br>
              {{ $order->shippingAddress->country }}<br>
              {{ $order->phone }}
            </p>
          </div>
        @endif

        @if($order->notes)
          <div class="summary-box mb-4">
            <p class="eyebrow mb-2">Order Notes</p>
            <p class="text-muted-lux mb-0" style="font-size:.85rem">{{ $order->notes }}</p>
          </div>
        @endif

        <div class="summary-box">
          <p class="eyebrow mb-3">Order Summary</p>
          <div class="summary-row"><span>Subtotal</span><span>${{ number_format((float) $order->subtotal) }}</span></div>
          <div class="summary-row"><span>Shipping</span><span class="text-gold">{{ (float) $order->shipping_fee > 0 ? '$'.number_format((float) $order->shipping_fee) : 'Complimentary' }}</span></div>
          <div class="summary-row"><span>Tax</span><span>${{ number_format((float) $order->tax) }}</span></div>
          @if((float) $order->discount > 0)
            <div class="summary-row"><span>Discount</span><span>-${{ number_format((float) $order->discount) }}</span></div>
          @endif
          <hr style="border-color:var(--line)">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="eyebrow">Total</span>
            <span class="summary-total">${{ number_format((float) $order->total) }}</span>
          </div>
          <p class="text-muted-lux mb-0" style="font-size:.85rem"><i class="bi bi-cash-coin text-gold me-2"></i>Cash on Delivery</p>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
