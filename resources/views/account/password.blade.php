@extends('layouts.app')

@section('title', 'Change Password | AURELIA')

@section('content')

<section class="page-head">
  <div class="container">
    <p class="eyebrow">Client Care</p>
    <h1>My Account</h1>
    <div class="rule"></div>
    <p class="breadcrumb-lux"><a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; <a href="{{ route('account.index') }}">Account</a> &nbsp;/&nbsp; Password</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-4">
        @include('partials.account-nav')
      </div>
      <div class="col-lg-8">
        <p class="eyebrow mb-2">Security</p>
        <h2 class="display-6 mt-0">Change Password</h2>
        <div class="rule rule-start"></div>

        @if (session('success'))
          <div class="mb-3" style="font-size:.85rem;color:var(--gold)">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
          <div class="mb-3" style="font-size:.82rem;color:#e2a5a5">
            @foreach ($errors->all() as $error)<p class="mb-1">{{ $error }}</p>@endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('account.password.update') }}" class="row g-3 mt-2">
          @csrf
          @method('PATCH')
          <div class="col-12"><input type="password" name="current_password" required class="form-control form-control-lux" placeholder="Current password"></div>
          <div class="col-md-6"><input type="password" name="password" required class="form-control form-control-lux" placeholder="New password"></div>
          <div class="col-md-6"><input type="password" name="password_confirmation" required class="form-control form-control-lux" placeholder="Confirm new password"></div>
          <div class="col-12"><button class="btn btn-gold mt-2" type="submit">Update Password</button></div>
        </form>
      </div>
    </div>
  </div>
</section>

@endsection
