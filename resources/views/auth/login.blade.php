@extends('layouts.app')

@section('title', 'Sign In | AURELIA')
@section('meta_description', 'Sign in to your AURELIA account.')

@section('content')

<section class="page-head">
  <div class="container">
    <p class="eyebrow">Client Access</p>
    <h1>Sign In</h1>
    <div class="rule"></div>
    <p class="breadcrumb-lux"><a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; Sign In</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-5">
        <div class="summary-box" data-aos="fade-up">
          <p class="eyebrow mb-3">Welcome Back</p>

          @if ($errors->any())
            <div class="mb-3" style="font-size:.82rem;color:#e2a5a5">
              @foreach ($errors->all() as $error)
                <p class="mb-1">{{ $error }}</p>
              @endforeach
            </div>
          @endif

          <form method="POST" action="{{ route('login.attempt') }}" class="d-flex flex-column gap-3">
            @csrf
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="form-control form-control-lux" placeholder="Email address">
            <input type="password" name="password" required class="form-control form-control-lux" placeholder="Password">
            <button class="btn btn-gold w-100 mt-2" type="submit">Sign In</button>
          </form>

          <p class="text-muted-lux text-center mt-4 mb-0" style="font-size:.82rem">
            New to Aurelia? <a href="{{ route('register') }}" class="text-gold">Create an account</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
