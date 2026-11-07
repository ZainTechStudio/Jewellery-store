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
    <div class="row g-5">
      <div class="col-lg-8">
        <div id="cartList"></div>
        <div id="cartEmpty" class="text-center py-5 d-none">
          <i class="bi bi-bag text-gold" style="font-size:2.4rem"></i>
          <h3 class="mt-3">Your bag is empty</h3>
          <p class="text-muted-lux">Discover a piece worth keeping for a lifetime.</p>
          <a href="{{ route('shop') }}" class="btn btn-gold mt-2">Browse the collection</a>
        </div>
        <div class="mt-4 d-flex flex-wrap gap-3" id="cartActions">
          <a href="{{ route('shop') }}" class="btn btn-ghost">Continue shopping</a>
          <button class="btn btn-ghost" id="clearBtn">Empty bag</button>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="summary-box" data-aos="fade-left">
          <p class="eyebrow mb-3">Order Summary</p>
          <div class="summary-row"><span>Subtotal</span><span id="sub">$0</span></div>
          <div class="summary-row"><span>Shipping</span><span class="text-gold">Complimentary</span></div>
          <div class="summary-row"><span>Estimated tax</span><span id="tax">$0</span></div>
          <hr style="border-color:var(--line)">
          <div class="d-flex justify-content-between align-items-center">
            <span class="eyebrow">Total</span>
            <span class="summary-total" id="total">$0</span>
          </div>
          <form class="mt-4 d-flex gap-2" data-fake="Promotional code applied">
            @csrf
            <input class="form-control form-control-lux" placeholder="Promo code">
            <button class="btn btn-ghost px-3" type="submit">Apply</button>
          </form>
          <button class="btn btn-gold w-100 mt-3" id="checkout">Secure checkout</button>
          <p class="text-muted-lux text-center mt-3 mb-0" style="font-size:.72rem;letter-spacing:.18em">
            <i class="bi bi-shield-lock text-gold me-1"></i>ENCRYPTED PAYMENT
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
