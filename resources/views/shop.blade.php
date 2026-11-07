@extends('layouts.app')

@section('title', 'Shop Fine Jewellery — Rings, Necklaces & More | AURELIA')
@section('meta_description', 'Browse the AURELIA collection: hand-set rings, necklaces, earrings and bracelets in 18k gold with rare diamonds.')
@section('og_title', 'Shop Fine Jewellery | AURELIA')
@section('og_description', 'Hand-set rings, necklaces, earrings and bracelets in 18k gold.')

@section('content')

<section class="page-head">
  <div class="container">
    <p class="eyebrow" data-aos="fade-up">The Collection</p>
    <h1 data-aos="fade-up" data-aos-delay="80">Shop All Jewellery</h1>
    <div class="rule"></div>
    <p class="breadcrumb-lux"><a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; Shop</p>
  </div>
</section>

<section class="section">
  <div class="container">
    {{-- Phase 2: filters are now a GET form so the URL represents filter state
         (e.g. /shop?category=rings&sort=low) and filtering happens server-side
         against the products table. shop.js only auto-submits this form on
         change — it no longer filters a hardcoded catalogue client-side. --}}
    <form method="GET" action="{{ route('shop') }}" id="shopFilters" class="row g-3 align-items-center mb-5" data-aos="fade-up">
      <div class="col-lg-6">
        <input type="text" name="q" id="search" value="{{ $search }}" class="form-control form-control-lux" placeholder="Search the collection">
      </div>
      <div class="col-6 col-lg-3">
        <select name="category" id="cat" class="form-select form-select-lux">
          <option value="">All categories</option>
          @foreach($categories as $category)
            <option value="{{ $category->slug }}" @selected($selectedCategory && $selectedCategory->slug === $category->slug)>{{ $category->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-6 col-lg-3">
        <select name="sort" id="sort" class="form-select form-select-lux">
          <option value="featured" @selected($sort === 'featured')>Sort: Featured</option>
          <option value="low" @selected($sort === 'low')>Price: Low to High</option>
          <option value="high" @selected($sort === 'high')>Price: High to Low</option>
          <option value="name" @selected($sort === 'name')>Name: A–Z</option>
        </select>
      </div>
    </form>

    <div class="row g-4" id="grid">
      @foreach($products as $i => $product)
        @include('partials.product-card', ['product' => $product, 'delay' => ($i % 4) * 90])
      @endforeach
    </div>
    <p class="text-center text-muted-lux mt-5 {{ $products->isNotEmpty() ? 'd-none' : '' }}" id="empty">No pieces match your search.</p>
  </div>
</section>

@endsection

@push('scripts')
<script src="{{ asset('js/shop.js') }}"></script>
@endpush
