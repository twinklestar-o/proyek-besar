@extends('auth.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold text-gray-800 mb-4">Absensi Kelas</h1>

  <!-- Filter Form -->
  <form id="filterForm" method="GET" action="{{ route(Auth::check() ? 'absens.kelas.auth' : 'absensi.kelas.public') }}"
    class="mb-4 space-y-4">
    <!-- Kode MK -->
    <div>
      <label for="kode_mk" class="block text-gray-700 font-semibold mb-2">Kode MK</label>
      <input type="text" name="kode_mk" id="kode_mk"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        required value="{{ request('kode_mk') }}">
    </div>
    <!-- ID Kurikulum -->
    <div>
      <label for="id_kur" class="block text-gray-700 font-semibold mb-2">ID Kurikulum</label>
      <input type="text" name="id_kur" id="id_kur"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        required value="{{ request('id_kur') }}">
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
    <!-- Minggu Ke -->
    <div>
      <label for="minggu_ke" class="block text-gray-700 font-semibold mb-2">Minggu Ke</label>
      <input type="number" name="minggu_ke" id="minggu_ke" min="1"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        value="{{ request('minggu_ke') }}">
    </div>
    <!-- Day -->
    <div>
      <label for="day" class="block text-gray-700 font-semibold mb-2">Day</label>
      <input type="number" name="day" id="day" min="1" max="31"
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

  <!-- Display Table -->
  <div class="mt-4">
    <h2 class="text-xl font-bold text-gray-800 mb-2">Absensi Data</h2>
    @if(isset($data) && isset($data['data']) && count($data['data']) > 0)
    <table class="min-w-full bg-white">
      <thead>
      <tr>
        <th class="py-2 px-4 border-b">No</th>
        <th class="py-2 px-4 border-b">Waktu Mulai</th>
        <th class="py-2 px-4 border-b">Waktu Akhir</th>
        <th class="py-2 px-4 border-b">Sesi</th>
        <th class="py-2 px-4 border-b">Lokasi</th>
        <th class="py-2 px-4 border-b">Total Mhs KRS</th>
        <th class="py-2 px-4 border-b">Total Mhs Hadir</th>
        <th class="py-2 px-4 border-b">Total Mhs Absen</th>
      </tr>
      </thead>
      <tbody>
      @foreach($data['data'] as $index => $item)
      <tr>
      <td class="py-2 px-4 border-b">{{ $index + 1 }}</td>
      <td class="py-2 px-4 border-b">{{ $item['waktu_mulai'] ?? 'N/A' }}</td>
      <td class="py-2 px-4 border-b">{{ $item['waktu_akhir'] ?? 'N/A' }}</td>
      <td class="py-2 px-4 border-b">{{ $item['sesi'] ?? 'N/A' }}</td>
      <td class="py-2 px-4 border-b">{{ $item['lokasi'] ?? 'N/A' }}</td>
      <td class="py-2 px-4 border-b">{{ $item['total_mhs_krs'] ?? 'N/A' }}</td>
      <td class="py-2 px-4 border-b">{{ $item['total_mhs_hadir'] ?? 'N/A' }}</td>
      <td class="py-2 px-4 border-b">{{ $item['total_mhs_absen'] ?? 'N/A' }}</td>
      </tr>
    @endforeach
      </tbody>
    </table>
  @else
  <p class="text-lg text-red-500 font-semibold">Data tidak tersedia.</p>
@endif
  </div>
</div>
@endsection