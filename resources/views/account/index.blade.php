@extends('layouts.app')

@section('title', 'My Account | AURELIA')

@section('content')

<section class="page-head">
  <div class="container">
    <p class="eyebrow">Client Care</p>
    <h1>My Account</h1>
    <div class="rule"></div>
    <p class="breadcrumb-lux"><a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; Account</p>
  </div>
</section>

<section class="section">
  <div class="container">
    @if (session('success'))
      <div class="mb-4" style="font-size:.85rem;color:var(--gold)">{{ session('success') }}</div>
    @endif
    <div class="row g-5">
      <div class="col-lg-4">
        @include('partials.account-nav')
      </div>
      <div class="col-lg-8">
        <p class="eyebrow mb-2">Welcome</p>
        <h2 class="display-6 mt-0">{{ $user->name }}</h2>
        <div class="rule rule-start"></div>
        <p class="text-muted-lux">{{ $user->email }}@if($user->phone) &middot; {{ $user->phone }}@endif</p>
        <p class="text-muted-lux mt-4">From here you can update your profile details, change your password, and manage the addresses used for delivery.</p>
        <div class="d-flex flex-wrap gap-3 mt-4">
          <a href="{{ route('account.profile.edit') }}" class="btn btn-ghost">Edit profile</a>
          <a href="{{ route('account.addresses.index') }}" class="btn btn-ghost">Manage addresses</a>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
