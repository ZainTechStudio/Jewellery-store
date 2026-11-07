@extends('layouts.app')

@section('title', 'Create an Account | AURELIA')
@section('meta_description', 'Create your AURELIA account.')

@section('content')

<section class="page-head">
  <div class="container">
    <p class="eyebrow">Client Access</p>
    <h1>Create an Account</h1>
    <div class="rule"></div>
    <p class="breadcrumb-lux"><a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; Register</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <div class="summary-box" data-aos="fade-up">
          <p class="eyebrow mb-3">Join Aurelia</p>

          @if ($errors->any())
            <div class="mb-3" style="font-size:.82rem;color:#e2a5a5">
              @foreach ($errors->all() as $error)
                <p class="mb-1">{{ $error }}</p>
              @endforeach
            </div>
          @endif

          <form method="POST" action="{{ route('register.store') }}" class="row g-3">
            @csrf
            <div class="col-md-6"><input type="text" name="name" value="{{ old('name') }}" required autofocus class="form-control form-control-lux" placeholder="Full name"></div>
            <div class="col-md-6"><input type="tel" name="phone" value="{{ old('phone') }}" class="form-control form-control-lux" placeholder="Phone (optional)"></div>
            <div class="col-12"><input type="email" name="email" value="{{ old('email') }}" required class="form-control form-control-lux" placeholder="Email address"></div>
            <div class="col-md-6"><input type="password" name="password" required class="form-control form-control-lux" placeholder="Password"></div>
            <div class="col-md-6"><input type="password" name="password_confirmation" required class="form-control form-control-lux" placeholder="Confirm password"></div>
            <div class="col-12"><button class="btn btn-gold w-100 mt-2" type="submit">Create Account</button></div>
          </form>

          <p class="text-muted-lux text-center mt-4 mb-0" style="font-size:.82rem">
            Already have an account? <a href="{{ route('login') }}" class="text-gold">Sign in</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
