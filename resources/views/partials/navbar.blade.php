<nav class="navbar navbar-expand-lg navbar-lux fixed-top">
  <div class="container">
    <a class="navbar-brand brand-mark" href="{{ route('home') }}">Aurelia<small>Fine Jewellery</small></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><i class="bi bi-list"></i></button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('shop') ? 'active' : '' }}" href="{{ route('shop') }}">Shop</a></li>
        {{-- Phase 2: "Collection" used to link to a generic /product page (defaulted to the
             first hardcoded product). Real products now live at /product/{slug}, so there is
             no single "generic" product page to link to any more — this now points to the
             shop listing, which is what "browse the collection" actually means. --}}
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('product.show') ? 'active' : '' }}" href="{{ route('shop') }}">Collection</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a></li>
      </ul>
      <div class="d-flex align-items-center">
        <a href="{{ route('shop') }}" class="nav-icon"><i class="bi bi-search"></i></a>
        {{-- Phase 4: the person icon is now a small Bootstrap dropdown showing
             auth state, instead of a dead link. Same icon/position as before —
             no navbar redesign, just real behaviour behind it. --}}
        <div class="dropdown d-inline-block">
          <a href="#" class="nav-icon" id="accountMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-person"></i></a>
          <ul class="dropdown-menu dropdown-menu-end account-dropdown" aria-labelledby="accountMenu">
            @auth
              <li><span class="dropdown-item-text text-muted-lux" style="font-size:.78rem">{{ Auth::user()->name }}</span></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="{{ route('account.index') }}">My Account</a></li>
              <li><a class="dropdown-item" href="{{ route('account.addresses.index') }}">Addresses</a></li>
              <li>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="dropdown-item">Logout</button>
                </form>
              </li>
            @else
              <li><a class="dropdown-item" href="{{ route('login') }}">Login</a></li>
              <li><a class="dropdown-item" href="{{ route('register') }}">Register</a></li>
            @endauth
          </ul>
        </div>
        <a href="{{ route('cart.index') }}" class="nav-icon"><i class="bi bi-bag"></i><span class="cart-count">{{ $cartCount ?? 0 }}</span></a>
      </div>
    </div>
  </div>
</nav>
