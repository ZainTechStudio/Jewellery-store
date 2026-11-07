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
    <div class="row g-3 align-items-center mb-5" data-aos="fade-up">
      <div class="col-lg-6">
        <input id="search" class="form-control form-control-lux" placeholder="Search the collection">
      </div>
      <div class="col-6 col-lg-3">
        <select id="cat" class="form-select form-select-lux">
          <option value="">All categories</option>
          <option>Rings</option><option>Necklaces</option><option>Earrings</option>
          <option>Bracelets</option><option>Sets</option>
        </select>
      </div>
      <div class="col-6 col-lg-3">
        <select id="sort" class="form-select form-select-lux">
          <option value="featured">Sort: Featured</option>
          <option value="low">Price: Low to High</option>
          <option value="high">Price: High to Low</option>
          <option value="name">Name: A–Z</option>
        </select>
      </div>
    </div>
    <div class="row g-4" id="grid"></div>
    <p class="text-center text-muted-lux mt-5 d-none" id="empty">No pieces match your search.</p>
  </div>
</section>

@endsection

@push('scripts')
<script src="{{ asset('js/shop.js') }}"></script>
@endpush
