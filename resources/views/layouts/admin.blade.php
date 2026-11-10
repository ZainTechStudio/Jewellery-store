<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Admin') &middot; AURELIA</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500&family=Jost:wght@200;300;400;500&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body class="admin-body">

<div class="admin-shell">
  @include('admin.partials.sidebar')

  <div class="admin-content">
    <header class="admin-topbar">
      <button class="admin-toggle d-lg-none" type="button" id="adminSidebarToggle"><i class="bi bi-list"></i></button>
      <h1 class="admin-page-title">@yield('title', 'Dashboard')</h1>
      <div class="ms-auto d-flex align-items-center gap-3">
        <a href="{{ route('home') }}" target="_blank" class="admin-topbar-link"><i class="bi bi-box-arrow-up-right me-1"></i>View Store</a>
        <div class="dropdown">
          <a href="#" class="admin-topbar-link" id="adminAccountMenu" data-bs-toggle="dropdown"><i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}</a>
          <ul class="dropdown-menu dropdown-menu-end account-dropdown" aria-labelledby="adminAccountMenu">
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item">Logout</button>
              </form>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <main class="admin-main">
      @if (session('success'))
        <div class="admin-alert admin-alert-success">{{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="admin-alert admin-alert-error">{{ session('error') }}</div>
      @endif
      @if ($errors->any())
        <div class="admin-alert admin-alert-error">
          @foreach ($errors->all() as $error)<p class="mb-1">{{ $error }}</p>@endforeach
        </div>
      @endif

      @yield('content')
    </main>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.getElementById('adminSidebarToggle')?.addEventListener('click', function () {
    document.querySelector('.admin-sidebar').classList.toggle('show');
  });
</script>
@stack('scripts')
</body>
</html>
