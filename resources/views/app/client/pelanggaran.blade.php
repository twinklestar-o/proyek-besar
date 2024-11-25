<!DOCTYPE html>
<html lang="en">
  <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Dashboard Del</title>
      <script src="https://cdn.tailwindcss.com"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
  </head>

  <body class="bg-gray-50">
      <!-- Include Header -->
      @include('layouts.headerbar')

      <!-- Include Sidebar -->
      @include('layouts.sidebar.sidebar_display')

      <!-- Main Content -->
      <main class="pl-64 mr-5 pt-20">
        <h2> Pelanggaran</h2>
      </main>
  </body>
</html>