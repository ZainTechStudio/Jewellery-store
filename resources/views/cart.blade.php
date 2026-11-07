@extends('layouts.app')

@section('title', 'Shopping Bag — Review Your Jewellery | AURELIA')
@section('meta_description', 'Review the pieces in your AURELIA shopping bag, adjust quantities and proceed to secure checkout.')
@section('og_title', 'Shopping Bag | AURELIA')
@section('og_description', 'Review your selected fine jewellery pieces and checkout securely.')

@section('content')

<section class="page-head">
  <div class="container">
    <p class="eyebrow">Your Selection</p>
    <h1>Shopping Bag</h1>
    <div class="rule"></div>
    <p class="breadcrumb-lux"><a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; Cart</p>
  </div>
</section>

<section class="section">
  <div class="container">

    @if (session('success'))
      <div class="alert" style="background:transparent;border:1px solid var(--line);color:var(--gold);font-size:.85rem" role="alert">{{ session('success') }}</div>
    @elseif (session('error'))
      <div class="alert" style="background:transparent;border:1px solid var(--line);color:var(--text);font-size:.85rem" role="alert">{{ session('error') }}</div>
    @endif

    <div class="row g-5">
      <div class="col-lg-8">
        {{-- Phase 3: rows are now rendered server-side from the database cart.
             Quantity +/- and remove use fetch() against the Phase 3 cart routes
             (see public/js/cart.js) and re-render from the server's response —
             the database stays the source of truth; refreshing the page always
             shows the same state since it's the exact same server render. --}}
        <div id="cartList">
          @foreach($items as $row)
            <div class="cart-row" data-id="{{ $row['id'] }}">
              <img src="{{ $row['image'] }}" alt="{{ $row['name'] }}" loading="lazy">
              <div class="flex-grow-1">
                <span class="card-cat">{{ $row['variant_label'] ?? '' }}</span>
                <h5 class="mb-1"><a href="{{ $row['url'] }}">{{ $row['name'] }}</a></h5>
                <span class="price unit-price">{{ $row['formatted_unit_price'] }}</span>
              </div>
              <div class="qty-box"><button type="button" data-dec>&minus;</button>
                <input value="{{ $row['quantity'] }}" readonly><button type="button" data-inc>+</button></div>
              <div class="text-end" style="min-width:96px">
                <div class="price line-total">{{ $row['formatted_line_subtotal'] }}</div>
                <button class="icon-btn mt-2" data-remove><i class="bi bi-trash"></i></button>
              </div>
            </div>
          @endforeach
        </div>
        <div id="cartEmpty" class="text-center py-5 {{ $items->isNotEmpty() ? 'd-none' : '' }}">
          <i class="bi bi-bag text-gold" style="font-size:2.4rem"></i>
          <h3 class="mt-3">Your bag is empty</h3>
          <p class="text-muted-lux">Discover a piece worth keeping for a lifetime.</p>
          <a href="{{ route('shop') }}" class="btn btn-gold mt-2">Browse the collection</a>
        </div>
        <div class="mt-4 d-flex flex-wrap gap-3 {{ $items->isEmpty() ? 'd-none' : '' }}" id="cartActions">
          <a href="{{ route('shop') }}" class="btn btn-ghost">Continue shopping</a>
          <button class="btn btn-ghost" id="clearBtn">Empty bag</button>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="summary-box" data-aos="fade-left">
          <p class="eyebrow mb-3">Order Summary</p>
          <div class="summary-row"><span>Subtotal</span><span id="sub">{{ $formatted_subtotal }}</span></div>
          <div class="summary-row"><span>Shipping</span><span class="text-gold">Complimentary</span></div>
          <div class="summary-row"><span>Estimated tax</span><span id="tax">{{ $formatted_tax }}</span></div>
          <hr style="border-color:var(--line)">
          <div class="d-flex justify-content-between align-items-center">
            <span class="eyebrow">Total</span>
            <span class="summary-total" id="total">{{ $formatted_total }}</span>
          </div>
          <form class="mt-4 d-flex gap-2" data-fake="Promotional code applied">
            @csrf
            <input class="form-control form-control-lux" placeholder="Promo code">
            <button class="btn btn-ghost px-3" type="submit">Apply</button>
          </form>
          {{-- Phase 5: checkout is real now — links straight to it. If the bag is
               empty, CheckoutController::index() itself redirects back here with
               a message, so this never needs to be disabled/guarded in JS. --}}
          <a href="{{ route('checkout.index') }}" class="btn btn-gold w-100 mt-3 text-center" id="checkout">Secure checkout</a>
          <p class="text-muted-lux text-center mt-3 mb-0" style="font-size:.72rem;letter-spacing:.18em">
            <i class="bi bi-shield-check text-gold me-1"></i>PAY ON DELIVERY
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script src="{{ asset('js/cart.js') }}"></script>
@endpush
