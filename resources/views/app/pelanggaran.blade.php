@extends('auth.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <!-- Pelanggaran Section -->
  <div class="bg-white shadow rounded-lg p-6 mb-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Data Pelanggaran Asrama</h1>

    <!-- Filter Form -->
    <form id="filterForm" method="GET" action="{{ route(Auth::check() ? 'pelanggaran.auth' : 'pelanggaran.public') }}"
      class="mb-4 space-y-4">
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
      </div>

      <!-- Tingkat Pelanggaran -->
      <div>
        <label for="tingkat_pelanggaran" class="block text-gray-700 font-semibold mb-2">Tingkat Pelanggaran</label>
        <select name="tingkat_pelanggaran" id="tingkat_pelanggaran"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2">
          <option value="">Pilih Tingkat Pelanggaran</option>
          <option value="1" {{ request('tingkat_pelanggaran') == '1' ? 'selected' : '' }}>Ringan Level I (Poin 1-5)
          </option>
          <option value="2" {{ request('tingkat_pelanggaran') == '2' ? 'selected' : '' }}>Ringan Level II (Poin 6-10)
          </option>
          <option value="3" {{ request('tingkat_pelanggaran') == '3' ? 'selected' : '' }}>Sedang Level I (Poin 11-15)
          </option>
          <option value="4" {{ request('tingkat_pelanggaran') == '4' ? 'selected' : '' }}>Sedang Level II (Poin 16-24)
          </option>
          <option value="5" {{ request('tingkat_pelanggaran') == '5' ? 'selected' : '' }}>Berat Level I (Poin 25-30)
          </option>
          <option value="6" {{ request('tingkat_pelanggaran') == '6' ? 'selected' : '' }}>Berat Level II (Poin 31-75)
          </option>
          <option value="7" {{ request('tingkat_pelanggaran') == '7' ? 'selected' : '' }}>Berat Level III (Poin >=76)
          </option>
        </select>
      </div>

      <!-- Start Date -->
      <div>
        <label for="start_date" class="block text-gray-700 font-semibold mb-2">Start Date</label>
        <input type="date" name="start_date" id="start_date"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          value="{{ request('start_date') }}">
      </div>

      <!-- End Date -->
      <div>
        <label for="end_date" class="block text-gray-700 font-semibold mb-2">End Date</label>
        <input type="date" name="end_date" id="end_date"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          value="{{ request('end_date') }}">
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
      @if(isset($data) && isset($data['result']) && $data['result'] == 'OK')
      <h2 class="text-lg font-bold text-gray-800">Data Pelanggaran</h2>
      <p class="text-md text-gray-700"><strong>Asrama:</strong> {{ $data['data']['nama_asrama'] }}</p>
      <p class="text-md text-gray-700"><strong>Total Pelanggaran:</strong> {{ $data['data']['total_pelanggaran'] }}</p>
    @else
      <p class="text-lg text-red-500 font-semibold">Data tidak tersedia atau gagal diambil.</p>
    @endif
    </div>
  </div>
</div>
@endsection