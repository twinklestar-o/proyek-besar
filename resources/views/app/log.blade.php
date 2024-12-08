@extends('auth.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold text-gray-800 mb-4">Log Mahasiswa</h1>

  <!-- Filter Form -->
  <form id="filterForm" method="GET" action="{{ route(Auth::check() ? 'log.mahasiswa.auth' : 'log.mahasiswa.public') }}"
    class="mb-4 space-y-4">
    <!-- Start Masuk -->
    <div>
      <label for="start_masuk" class="block text-gray-700 font-semibold mb-2">Start Masuk</label>
      <input type="date" name="start_masuk" id="start_masuk"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        value="{{ request('start_masuk') }}">
    </div>

    <!-- End Masuk -->
    <div>
      <label for="end_masuk" class="block text-gray-700 font-semibold mb-2">End Masuk</label>
      <input type="date" name="end_masuk" id="end_masuk"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        value="{{ request('end_masuk') }}">
    </div>

    <!-- Start Keluar -->
    <div>
      <label for="start_keluar" class="block text-gray-700 font-semibold mb-2">Start Keluar</label>
      <input type="date" name="start_keluar" id="start_keluar"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        value="{{ request('start_keluar') }}">
    </div>

    <!-- End Keluar -->
    <div>
      <label for="end_keluar" class="block text-gray-700 font-semibold mb-2">End Keluar</label>
      <input type="date" name="end_keluar" id="end_keluar"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        value="{{ request('end_keluar') }}">
    </div>

    <!-- Day -->
    <div>
      <label for="day" class="block text-gray-700 font-semibold mb-2">Day</label>
      <input type="text" name="day" id="day"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        value="{{ request('day') }}">
    </div>

    <!-- Month -->
    <div>
      <label for="month" class="block text-gray-700 font-semibold mb-2">Month</label>
      <input type="number" name="month" id="month" min="1" max="12"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        value="{{ request('month') }}">
    </div>

    <!-- Year -->
    <div>
      <label for="year" class="block text-gray-700 font-semibold mb-2">Year</label>
      <input type="number" name="year" id="year" min="2000" max="2099"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        value="{{ request('year') }}">
    </div>

    <!-- Fetch Data Button -->
    <div>
      <button type="submit"
        class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-200">
        Fetch Data
      </button>
    </div>
  </form>

  <!-- Display Data -->
  <div class="mt-4">
    <h2 class="text-xl font-bold text-gray-800 mb-2">Log Summary</h2>
    @if(isset($data) && isset($data['result']) && $data['result'] == "OK")
    <p><strong>Status:</strong> {{ $data['status'] ?? 'N/A' }}</p>
    <p><strong>Total:</strong> {{ $data['total'] ?? '0' }}</p>
  @else
  <p class="text-lg text-red-500 font-semibold">Data tidak tersedia.</p>
@endif
  </div>
</div>
@endsection