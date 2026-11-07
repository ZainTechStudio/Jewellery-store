@extends('layouts.app')

@section('title', 'Checkout | AURELIA')
@section('meta_description', 'Review your order and complete checkout.')

@section('content')

<section class="page-head">
  <div class="container">
    <p class="eyebrow">Your Order</p>
    <h1>Checkout</h1>
    <div class="rule"></div>
    <p class="breadcrumb-lux"><a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; <a href="{{ route('cart.index') }}">Cart</a> &nbsp;/&nbsp; Checkout</p>
  </div>
</section>

<section class="section">
  <div class="container">

    @if (session('error'))
      <div class="mb-4" style="font-size:.85rem;color:#e2a5a5">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
      <div class="mb-4" style="font-size:.85rem;color:#e2a5a5">
        @foreach ($errors->all() as $error)<p class="mb-1">{{ $error }}</p>@endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('checkout.store') }}" id="checkoutForm">
      @csrf
      <div class="row g-5">
        <div class="col-lg-8">

          <div class="summary-box mb-4">
            <p class="eyebrow mb-3">Contact Information</p>
            <p class="text-muted-lux mb-1" style="font-size:.9rem">{{ $user->name }}</p>
            <p class="text-muted-lux mb-3" style="font-size:.9rem">{{ $user->email }}</p>
            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required class="form-control form-control-lux" placeholder="Phone number (for delivery contact)" style="max-width:320px">
          </div>

          <div class="summary-box mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
              <p class="eyebrow mb-0">Delivery Address</p>
              <a href="{{ route('account.addresses.create', ['redirect' => 'checkout']) }}" class="text-gold" style="font-size:.78rem">+ Add new address</a>
            </div>

            @if ($addresses->isEmpty())
              <p class="text-muted-lux" style="font-size:.9rem">You don't have a saved address yet. Please add one to continue.</p>
            @else
              <div class="d-flex flex-column gap-3">
                @foreach($addresses as $address)
                  <label class="d-flex gap-3 align-items-start" style="cursor:pointer">
                    <input type="radio" name="shipping_address_id" value="{{ $address->id }}" {{ (old('shipping_address_id', $addresses->firstWhere('is_default', true)?->id ?? $addresses->first()->id) == $address->id) ? 'checked' : '' }} required style="margin-top:.3rem">
                    <span class="text-muted-lux" style="font-size:.85rem">
                      <strong class="text-text" style="color:var(--text)">{{ $address->contact_name }}</strong>
                      @if($address->is_default) <span class="text-gold">&middot; Default</span>@endif<br>
                      {{ $address->address_line_1 }}@if($address->address_line_2), {{ $address->address_line_2 }}@endif,
                      {{ $address->city }}@if($address->state), {{ $address->state }}@endif @if($address->postal_code) {{ $address->postal_code }}@endif,
                      {{ $address->country }}<br>
                      {{ $address->phone }}
                    </span>
                  </label>
                @endforeach
              </div>
            @endif
          </div>

          <div class="summary-box mb-4">
            <p class="eyebrow mb-3">Order Items</p>
            @foreach($cart['items'] as $row)
              <div class="d-flex align-items-center gap-3 py-2" style="border-bottom:1px solid rgba(255,255,255,.06)">
                <img src="{{ $row['image'] }}" alt="{{ $row['name'] }}" style="width:64px;height:64px;object-fit:cover;border:1px solid rgba(255,255,255,.07)">
                <div class="flex-grow-1">
                  <span class="card-cat">{{ $row['variant_label'] ?? '' }}</span>
                  <h6 class="mb-0">{{ $row['name'] }}</h6>
                  <span class="text-muted-lux" style="font-size:.8rem">Qty {{ $row['quantity'] }} &times; {{ $row['formatted_unit_price'] }}</span>
                </div>
                <div class="price">{{ $row['formatted_line_subtotal'] }}</div>
              </div>
            @endforeach
          </div>

          <div class="summary-box">
            <p class="eyebrow mb-3">Order Notes (optional)</p>
            <textarea name="notes" rows="3" class="form-control form-control-lux" placeholder="Delivery instructions, gift note, etc.">{{ old('notes') }}</textarea>
          </div>

        </div>

        <div class="col-lg-4">
          <div class="summary-box" data-aos="fade-left">
            <p class="eyebrow mb-3">Order Summary</p>
            <div class="summary-row"><span>Subtotal</span><span>{{ $cart['formatted_subtotal'] }}</span></div>
            <div class="summary-row"><span>Shipping</span><span class="text-gold">Complimentary</span></div>
            <div class="summary-row"><span>Estimated tax</span><span>{{ $cart['formatted_tax'] }}</span></div>
            <hr style="border-color:var(--line)">
            <div class="d-flex justify-content-between align-items-center">
              <span class="eyebrow">Total</span>
              <span class="summary-total">{{ $cart['formatted_total'] }}</span>
            </div>

            <div class="mt-4">
              <p class="eyebrow mb-2">Payment Method</p>
              <p class="text-muted-lux" style="font-size:.85rem"><i class="bi bi-cash-coin text-gold me-2"></i>Cash on Delivery</p>
            </div>

            <button class="btn btn-gold w-100 mt-3" type="submit" id="placeOrderBtn" {{ $addresses->isEmpty() ? 'disabled' : '' }}>Place Order</button>
            <p class="text-muted-lux text-center mt-3 mb-0" style="font-size:.72rem;letter-spacing:.18em">
              <i class="bi bi-shield-check text-gold me-1"></i>PAY ON DELIVERY
            </p>
          </div>
        </div>
      </div>
    </form>

  </div>
</section>

@endsection

@push('scripts')
<script>
  // Obvious-case duplicate-submit guard: disable the button the moment
  // the form is submitted so an impatient double-click can't fire a
  // second request. The server also protects against a true race (see
  // CheckoutController::store()).
  document.getElementById('checkoutForm').addEventListener('submit', function () {
    var btn = document.getElementById('placeOrderBtn');
    btn.disabled = true;
    btn.textContent = 'Placing Order…';
  });
</script>
@endpush
