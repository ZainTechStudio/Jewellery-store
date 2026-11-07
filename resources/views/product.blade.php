@extends('layouts.app')

@section('title', $product->name.' — AURELIA Fine Jewellery')
@section('meta_description', $product->short_description ?? str(strip_tags((string) $product->description))->limit(155))
@section('og_title', $product->name.' | AURELIA')
@section('og_description', $product->short_description ?? str(strip_tags((string) $product->description))->limit(155))
@section('og_type', 'product')

@section('content')

@php
  $mainImage = $product->primary_image;
@endphp

<section class="section" style="padding-top:9rem">
  <div class="container">
    <p class="breadcrumb-lux mb-4"><a href="{{ route('home') }}">Home</a> / <a href="{{ route('shop') }}">Shop</a> / <span class="text-gold">{{ $product->name }}</span></p>
    <div class="row g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <div class="gallery-main" id="galleryMain">
          <img id="mainImg" src="{{ $mainImage ? asset($mainImage->image_path) : asset('img/p1.jpg') }}" alt="{{ $mainImage->alt_text ?? $product->name }}">
        </div>
        <p class="text-muted-lux mt-2" style="font-size:.7rem;letter-spacing:.2em;text-transform:uppercase">Click image to zoom</p>
        <div class="row g-2 mt-1" id="thumbs">
          {{-- Phase 2: gallery now comes from product_images, in stored sort_order —
               no more random other-products images (a bug flagged in the Phase 0 audit). --}}
          @foreach($product->images as $image)
            <div class="col-3">
              <div class="thumb {{ $loop->first ? 'active' : '' }}" data-src="{{ asset($image->image_path) }}">
                <img src="{{ asset($image->image_path) }}" alt="{{ $image->alt_text ?? $product->name.' view '.$loop->iteration }}" loading="lazy">
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <div class="col-lg-6" data-aos="fade-left">
        <p class="eyebrow">{{ $product->category->name ?? '' }}</p>
        <h1 class="display-6 mt-2">{{ $product->name }}</h1>
        <div class="stars mb-3">★★★★★ <span class="text-muted-lux ms-2" style="letter-spacing:.14em">128 reviews</span></div>
        <div class="price fs-4 mb-4">
          {{ $product->has_discount ? $product->formatted_sale_price : $product->formatted_price }}
          @if($product->has_discount)
            <del>{{ $product->formatted_price }}</del>
          @endif
        </div>
        <p class="text-muted-lux">{{ $product->description ?? $product->short_description }}</p>

        @if($product->metalType || $product->gemstone || $product->metal_purity)
        <div class="mt-4">
          <p class="eyebrow mb-2">Details</p>
          <p class="text-muted-lux mb-0" style="font-size:.85rem">
            @if($product->metalType){{ $product->metalType->name }}@endif
            @if($product->metal_purity) &middot; {{ $product->metal_purity }}@endif
            @if($product->gemstone) &middot; {{ $product->gemstone->name }}@endif
            @if($product->occasion) &middot; {{ $product->occasion->name }}@endif
          </p>
        </div>
        @endif

        {{-- Metal swatches: kept as static/cosmetic exactly as in the original design.
             Each product has a single metal_type (not a set of selectable metal options),
             so there is no real data to wire this to without inventing a variant axis
             that doesn't exist in the Phase 1 schema — left untouched, matching the
             instruction to preserve UI that has no corresponding database field. --}}
        <div class="mt-4">
          <p class="eyebrow mb-2">Metal</p>
          <div class="d-flex gap-3">
            <span class="swatch active" style="background:linear-gradient(135deg,#f7ecc9,#d4af37)" title="Yellow gold"></span>
            <span class="swatch" style="background:linear-gradient(135deg,#f6dcd2,#c98e77)" title="Rose gold"></span>
            <span class="swatch" style="background:linear-gradient(135deg,#f2f2f2,#b9bcc2)" title="White gold"></span>
          </div>
        </div>

        @if($product->variants->isNotEmpty())
        <div class="mt-4">
          <p class="eyebrow mb-2">Size</p>
          <select id="variant" class="form-select form-select-lux" style="max-width:220px">
            @foreach($product->variants as $variant)
              <option value="{{ $variant->id }}">{{ $variant->name }}</option>
            @endforeach
          </select>
        </div>
        @endif

        <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
          <div class="qty-box">
            <button type="button" data-step="-1">−</button>
            <input id="qty" value="1" inputmode="numeric">
            <button type="button" data-step="1">+</button>
          </div>
          {{-- Phase 3: only the product id (+ selected variant id, read from
               #variant above) is sent to the cart backend — see product.js. --}}
          <button class="btn btn-gold" id="addBtn" data-product-id="{{ $product->id }}">Add to bag</button>
          <button class="btn btn-ghost"><i class="bi bi-heart me-2"></i>Save</button>
        </div>

        <div class="accordion accordion-lux mt-5" id="acc">
          <div class="accordion-item">
            <h2 class="accordion-header"><button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#a1">Details & materials</button></h2>
            <div id="a1" class="accordion-collapse collapse show" data-bs-parent="#acc"><div class="accordion-body">
              @if($product->metalType){{ $product->metalType->name }}@endif@if($product->metal_purity) ({{ $product->metal_purity }})@endif
              @if($product->gemstone) · certified {{ $product->gemstone->name }}@endif
              · hand-polished finish · engraved maison hallmark.</div></div>
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

@if($relatedProducts->isNotEmpty())
<section class="section pt-0">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <p class="eyebrow">You may also love</p>
      <h2 class="display-6">Related Pieces</h2>
      <div class="rule"></div>
    </div>
    {{-- Phase 2: related products are now genuinely category-based (same category,
         active, excluding the current product) — the old behaviour showed random
         other products, flagged as a bug during the Phase 0 audit. --}}
    <div id="related" class="carousel slide" data-bs-ride="carousel" data-aos="fade-up">
      <div class="carousel-inner">
        @foreach($relatedProducts->chunk(4) as $chunk)
          <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
            <div class="row g-4 px-lg-5">
              @foreach($chunk as $related)
                @include('partials.product-card', ['product' => $related, 'delay' => 0])
              @endforeach
            </div>
          </div>
        @endforeach
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#related" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
      <button class="carousel-control-next" type="button" data-bs-target="#related" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
    </div>
  </div>
</section>
@endif

@endsection

@push('scripts')
<script src="{{ asset('js/product.js') }}"></script>
@endpush
