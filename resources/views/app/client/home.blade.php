@extends('layouts.sidebar.sidebar_admin')

@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="bg-white shadow-md rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Total Mahasiswa Aktif</h1>
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