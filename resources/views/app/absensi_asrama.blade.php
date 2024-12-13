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
        <div id="warning-message"
          class="mt-2 flex items-center text-yellow-600 {{ empty(request('id_asrama')) ? '' : 'hidden' }}">
          <p class=font-semibold> ! &nbsp;</p>
          <p class="text-sm">Silakan pilih asrama untuk mendapatkan data.</p>
        </div>
      </div>


      <!-- Start Time -->
      <div>
        <label for="start_time" class="block text-gray-700 font-semibold mb-2">Dari tanggal : </label>
        <input type="date" name="start_time" id="start_time"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          value="{{ request('start_time') }}">
      </div>

      <!-- End Time -->
      <div>
        <label for="end_time" class="block text-gray-700 font-semibold mb-2">Sampai tanggal : </label>
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
    <div class="flex flex-row">
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
      <div class="sm:w-full d-flex content-center w-8">
        <canvas id="absensiAsramaChart"></canvas>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const barColors = ["royalblue", "royalblue", "royalblue"];
    const type = ["Absen", "Izin", "Sakit"];

    function updateChart() {
      const Absen = {{ $data['data']['jumlah_absen'] ?? '0' }};
      const Izin = {{ $data['data']['jumlah_izin'] ?? '0' }};
      const Sakit = {{ $data['data']['jumlah_sakit'] ?? '0' }};
      const jlhAbsensi = [Absen, Izin, Sakit];

      const maxValue = Math.max(...jlhAbsensi);
      const gap = maxValue;
      const yMax = maxValue + gap;

      if (absensiAsramaChart) {
        absensiAsramaChart.destroy();
      }

      absensiAsramaChart = new Chart("absensiAsramaChart", {
        type: "bar",
        data: {
          labels: type,
          datasets: [
            {
              label: 'Jumlah Mahasiswa yang Absen',
              backgroundColor: barColors,
              data: jlhAbsensi
            }
          ]
        },
        options: {
          plugins: {
            legend: { display: true },
            title: {
              display: true,
              text: "Jumlah Absensi"
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              max: yMax
            }
          }
        }
      });
    }

    let absensiAsramaChart;
    updateChart();
  });
</script>
@endsection