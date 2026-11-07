<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'AURELIA — Fine Jewellery, Crafted in Gold & Light')</title>
<meta name="description" content="@yield('meta_description', 'AURELIA is a house of fine jewellery: rare diamonds, 18k gold and champagne stones, set by hand in limited series.')">
<meta property="og:title" content="@yield('og_title', 'AURELIA — Fine Jewellery, Crafted in Gold & Light')">
<meta property="og:description" content="@yield('og_description', 'Rare diamonds, 18k gold and champagne stones, set by hand in limited series.')">
<meta property="og:type" content="@yield('og_type', 'website')">
<meta name="twitter:card" content="summary_large_image">
@stack('head')
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500&family=Jost:wght@200;300;400;500&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body>

@include('partials.navbar')

@yield('content')

@include('partials.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="{{ asset('js/main.js') }}"></script>
@stack('scripts')
</body>
</html>
