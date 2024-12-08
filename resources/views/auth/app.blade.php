<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Application')</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
  @if(Auth::check()) <!-- Check if the user is authenticated -->
    @include('layouts.sidebar_admin') <!-- Admin layout -->
  @else
    @include('layouts.header') <!-- Header is now completely handled in header.blade.php -->
    <div class="layout flex">
    @include('layouts.sidebar_user') <!-- Guest/unauthenticated layout -->
    <main class="content flex-1 p-4 bg-white">
      @yield('content')
    </main>
    </div>
  @endif
</body>

</html>