@extends('layouts.app')

@section('title', 'My Orders | AURELIA')

@section('content')

<section class="page-head">
  <div class="container">
    <p class="eyebrow">Client Care</p>
    <h1>My Account</h1>
    <div class="rule"></div>
    <p class="breadcrumb-lux"><a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; <a href="{{ route('account.index') }}">Account</a> &nbsp;/&nbsp; My Orders</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-4">
        @include('partials.account-nav')
      </div>
      <div class="col-lg-8">
        <p class="eyebrow mb-2">Order History</p>
        <h2 class="display-6 mt-0">My Orders</h2>
        <div class="rule rule-start"></div>

        @if ($orders->isEmpty())
          <p class="text-muted-lux mt-3">You haven't placed an order yet.</p>
          <a href="{{ route('shop') }}" class="btn btn-gold mt-2">Browse the collection</a>
        @else
          <div class="d-flex flex-column gap-3 mt-3">
            @foreach($orders as $order)
              <a href="{{ route('account.orders.show', $order) }}" class="card-lux p-4 d-block text-decoration-none">
                <div class="row align-items-center g-2">
                  <div class="col-6 col-md-3"><span class="card-cat">Order</span><p class="mb-0" style="color:var(--text)">{{ $order->order_number }}</p></div>
                  <div class="col-6 col-md-3"><span class="card-cat">Date</span><p class="mb-0" style="color:var(--text)">{{ $order->created_at->format('M d, Y') }}</p></div>
                  <div class="col-6 col-md-2"><span class="card-cat">Items</span><p class="mb-0" style="color:var(--text)">{{ $order->items_count }}</p></div>
                  <div class="col-6 col-md-2"><span class="card-cat">Status</span><p class="mb-0 text-gold">{{ ucfirst($order->status) }}</p></div>
                  <div class="col-12 col-md-2 text-md-end"><span class="card-cat">Total</span><p class="mb-0 price">${{ number_format((float) $order->total) }}</p></div>
                </div>
              </a>
            @endforeach
          </div>
          <div class="mt-4">{{ $orders->links('pagination::bootstrap-5') }}</div>
        @endif
      </div>
    </div>
  </div>
</section>

@endsection
