@extends('layouts.app')

@section('title', 'Order Confirmed | AURELIA')
@section('meta_description', 'Your AURELIA order has been placed.')

@section('content')

<section class="page-head">
  <div class="container">
    <p class="eyebrow">Thank You</p>
    <h1>Order Confirmed</h1>
    <div class="rule"></div>
    <p class="breadcrumb-lux"><a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; Order Confirmed</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-8">
        <div class="summary-box mb-4">
          <i class="bi bi-gem text-gold" style="font-size:1.8rem"></i>
          <h2 class="display-6 mt-3 mb-1">Thank you, {{ $order->contact_name }}.</h2>
          <p class="text-muted-lux">Your order has been received and is <strong class="text-gold">{{ ucfirst($order->status) }}</strong>. We'll be in touch shortly to confirm delivery.</p>
          <div class="row g-4 mt-2">
            <div class="col-6 col-md-4"><span class="card-cat">Order Number</span><p class="mb-0" style="color:var(--text)">{{ $order->order_number }}</p></div>
            <div class="col-6 col-md-4"><span class="card-cat">Payment</span><p class="mb-0" style="color:var(--text)">Cash on Delivery</p></div>
            <div class="col-6 col-md-4"><span class="card-cat">Total</span><p class="mb-0 text-gold">${{ number_format((float) $order->total) }}</p></div>
          </div>
        </div>

        <div class="summary-box mb-4">
          <p class="eyebrow mb-3">Order Items</p>
          @foreach($order->items as $item)
            <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid rgba(255,255,255,.06)">
              <div class="flex-grow-1">
                <span class="card-cat">{{ $item->product_options['ring_size'] ?? $item->product_options['chain_length_in'] ?? $item->product_options['bracelet_size'] ?? '' }}</span>
                <h6 class="mb-0">{{ $item->product_name }}</h6>
                <span class="text-muted-lux" style="font-size:.8rem">Qty {{ $item->quantity }} &times; ${{ number_format((float) $item->unit_price) }}</span>
              </div>
              <div class="price">${{ number_format((float) $item->subtotal) }}</div>
            </div>
          @endforeach
        </div>

        @if($order->shippingAddress)
          <div class="summary-box">
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
      </div>

      <div class="col-lg-4">
        <div class="summary-box" data-aos="fade-left">
          <p class="eyebrow mb-3">Order Summary</p>
          <div class="summary-row"><span>Subtotal</span><span>${{ number_format((float) $order->subtotal) }}</span></div>
          <div class="summary-row"><span>Shipping</span><span class="text-gold">Complimentary</span></div>
          <div class="summary-row"><span>Estimated tax</span><span>${{ number_format((float) $order->tax) }}</span></div>
          <hr style="border-color:var(--line)">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <span class="eyebrow">Total</span>
            <span class="summary-total">${{ number_format((float) $order->total) }}</span>
          </div>
          <a href="{{ route('shop') }}" class="btn btn-gold w-100 mb-2">Continue Shopping</a>
          <a href="{{ route('account.index') }}" class="btn btn-ghost w-100">My Account</a>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
