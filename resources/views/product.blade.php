@extends('layouts.app')

@section('title', 'Product Detail — Hand-Set Fine Jewellery | AURELIA')
@section('meta_description', 'View full details, gallery and specifications of this hand-set AURELIA piece in 18k gold with certified stones.')
@section('og_title', 'Product Detail | AURELIA')
@section('og_description', 'Hand-set 18k gold jewellery with certified stones.')
@section('og_type', 'product')

@section('content')

<section class="section" style="padding-top:9rem">
  <div class="container">
    <p class="breadcrumb-lux mb-4"><a href="{{ route('home') }}">Home</a> / <a href="{{ route('shop') }}">Shop</a> / <span id="crumb" class="text-gold">Piece</span></p>
    <div class="row g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <div class="gallery-main" id="galleryMain"><img id="mainImg" src="{{ asset('img/p1.jpg') }}" alt="Product image"></div>
        <p class="text-muted-lux mt-2" style="font-size:.7rem;letter-spacing:.2em;text-transform:uppercase">Click image to zoom</p>
        <div class="row g-2 mt-1" id="thumbs"></div>
      </div>

      <div class="col-lg-6" data-aos="fade-left">
        <p class="eyebrow" id="pCat">Rings</p>
        <h1 class="display-6 mt-2" id="pName">Solitaire Éclat Ring</h1>
        <div class="stars mb-3" id="pStars">★★★★★ <span class="text-muted-lux ms-2" style="letter-spacing:.14em">128 reviews</span></div>
        <div class="price fs-4 mb-4" id="pPrice">$4,850</div>
        <p class="text-muted-lux" id="pDesc">
          A single brilliant-cut stone raised on a hand-forged 18k gold band. The setting is
          filed and polished by one goldsmith from start to finish — no two are identical.
        </p>

        <div class="mt-4">
          <p class="eyebrow mb-2">Metal</p>
          <div class="d-flex gap-3">
            <span class="swatch active" style="background:linear-gradient(135deg,#f7ecc9,#d4af37)" title="Yellow gold"></span>
            <span class="swatch" style="background:linear-gradient(135deg,#f6dcd2,#c98e77)" title="Rose gold"></span>
            <span class="swatch" style="background:linear-gradient(135deg,#f2f2f2,#b9bcc2)" title="White gold"></span>
          </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
          <div class="qty-box">
            <button type="button" data-step="-1">−</button>
            <input id="qty" value="1" inputmode="numeric">
            <button type="button" data-step="1">+</button>
          </div>
          <button class="btn btn-gold" id="addBtn">Add to bag</button>
          <button class="btn btn-ghost"><i class="bi bi-heart me-2"></i>Save</button>
        </div>

        <div class="accordion accordion-lux mt-5" id="acc">
          <div class="accordion-item">
            <h2 class="accordion-header"><button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#a1">Details & materials</button></h2>
            <div id="a1" class="accordion-collapse collapse show" data-bs-parent="#acc"><div class="accordion-body">
              18k solid gold · certified brilliant-cut centre stone · hand-polished finish · engraved maison hallmark.</div></div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#a2">Shipping & returns</button></h2>
            <div id="a2" class="accordion-collapse collapse" data-bs-parent="#acc"><div class="accordion-body">
              Complimentary insured worldwide delivery in 3–5 days. 30-day returns on unworn pieces.</div></div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#a3">Care & warranty</button></h2>
            <div id="a3" class="accordion-collapse collapse" data-bs-parent="#acc"><div class="accordion-body">
              Lifetime craftsmanship warranty with free annual cleaning and re-polishing at any boutique.</div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section pt-0">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <p class="eyebrow">You may also love</p>
      <h2 class="display-6">Related Pieces</h2>
      <div class="rule"></div>
    </div>
    <div id="related" class="carousel slide" data-bs-ride="carousel" data-aos="fade-up">
      <div class="carousel-inner" id="relatedInner"></div>
      <button class="carousel-control-prev" type="button" data-bs-target="#related" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
      <button class="carousel-control-next" type="button" data-bs-target="#related" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script src="{{ asset('js/product.js') }}"></script>
@endpush
