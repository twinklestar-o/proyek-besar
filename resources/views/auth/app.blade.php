<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Dashboard')</title>
  <!-- Tailwind CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <!-- Include any additional CSS if necessary -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

</head>

<body class="flex flex-col min-h-screen">
  @if(Auth::check())
    @include('layouts.sidebar_admin') <!-- Admin layout -->
  @else
    @include('layouts.header') <!-- Header is handled in header.blade.php -->
    <div class="flex flex-1">
    @include('layouts.sidebar_user') <!-- Guest/unauthenticated layout -->
    <main class="flex-1 p-4 bg-white overflow-auto">
      @yield('content')
    </main>
    </div>
  @endif
</body>

</html>