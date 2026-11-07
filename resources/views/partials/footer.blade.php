<footer class="footer-lux">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4">
        <span class="brand-mark">Aurelia<small>Fine Jewellery</small></span>
        <p class="text-muted-lux mt-3" style="max-width:22rem">A small house of goldsmiths working in rare stones and 18k gold since 1974.</p>
        <div class="mt-3">
          <a class="social-ico" href="#"><i class="bi bi-instagram"></i></a>
          <a class="social-ico" href="#"><i class="bi bi-facebook"></i></a>
          <a class="social-ico" href="#"><i class="bi bi-pinterest"></i></a>
          <a class="social-ico" href="#"><i class="bi bi-twitter-x"></i></a>
        </div>
      </div>
      <div class="col-6 col-lg-2"><h6>Shop</h6>
        <a href="{{ route('shop') }}">Rings</a><br><a href="{{ route('shop') }}">Necklaces</a><br>
        <a href="{{ route('shop') }}">Earrings</a><br><a href="{{ route('shop') }}">Bracelets</a></div>
      <div class="col-6 col-lg-2"><h6>Maison</h6>
        <a href="{{ route('about') }}">Our story</a><br><a href="{{ route('about') }}">Craftsmanship</a><br>
        <a href="{{ route('contact') }}">Boutiques</a><br><a href="{{ route('contact') }}">Contact</a></div>
      <div class="col-lg-4"><h6>Client Care</h6>
        <p class="text-muted-lux mb-1"><i class="bi bi-telephone text-gold me-2"></i>+1 (212) 555 0148</p>
        <p class="text-muted-lux mb-1"><i class="bi bi-envelope text-gold me-2"></i>care@aurelia.com</p>
        <p class="text-muted-lux"><i class="bi bi-geo-alt text-gold me-2"></i>14 Rue de la Paix, Paris</p></div>
    </div>
    <div class="footer-bottom d-flex flex-wrap justify-content-between">
      <span>© {{ date('Y') }} Aurelia Fine Jewellery. All rights reserved.</span>
      <span>Privacy · Terms · Shipping</span>
    </div>
  </div>
</footer>
