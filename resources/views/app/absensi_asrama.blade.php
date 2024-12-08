@extends('auth.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold text-gray-800 mb-4">Absensi Asrama</h1>

  <!-- Filter Form -->
  <form id="filterForm" method="GET"
    action="{{ route(Auth::check() ? 'absensi.asrama.auth' : 'absensi.asrama.public') }}" class="mb-4 space-y-4">
    <div>
      <label for="id_asrama" class="block text-gray-700 font-semibold mb-2">Pilih Asrama</label>
      <select name="id_asrama" id="id_asrama"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        required>
        <option value="">Semua Asrama</option>
        <option value="1" {{ request('id_asrama') == '1' ? 'selected' : '' }}>Pniel</option>
        <option value="2" {{ request('id_asrama') == '2' ? 'selected' : '' }}>Kapernaum</option>
        <option value="3" {{ request('id_asrama') == '3' ? 'selected' : '' }}>Silo</option>
        <option value="7" {{ request('id_asrama') == '7' ? 'selected' : '' }}>Mamre</option>
        <option value="8" {{ request('id_asrama') == '8' ? 'selected' : '' }}>Nazareth</option>
        <option value="17" {{ request('id_asrama') == '17' ? 'selected' : '' }}>Kana</option>
        <option value="20" {{ request('id_asrama') == '20' ? 'selected' : '' }}>Jati</option>
      </select>
    </div>
    <div>
      <label for="start_time" class="block text-gray-700 font-semibold mb-2">Start Time</label>
      <input type="date" name="start_time" id="start_time"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        value="{{ request('start_time') }}">
    </div>
    <div>
      <label for="end_time" class="block text-gray-700 font-semibold mb-2">End Time</label>
      <input type="date" name="end_time" id="end_time"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        value="{{ request('end_time') }}">
    </div>
    <div>
      <label for="day" class="block text-gray-700 font-semibold mb-2">Day</label>
      <input type="number" name="day" id="day" min="1" max="31"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        value="{{ request('day') }}">
    </div>
    <div>
      <label for="month" class="block text-gray-700 font-semibold mb-2">Month</label>
      <input type="number" name="month" id="month" min="1" max="12"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        value="{{ request('month') }}">
    </div>
    <div>
      <label for="year" class="block text-gray-700 font-semibold mb-2">Year</label>
      <input type="number" name="year" id="year" min="2000" max="2099"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        value="{{ request('year') }}">
    </div>
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
    <p><strong>Jumlah Absen:</strong> {{ $data['data']['jumlah_absen'] ?? '0' }}</p>
    <p><strong>Jumlah Izin:</strong> {{ $data['data']['jumlah_izin'] ?? '0' }}</p>
    <p><strong>Jumlah Sakit:</strong> {{ $data['data']['jumlah_sakit'] ?? '0' }}</p>
  @else
  <p class="text-lg text-red-500 font-semibold">Data tidak tersedia.</p>
@endif
  </div>
</div>
@endsection