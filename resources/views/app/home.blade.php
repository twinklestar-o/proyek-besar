@extends('layouts.sidebar_admin')

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
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          onblur="document.getElementById('filterForm').submit()">
      </div>

      <!-- Dropdown for prodi -->
      <div>
        <label for="prodi" class="block text-gray-700 font-semibold mb-2">Filter by Prodi:</label>
        <select name="prodi" id="prodi"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          onchange="document.getElementById('filterForm').submit()">
          <option value="">Select Prodi</option>
          @foreach($prodiList as $prodi)
        <option value="{{ $prodi }}" {{ request('prodi') == $prodi ? 'selected' : '' }}>
        {{ $prodi }}
        </option>
      @endforeach
        </select>
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
</div>
@endsection