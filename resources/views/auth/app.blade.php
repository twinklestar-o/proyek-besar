<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Application')</title>
  <style>
    /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      line-height: 1.6;
      background-color: #f5f5f5;
      color: #333;
    }

    /* Header */
    .header {
      background-color: #0056b3;
      color: #fff;
      padding: 1rem;
      text-align: center;
      font-size: 1.5rem;
      font-weight: bold;
      width: 100%;
    }

    /* Layout */
    .layout {
      display: flex;
      height: calc(100vh - 80px);
      /* Adjusted for header height */
    }

    /* Sidebar */
    .sidebar {
      background-color: #e8e8e8;
      width: 200px;
      padding: 1rem;
      overflow-y: auto;
      border-right: 1px solid #ddd;
    }

    .sidebar nav ul {
      list-style: none;
    }

    .sidebar nav ul li {
      margin-bottom: 10px;
    }

    .sidebar nav ul li a {
      text-decoration: none;
      color: #0056b3;
      font-weight: bold;
      display: block;
      padding: 0.5rem;
      border-radius: 5px;
      transition: background-color 0.3s ease;
    }

    .sidebar nav ul li a:hover {
      background-color: #0056b3;
      color: #fff;
    }

    /* Content */
    .content {
      flex: 1;
      padding: 2rem;
      background-color: #fff;
      overflow-y: hidden;
    }
  </style>
</head>

<body>

  @guest
    @include('layouts.header') {{-- Include the header for guests --}}
  @endguest

  <div class="layout">

    @auth
    @include('layouts.sidebar_admin') {{-- Admin sidebar for authenticated users --}}
  @else
  @include('layouts.sidebar_user') {{-- User sidebar for guests --}}
@endauth

    <main class="content">
      @yield('content') {{-- Dynamic content for the page --}}
    </main>
  </div>
</body>

</html>