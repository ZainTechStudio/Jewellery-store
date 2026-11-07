@extends('layouts.app')

@section('title', 'My Addresses | AURELIA')

@section('content')

<section class="page-head">
  <div class="container">
    <p class="eyebrow">Client Care</p>
    <h1>My Account</h1>
    <div class="rule"></div>
    <p class="breadcrumb-lux"><a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; <a href="{{ route('account.index') }}">Account</a> &nbsp;/&nbsp; Addresses</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-4">
        @include('partials.account-nav')
      </div>
      <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div>
            <p class="eyebrow mb-2">Delivery</p>
            <h2 class="display-6 mt-0 mb-0">Addresses</h2>
          </div>
          <a href="{{ route('account.addresses.create') }}" class="btn btn-gold">Add address</a>
        </div>
        <div class="rule rule-start"></div>

        @if (session('success'))
          <div class="mb-3" style="font-size:.85rem;color:var(--gold)">{{ session('success') }}</div>
        @endif

        @if ($addresses->isEmpty())
          <p class="text-muted-lux mt-3">You haven't saved an address yet.</p>
        @else
          <div class="row g-4 mt-1">
            @foreach($addresses as $address)
              <div class="col-md-6">
                <div class="card-lux p-4 h-100">
                  @if($address->is_default)<span class="card-cat text-gold">Default</span>@endif
                  <h5 class="mt-1 mb-1">{{ $address->contact_name }}</h5>
                  <p class="text-muted-lux mb-1" style="font-size:.85rem">
                    {{ $address->address_line_1 }}@if($address->address_line_2), {{ $address->address_line_2 }}@endif<br>
                    {{ $address->city }}@if($address->state), {{ $address->state }}@endif @if($address->postal_code) {{ $address->postal_code }}@endif<br>
                    {{ $address->country }}<br>
                    {{ $address->phone }}
                  </p>
                  <div class="d-flex gap-3 mt-3">
                    <a href="{{ route('account.addresses.edit', $address) }}" class="text-gold" style="font-size:.8rem">Edit</a>
                    <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" onsubmit="return confirm('Remove this address?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-muted-lux p-0 border-0 bg-transparent" style="font-size:.8rem">Remove</button>
                    </form>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>
</section>

@endsection
