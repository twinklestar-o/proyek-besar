@extends('auth.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="bg-white shadow-md rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Total Mahasiswa Aktif</h1>

    <!-- Filter form for angkatan and prodi -->
    <form id="filterForm" method="GET" action="{{ route('home') }}" class="mb-4 space-y-4">

      <!-- Text input for angkatan -->
      <div>
        <label for="angkatan" class="block text-gray-700 font-semibold mb-2">Filter by Angkatan:</label>
        <input type="text" name="angkatan" id="angkatan" value="{{ request('angkatan') }}" placeholder="Enter Angkatan"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2">
      </div>

      <!-- Dropdown for prodi -->
      <div>
        <label for="prodi" class="block text-gray-700 font-semibold mb-2">Filter by Prodi:</label>
        <select name="prodi" id="prodi"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2">
          <option value="">Semua Prodi</option>
          @foreach($prodiList as $prodi)
        <option value="{{ $prodi }}" {{ request('prodi') == $prodi ? 'selected' : '' }}>
        {{ $prodi }}
        </option>
      @endforeach
        </select>
      </div>

      <!-- Filter button -->
      <div>
        <button type="submit"
          class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-200">
          Filter Data
        </button>
      </div>
    </form>

    <!-- Display the data -->
    @if(isset($totalMahasiswaAktif))
    <p class="text-lg text-green-600 font-semibold">
      Total Mahasiswa Aktif: {{ $totalMahasiswaAktif }}
    </p>
  @else
  <p class="text-lg text-red-500 font-semibold">
    Data tidak tersedia.
  </p>
@endif
  </div>

  <!-- Prestasi Section -->
  <div class="bg-white shadow-md rounded-lg p-6 mt-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Prestasi</h1>
    <p class="text-gray-600 mb-4">Lorem ipsum dolor sit amet consectetur adipisicing elit.</p>
    <form id="filterPrestasi" method="GET" action="{{ route('home') }}" class="mb-4 space-y-4">
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Filter by:</label>
        <input type="radio" id="tahun" name="waktu" value="tahun" checked>
        <label for="tahun">Tahun</label><br>
        <input type="radio" id="semester" name="waktu" value="semester">
        <label for="semester">Semester</label>
      </div>
    </form>
    <canvas id="prestasiTahun"></canvas>
    <canvas id="prestasiSemester" style="display: none;"></canvas>
  </div>

  <!-- Kegiatan Luar Kampus Section -->
  <div class="bg-white shadow-md rounded-lg p-6 mt-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Jumlah Mahasiswa yang Mengikuti Kegiatan di Luar Kampus</h1>
    <div class="space-y-4">
      <h5 class="font-bold text-gray-700">1. MBKM</h5>
      <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>

      <h5 class="font-bold text-gray-700">2. IISMA</h5>
      <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>

      <h5 class="font-bold text-gray-700">3. Kerja Praktik</h5>
      <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>

      <h5 class="font-bold text-gray-700">4. Studi Independent</h5>
      <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>

      <h5 class="font-bold text-gray-700">5. Pertukaran Pelajar</h5>
      <p class="text-gray-600">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
    </div>
    <canvas id="jlhMahasiswaKegiatanChart"></canvas>
  </div>
</div>
@endsection

@push('scripts')
  <script>
    // Include your chart initialization scripts here
  </script>
@endpush