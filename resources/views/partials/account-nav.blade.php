<div class="summary-box mb-4 mb-lg-0">
  <p class="eyebrow mb-3">My Account</p>
  <div class="d-flex flex-column gap-2">
    <a href="{{ route('account.index') }}" class="{{ request()->routeIs('account.index') ? 'text-gold' : 'text-muted-lux' }}" style="font-size:.85rem">Dashboard</a>
    <a href="{{ route('account.profile.edit') }}" class="{{ request()->routeIs('account.profile.*') ? 'text-gold' : 'text-muted-lux' }}" style="font-size:.85rem">Profile</a>
    <a href="{{ route('account.password.edit') }}" class="{{ request()->routeIs('account.password.*') ? 'text-gold' : 'text-muted-lux' }}" style="font-size:.85rem">Password</a>
    <a href="{{ route('account.addresses.index') }}" class="{{ request()->routeIs('account.addresses.*') ? 'text-gold' : 'text-muted-lux' }}" style="font-size:.85rem">Addresses</a>
    <hr style="border-color:var(--line)" class="my-1">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="text-muted-lux p-0 border-0 bg-transparent" style="font-size:.85rem">Logout</button>
    </form>
  </div>
</div>
