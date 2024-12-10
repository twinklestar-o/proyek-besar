@extends('auth.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <!-- Absensi Asrama Section -->
  <div class="bg-white shadow rounded-lg p-6 mb-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Absensi Asrama</h1>

    <!-- Filter Form -->
    <form id="filterForm" method="GET"
      action="{{ route(Auth::check() ? 'absensi.asrama.auth' : 'absensi.asrama.public') }}" class="mb-4 space-y-4">
      <!-- Pilih Asrama -->
      <div>
        <label for="id_asrama" class="block text-gray-700 font-semibold mb-2">Pilih Asrama</label>
        <select name="id_asrama" id="id_asrama"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2">
          <option value="">Pilih Asrama</option>
          <option value="1" {{ request('id_asrama') == '1' ? 'selected' : '' }}>Pniel</option>
          <option value="2" {{ request('id_asrama') == '2' ? 'selected' : '' }}>Kapernaum</option>
          <option value="3" {{ request('id_asrama') == '3' ? 'selected' : '' }}>Silo</option>
          <option value="7" {{ request('id_asrama') == '7' ? 'selected' : '' }}>Mamre</option>
          <option value="8" {{ request('id_asrama') == '8' ? 'selected' : '' }}>Nazareth</option>
          <option value="17" {{ request('id_asrama') == '17' ? 'selected' : '' }}>Kana</option>
          <option value="20" {{ request('id_asrama') == '20' ? 'selected' : '' }}>Jati</option>
        </select>

        <!-- Warning -->
        @if(empty(request('id_asrama')))
      <div class="mt-2 flex items-center text-yellow-600">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
        stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round"
          d="M13 16h-1v-4h-1m1-4h.01M21 16v-2a4 4 0 00-3-3.87M7.5 12H3a2 2 0 100 4h4.5m2 0H21a2 2 0 000-4H9.5m0 0L7 16m2-4V8m0 0h1m-1-2h.01" />
        </svg>
        <p class="text-sm">Silakan pilih asrama untuk mendapatkan data.</p>
      </div>
    @endif
      </div>

      <!-- Start Time -->
      <div>
        <label for="start_time" class="block text-gray-700 font-semibold mb-2">Start Time</label>
        <input type="date" name="start_time" id="start_time"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          value="{{ request('start_time') }}">
      </div>

      <!-- End Time -->
      <div>
        <label for="end_time" class="block text-gray-700 font-semibold mb-2">End Time</label>
        <input type="date" name="end_time" id="end_time"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          value="{{ request('end_time') }}">
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
      <h2 class="text-xl font-bold text-gray-800 mb-2">Asrama Summary</h2>
      @if(isset($data) && isset($data['result']) && $data['result'] == "OK")
      <p class="text-lg text-green-600 font-semibold">Jumlah Absen: {{ $data['data']['jumlah_absen'] ?? '0' }}</p>
      <p class="text-lg text-green-600 font-semibold">Jumlah Izin: {{ $data['data']['jumlah_izin'] ?? '0' }}</p>
      <p class="text-lg text-green-600 font-semibold">Jumlah Sakit: {{ $data['data']['jumlah_sakit'] ?? '0' }}</p>
    @else
      <p class="text-lg text-red-500 font-semibold">Data tidak tersedia.</p>
    @endif
    </div>
  </div>
</div>
@endsection