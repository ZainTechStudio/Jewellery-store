@extends('layouts.app')

@section('title', 'About the Maison — Goldsmiths Since 1974 | AURELIA')
@section('meta_description', 'The story of AURELIA: a family atelier of goldsmiths hand-setting rare stones in 18k gold since 1974.')
@section('og_title', 'About the Maison | AURELIA')
@section('og_description', 'A family atelier of goldsmiths hand-setting rare stones in 18k gold since 1974.')

@section('content')

<section class="page-head">
  <div class="container">
    <p class="eyebrow">Maison Aurelia</p>
    <h1>Fifty Years of Light</h1>
    <div class="rule"></div>
    <p class="breadcrumb-lux"><a href="{{ route('home') }}">Home</a> &nbsp;/&nbsp; About</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <div class="cat-tile" style="aspect-ratio:4/5">
          <img src="{{ asset('img/p6.jpg') }}" alt="Aurelia atelier jewellery" loading="lazy">
        </div>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <p class="eyebrow">Our Story</p>
        <h2 class="display-6 mt-2">Made slowly, on purpose.</h2>
        <div class="rule rule-start"></div>
        <p class="text-muted-lux">Aurelia began in 1974 in a two-bench workshop off the Rue de la Paix.
          Half a century later the benches have multiplied, but the rule has not changed: one goldsmith
          carries a piece from wax to final polish.</p>
        <p class="text-muted-lux">We buy small parcels of stones directly, decline anything without
          traceable origin, and release in limited series rather than seasons. It is a slower way to
          build a house — it is also the only way we know to make something worth inheriting.</p>
        <div class="row g-4 mt-3">
          <div class="col-4"><h3 class="gold-text mb-0">50</h3><span class="card-cat">Years</span></div>
          <div class="col-4"><h3 class="gold-text mb-0">1974</h3><span class="card-cat">Founded</span></div>
          <div class="col-4"><h3 class="gold-text mb-0">18k</h3><span class="card-cat">Solid gold</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section pt-0">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <p class="eyebrow">What we hold to</p>
      <h2 class="display-6">The House Values</h2>
      <div class="rule"></div>
    </div>
    <div class="row g-4 text-center">
      <div class="col-md-3" data-aos="fade-up"><div class="card-lux p-4 h-100">
        <i class="bi bi-gem value-ico"></i><h5>Rare Stones</h5>
        <p class="text-muted-lux mb-0" style="font-size:.88rem">Hand-selected parcels, certified and fully traceable.</p></div></div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="100"><div class="card-lux p-4 h-100">
        <i class="bi bi-hammer value-ico"></i><h5>One Goldsmith</h5>
        <p class="text-muted-lux mb-0" style="font-size:.88rem">Every piece finished by the same pair of hands.</p></div></div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="200"><div class="card-lux p-4 h-100">
        <i class="bi bi-award value-ico"></i><h5>Lifetime Care</h5>
        <p class="text-muted-lux mb-0" style="font-size:.88rem">Free cleaning, re-polishing and setting checks, forever.</p></div></div>
      <div class="col-md-3" data-aos="fade-up" data-aos-delay="300"><div class="card-lux p-4 h-100">
        <i class="bi bi-globe2 value-ico"></i><h5>Ethical Sourcing</h5>
        <p class="text-muted-lux mb-0" style="font-size:.88rem">Recycled gold and conflict-free stones, without exception.</p></div></div>
    </div>
  </div>
</section>

<section class="banner-lux">
  <div class="container text-center">
    <p class="eyebrow" data-aos="fade-up">Private Appointment</p>
    <h2 class="display-5 mt-2" data-aos="fade-up" data-aos-delay="80">Visit the <span class="gold-text">atelier</span></h2>
    <p class="text-muted-lux mx-auto mb-4" style="max-width:34rem" data-aos="fade-up" data-aos-delay="140">
      Spend an hour with a goldsmith and design a piece that exists only once.</p>
    <a href="{{ route('contact') }}" class="btn btn-gold" data-aos="fade-up" data-aos-delay="200">Book a visit</a>
  </div>
</section>

@endsection
