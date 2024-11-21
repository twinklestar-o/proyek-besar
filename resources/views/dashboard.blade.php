<!-- resources/views/home.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Del</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50">

    <!-- Include Header -->
    @include('layouts.headerbar')

    <!-- Include Sidebar -->
    @include('layouts.sidebar.sidebar_display')

    <!-- Main Content -->
    <main class="pl-64 pt-20">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-4">Welcome to the Home Page!</h1>
            <p>This is the main content area. You can add more content here.</p>
        </div>
    </main>

</body>

</html>