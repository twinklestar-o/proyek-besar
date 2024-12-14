@extends('auth.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <!-- Log Mahasiswa Section -->
  <div class="bg-white shadow rounded-lg p-6 mb-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Log Keluar/Masuk Mahasiswa</h1>

    <!-- Filter Form -->
    <form id="filterForm" method="GET"
      action="{{ route(Auth::check() ? 'log.mahasiswa.auth' : 'log.mahasiswa.public') }}" class="mb-4 space-y-6">

      <!-- Mahasiswa Masuk -->
      <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Log Masuk</h2>
        <!-- Start Masuk -->
        <div>
          <label for="start_masuk" class="block text-gray-700 font-semibold mb-2">Dari tanggal : </label>
          <input type="date" name="start_masuk" id="start_masuk"
            class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
            value="{{ old('start_masuk', $startMasuk ?? '') }}">
        </div>

        <!-- End Masuk -->
        <div>
          <label for="end_masuk" class="block text-gray-700 font-semibold mb-2">Sampai tanggal : </label>
          <input type="date" name="end_masuk" id="end_masuk"
            class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
            value="{{ old('end_masuk', $endMasuk ?? '') }}">
        </div>
      </div>

      <!-- Mahasiswa Keluar -->
      <div>
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Log Keluar</h2>
        <!-- Start Keluar -->
        <div>
          <label for="start_keluar" class="block text-gray-700 font-semibold mb-2">Dari tanggal : </label>
          <input type="date" name="start_keluar" id="start_keluar"
            class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
            value="{{ old('start_keluar', $startKeluar ?? '') }}">
        </div>

        <!-- End Keluar -->
        <div>
          <label for="end_keluar" class="block text-gray-700 font-semibold mb-2">Sampai tanggal : </label>
          <input type="date" name="end_keluar" id="end_keluar"
            class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
            value="{{ old('end_keluar', $endKeluar ?? '') }}">
        </div>
      </div>

      <!-- Fetch Data Button -->
      <div>
        <button type="submit"
          class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-200">
          Ambil Data
        </button>
      </div>
    </form>

    <!-- Display Data -->
    <div class="mt-4">
      @if(isset($dataMasuk) || isset($dataKeluar))
      @if($dataMasuk && $dataMasuk['result'] === 'OK')
      <p class="text-lg text-green-600 font-semibold">
      Total Log Masuk: {{ $dataMasuk['total'] ?? '0' }}
      </p>
      @if(isset($dataMasuk['logs']) && is_array($dataMasuk['logs']))
      <div class="mt-4">
      <h3 class="text-lg font-semibold text-gray-800 mb-2">Detail Logs Masuk:</h3>
      <ul class="list-disc list-inside">
      @foreach($dataMasuk['logs'] as $log)
      <li class="text-green-600">
      <strong>{{ $log['name'] ?? 'N/A' }}</strong> - Masuk: {{ $log['masuk'] ?? 'N/A' }}
      </li>
    @endforeach
      </ul>
      </div>
    @endif
    @elseif($dataMasuk && $dataMasuk['result'] === 'FAILED')
      <p class="text-lg text-red-500 font-semibold">{{ $dataMasuk['status'] ?? 'Gagal mengambil data masuk' }}</p>
    @endif

      @if($dataKeluar && $dataKeluar['result'] === 'OK')
      <p class="text-lg text-blue-600 font-semibold mt-6">
      Total Log Keluar: {{ $dataKeluar['total'] ?? '0' }}
      </p>
      @if(isset($dataKeluar['logs']) && is_array($dataKeluar['logs']))
      <div class="mt-4">
      <h3 class="text-lg font-semibold text-gray-800 mb-2">Detail Logs Keluar:</h3>
      <ul class="list-disc list-inside">
      @foreach($dataKeluar['logs'] as $log)
      <li class="text-blue-600">
      <strong>{{ $log['name'] ?? 'N/A' }}</strong> - Keluar: {{ $log['keluar'] ?? 'N/A' }}
      </li>
    @endforeach
      </ul>
      </div>
    @endif
    @elseif($dataKeluar && $dataKeluar['result'] === 'FAILED')
      <p class="text-lg text-red-500 font-semibold">{{ $dataKeluar['status'] ?? 'Gagal mengambil data keluar' }}</p>
    @endif
    @else
      @if(isset($errors) && count($errors) > 0)
      @foreach($errors as $error)
      <p class="text-lg text-red-500 font-semibold">{{ $error }}</p>
    @endforeach
    @else
      <p class="text-lg text-red-500 font-semibold">Data belum tersedia.</p>
      <p class="text-sm text-gray-500">Tips: Pastikan Anda mengisi setidaknya satu filter untuk mendapatkan data log.
      </p>
    @endif
    @endif
    </div>
  </div>
</div>
@endsection