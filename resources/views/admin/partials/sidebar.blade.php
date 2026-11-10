<aside class="admin-sidebar">
  <a class="admin-brand" href="{{ route('admin.dashboard') }}">Aurelia<small>Admin</small></a>
  <nav class="admin-nav">
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}"><i class="bi bi-gem"></i> Products</a>
    <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i> Categories</a>
    <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"><i class="bi bi-receipt"></i> Orders</a>
    <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}"><i class="bi bi-people"></i> Customers</a>
    <a href="{{ route('admin.reviews.index') }}" class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}"><i class="bi bi-star"></i> Reviews</a>
    <a href="{{ route('admin.coupons.index') }}" class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}"><i class="bi bi-ticket-perforated"></i> Coupons</a>

    <span class="admin-nav-label">Attributes</span>
    <a href="{{ route('admin.metal-types.index') }}" class="{{ request()->routeIs('admin.metal-types.*') ? 'active' : '' }}"><i class="bi bi-gem"></i> Metal Types</a>
    <a href="{{ route('admin.gemstones.index') }}" class="{{ request()->routeIs('admin.gemstones.*') ? 'active' : '' }}"><i class="bi bi-sparkle"></i> Gemstones</a>
    <a href="{{ route('admin.occasions.index') }}" class="{{ request()->routeIs('admin.occasions.*') ? 'active' : '' }}"><i class="bi bi-calendar-event"></i> Occasions</a>
  </nav>
</aside>
