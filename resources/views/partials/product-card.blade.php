{{--
    Reusable product card. Expects:
    - $product  (Eloquent Product, with `images` eager-loaded)
    - $delay    (optional AOS animation delay in ms, default 0)

    Markup/classes are kept identical to the old public/js/main.js
    productCard() function so the visual design is unchanged — this
    partial just replaces client-side rendering with server-side
    rendering from real database data.

    Badge text is derived from real product flags only (is_featured /
    has_discount) — the old JS catalogue's arbitrary "tag" strings
    (Bestseller/New/Signature/Limited) don't exist as real data in the
    Phase 1 schema, so they aren't fabricated here. The star rating
    line is kept as static decorative markup (identical on every card)
    since no review/rating data exists yet — reviews are out of scope
    for this phase.
--}}
@php
    $delay = $delay ?? 0;
    $image = $product->primary_image;
    $badge = $product->has_discount ? 'Sale' : ($product->is_featured ? 'Featured' : null);
@endphp
<div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $delay }}">
  <article class="card-lux">
    <a href="{{ route('product.show', $product) }}" class="card-media d-block">
      @if($badge)
        <span class="badge-lux">{{ $badge }}</span>
      @endif
      <img src="{{ $image ? asset($image->image_path) : asset('img/p1.jpg') }}" alt="{{ $image->alt_text ?? $product->name }}" loading="lazy" width="900" height="900">
      {{-- Phase 3: only the product id is sent to the cart backend — price,
           name, image etc. are never client-submitted; the server looks
           everything up from the database when /cart/add is called. --}}
      <span class="quick-add" data-add data-product-id="{{ $product->id }}">Add to bag</span>
    </a>
    <div class="card-body-lux">
      <span class="card-cat">{{ $product->category->name ?? '' }}</span>
      <h5 class="mt-1"><a href="{{ route('product.show', $product) }}">{{ $product->name }}</a></h5>
      <div class="stars">★★★★★</div>
      <div class="price">
        {{ $product->has_discount ? $product->formatted_sale_price : $product->formatted_price }}
        @if($product->has_discount)
          <del>{{ $product->formatted_price }}</del>
        @endif
      </div>
    </div>
  </article>
</div>
