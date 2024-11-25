<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Del</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
  @extends('layouts.auth')
</head>

<body class="bg-gray-50">
  @section('content')
  <main class="pl-64 mr-5 pt-20">
    <div class="container mx-auto mt-8 ">
      <h1 class="text-2xl font-bold mb-4">Log Keluar/Masuk</h1>
      <p>Ini halaman keluar masuk.</p>

    </div>
    @endsection

    @section('title', 'Log Keluar/Masuk')


    @include('layouts.headerbar')
    <!-- Include Sidebar -->
    @include('layouts.sidebar.sidebar_display')
</body>

</html>