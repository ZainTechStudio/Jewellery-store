@extends('layouts.app')

@section('title', 'AURELIA — Fine Jewellery, Crafted in Gold & Light')
@section('meta_description', 'AURELIA is a house of fine jewellery: rare diamonds, 18k gold and champagne stones, set by hand in limited series.')
@section('og_title', 'AURELIA — Fine Jewellery, Crafted in Gold & Light')
@section('og_description', 'Rare diamonds, 18k gold and champagne stones, set by hand in limited series.')

@section('content')

<header class="hero hero-video-mode">
  <div class="hero-media" aria-hidden="true">
    <img class="hero-fallback" src="{{ asset('img/hero-fallback.jpg') }}" alt="" width="1920" height="1080">
    <video class="hero-video" autoplay muted loop playsinline preload="none"
           poster="{{ asset('img/hero-fallback.jpg') }}"
           data-src="{{ asset('video/hero2-jewellery.mp4') }}"></video>
    <span class="hero-video-overlay"></span>
    <span class="hero-video-gold"></span>
  </div>
  <span class="hero-glow"></span>
  <div class="container position-relative">
    <div class="row align-items-center g-5">
      <div class="col-lg-6 hero-copy hero-fade">
        <p class="eyebrow mb-3" data-aos="fade-up">Maison Aurelia · Est. 1974</p>
        <h1 data-aos="fade-up" data-aos-delay="80">Light, <span class="gold-text">set in gold</span>.</h1>
        <p class="lead-lux mt-4" data-aos="fade-up" data-aos-delay="160">
          Each piece is cut, polished and set by hand in our atelier — rare stones,
          18k gold, and a quiet kind of brilliance made to be worn for decades.
        </p>
        <div class="d-flex flex-wrap gap-3 mt-5" data-aos="fade-up" data-aos-delay="240">
          <a href="{{ route('shop') }}" class="btn btn-gold">Explore the collection</a>
          <a href="{{ route('about') }}" class="btn btn-ghost">Our craft</a>
        </div>
      </div>
      <div class="col-lg-6"></div>
    </div>
  </div>
  <div class="scroll-cue">Scroll <i class="bi bi-chevron-down"></i></div>
</header>

<div class="marquee-strip">
  <div>
    <span>Complimentary worldwide shipping <i class="bi bi-dot"></i></span>
    <span>Lifetime craftsmanship warranty <i class="bi bi-dot"></i></span>
    <span>Ethically sourced stones <i class="bi bi-dot"></i></span>
    <span>Private atelier appointments <i class="bi bi-dot"></i></span>
    <span>Complimentary worldwide shipping <i class="bi bi-dot"></i></span>
    <span>Lifetime craftsmanship warranty <i class="bi bi-dot"></i></span>
    <span>Ethically sourced stones <i class="bi bi-dot"></i></span>
    <span>Private atelier appointments <i class="bi bi-dot"></i></span>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <p class="eyebrow">The Houses</p>
      <h2 class="display-6">Featured Categories</h2>
      <div class="rule"></div>
    </div>
    <div class="row g-4">
      {{-- Phase 2: category tiles are now database-backed (top 3 active top-level
           categories by sort_order). No category images are seeded yet, so the
           existing placeholder photos are reused per tile position — no new
           photography invented. --}}
      @foreach($topCategories as $i => $category)
      <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ $i * 120 }}"><a class="cat-tile" href="{{ route('shop') }}?category={{ $category->slug }}">
        <img src="{{ $category->image ? asset($category->image) : asset('img/p'.($i + 1).'.jpg') }}" alt="{{ $category->name }}" loading="lazy">
        <div class="cat-body"><h4>{{ $category->name }}</h4><br><span>Discover →</span></div></a>
      </div>
      @endforeach
    </div>
  </div>
</section>

<section class="section pt-0">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <p class="eyebrow">Selected Pieces</p>
      <h2 class="display-6">Featured Jewellery</h2>
      <div class="rule"></div>
    </div>
    <div class="row g-4">
      {{-- Phase 2: featured products now come from the database (is_active + is_featured, ordered by sort_order). --}}
      @foreach($featuredProducts as $i => $product)
        @include('partials.product-card', ['product' => $product, 'delay' => $i * 100])
      @endforeach
    </div>
    <div class="text-center mt-5" data-aos="fade-up">
      <a href="{{ route('shop') }}" class="btn btn-ghost">View all pieces</a>
    </div>
  </div>
</section>

<section class="banner-lux">
  <div class="container">
    <div class="row">
      <div class="col-lg-6" data-aos="fade-right">
        <p class="eyebrow">Private Event</p>
        <h2 class="display-5 mt-2">The Champagne <span class="gold-text">Collection</span></h2>
        <div class="rule rule-start"></div>
        <p class="text-muted-lux mb-4">Twelve one-of-a-kind pieces, released for a single season.
          Enjoy 15% within the private preview window.</p>
        <a href="{{ route('shop') }}" class="btn btn-gold">Shop the release</a>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <p class="eyebrow">Clients</p>
      <h2 class="display-6">Words of Praise</h2>
      <div class="rule"></div>
    </div>
    <div id="testi" class="carousel slide" data-bs-ride="carousel" data-aos="fade-up">
      <div class="carousel-inner pb-5">
        <div class="carousel-item active"><div class="quote-card">
          <div class="stars mb-3">★★★★★</div>
          <p>&ldquo;The solitaire arrived in a hand-tied box and it genuinely stopped the room. Nothing about this house feels mass produced.&rdquo;</p>
          <span class="eyebrow d-block mt-3">Elena V. — Milan</span></div>
        </div>
        <div class="carousel-item"><div class="quote-card">
          <div class="stars mb-3">★★★★★</div>
          <p>&ldquo;I have bought from the great maisons. Aurelia's finishing is at that level, with a warmth the big names lost.&rdquo;</p>
          <span class="eyebrow d-block mt-3">Rafael M. — Lisbon</span></div>
        </div>
        <div class="carousel-item"><div class="quote-card">
          <div class="stars mb-3">★★★★★</div>
          <p>&ldquo;They re-set my grandmother's stones. Two months of care, and the result is heirloom quality.&rdquo;</p>
          <span class="eyebrow d-block mt-3">Aisha K. — Dubai</span></div>
        </div>
      </div>
      <div class="carousel-indicators">
        <button data-bs-target="#testi" data-bs-slide-to="0" class="active"></button>
        <button data-bs-target="#testi" data-bs-slide-to="1"></button>
        <button data-bs-target="#testi" data-bs-slide-to="2"></button>
      </div>
    </div>
  </div>
</section>

<section class="section-sm bg-ink-2 border-top border-bottom" style="border-color:var(--line)!important">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-6" data-aos="fade-right">
        <p class="eyebrow">The Private List</p>
        <h3 class="mb-0">Invitations, new releases, nothing else.</h3>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <form class="d-flex gap-2 flex-wrap flex-sm-nowrap" data-fake="Welcome to the private list" method="POST">
          @csrf
          {{-- action left unset for now — newsletter endpoint is wired up in a later phase --}}
          <input type="email" name="email" required class="form-control form-control-lux" placeholder="Your email address">
          <button class="btn btn-gold flex-shrink-0" type="submit">Subscribe</button>
        </form>
      </div>
    </div>
  </div>
</section>

@endsection
