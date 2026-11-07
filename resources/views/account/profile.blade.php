@extends('layouts.app')

@section('title', 'Edit Profile | AURELIA')

@section('content')

<section class="page-head">
  <div class="container">
    <p class="eyebrow">Client Care</p>
    <h1>My Account</h1>
    <div class="rule"></div>
    <p class="breadcrumb-lux"><a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; <a href="{{ route('account.index') }}">Account</a> &nbsp;/&nbsp; Profile</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-4">
        @include('partials.account-nav')
      </div>
      <div class="col-lg-8">
        <p class="eyebrow mb-2">Profile</p>
        <h2 class="display-6 mt-0">Your Details</h2>
        <div class="rule rule-start"></div>

        @if (session('success'))
          <div class="mb-3" style="font-size:.85rem;color:var(--gold)">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
          <div class="mb-3" style="font-size:.82rem;color:#e2a5a5">
            @foreach ($errors->all() as $error)<p class="mb-1">{{ $error }}</p>@endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('account.profile.update') }}" class="row g-3 mt-2">
          @csrf
          @method('PATCH')
          <div class="col-md-6"><input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-control form-control-lux" placeholder="Full name"></div>
          <div class="col-md-6"><input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control form-control-lux" placeholder="Phone"></div>
          <div class="col-12"><input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control form-control-lux" placeholder="Email address"></div>
          <div class="col-12"><button class="btn btn-gold mt-2" type="submit">Save Changes</button></div>
        </form>
      </div>
    </div>
  </div>
</section>

@endsection
