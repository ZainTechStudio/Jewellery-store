@extends('layouts.app')

@section('title', ($address->exists ? 'Edit Address' : 'Add Address').' | AURELIA')

@section('content')

<section class="page-head">
  <div class="container">
    <p class="eyebrow">Client Care</p>
    <h1>My Account</h1>
    <div class="rule"></div>
    <p class="breadcrumb-lux"><a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; <a href="{{ route('account.index') }}">Account</a> &nbsp;/&nbsp; <a href="{{ route('account.addresses.index') }}">Addresses</a> &nbsp;/&nbsp; {{ $address->exists ? 'Edit' : 'Add' }}</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-4">
        @include('partials.account-nav')
      </div>
      <div class="col-lg-8">
        <p class="eyebrow mb-2">Delivery</p>
        <h2 class="display-6 mt-0">{{ $address->exists ? 'Edit Address' : 'Add Address' }}</h2>
        <div class="rule rule-start"></div>

        @if ($errors->any())
          <div class="mb-3" style="font-size:.82rem;color:#e2a5a5">
            @foreach ($errors->all() as $error)<p class="mb-1">{{ $error }}</p>@endforeach
          </div>
        @endif

        <form method="POST" action="{{ $address->exists ? route('account.addresses.update', $address) : route('account.addresses.store') }}" class="row g-3 mt-2">
          @csrf
          @if($address->exists) @method('PATCH') @endif
          @if(request('redirect')) <input type="hidden" name="redirect" value="{{ request('redirect') }}"> @endif

          <div class="col-md-6"><input type="text" name="type" value="{{ old('type', $address->type) }}" class="form-control form-control-lux" placeholder="Label (e.g. Home, Work)"></div>
          <div class="col-md-6"><input type="text" name="contact_name" value="{{ old('contact_name', $address->contact_name) }}" required class="form-control form-control-lux" placeholder="Contact name"></div>
          <div class="col-md-6"><input type="tel" name="phone" value="{{ old('phone', $address->phone) }}" required class="form-control form-control-lux" placeholder="Phone"></div>
          <div class="col-md-6"><input type="text" name="country" value="{{ old('country', $address->country) }}" required class="form-control form-control-lux" placeholder="Country"></div>
          <div class="col-12"><input type="text" name="address_line_1" value="{{ old('address_line_1', $address->address_line_1) }}" required class="form-control form-control-lux" placeholder="Address line 1"></div>
          <div class="col-12"><input type="text" name="address_line_2" value="{{ old('address_line_2', $address->address_line_2) }}" class="form-control form-control-lux" placeholder="Address line 2 (optional)"></div>
          <div class="col-md-4"><input type="text" name="city" value="{{ old('city', $address->city) }}" required class="form-control form-control-lux" placeholder="City"></div>
          <div class="col-md-4"><input type="text" name="state" value="{{ old('state', $address->state) }}" class="form-control form-control-lux" placeholder="State / Province"></div>
          <div class="col-md-4"><input type="text" name="postal_code" value="{{ old('postal_code', $address->postal_code) }}" class="form-control form-control-lux" placeholder="Postal code"></div>

          <div class="col-12 d-flex align-items-center gap-2 mt-2">
            <input type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default', $address->is_default) ? 'checked' : '' }} style="width:16px;height:16px">
            <label for="is_default" class="text-muted-lux mb-0" style="font-size:.85rem">Set as default address</label>
          </div>

          <div class="col-12"><button class="btn btn-gold mt-2" type="submit">{{ $address->exists ? 'Save Changes' : 'Add Address' }}</button></div>
        </form>
      </div>
    </div>
  </div>
</section>

@endsection
