@extends('auth.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <!-- Absensi Asrama Section -->
  <div class="bg-white shadow rounded-lg p-6 mb-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Absensi Asrama</h1>

    <!-- Menampilkan Error Jika Ada -->
    @if(isset($errors) && count($errors) > 0)
    @foreach($errors as $error)
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
      {{ $error }}
    </div>
  @endforeach
  @endif

    <!-- Filter Form -->
    <form id="filterForm" method="GET"
      action="{{ route(Auth::check() ? 'absensi.asrama.auth' : 'absensi.asrama.public') }}" class="mb-4 space-y-4">

      <!-- Pilih Asrama -->
      <div>
        <label for="id_asrama" class="block text-gray-700 font-semibold mb-2">Pilih Asrama</label>
        <select name="id_asrama" id="id_asrama"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2">
          <option value="">Pilih Asrama</option>
          <option value="1" {{ old('id_asrama', $idAsrama ?? '') == '1' ? 'selected' : '' }}>Pniel</option>
          <option value="2" {{ old('id_asrama', $idAsrama ?? '') == '2' ? 'selected' : '' }}>Kapernaum</option>
          <option value="3" {{ old('id_asrama', $idAsrama ?? '') == '3' ? 'selected' : '' }}>Silo</option>
          <option value="7" {{ old('id_asrama', $idAsrama ?? '') == '7' ? 'selected' : '' }}>Mamre</option>
          <option value="8" {{ old('id_asrama', $idAsrama ?? '') == '8' ? 'selected' : '' }}>Nazareth</option>
          <option value="17" {{ old('id_asrama', $idAsrama ?? '') == '17' ? 'selected' : '' }}>Kana</option>
          <option value="20" {{ old('id_asrama', $idAsrama ?? '') == '20' ? 'selected' : '' }}>Jati</option>
        </select>

        <!-- Warning Jika Asrama Tidak Dipilih -->
        <div id="warning-message"
          class="mt-2 flex items-center text-yellow-600 {{ empty($idAsrama) ? 'flex' : 'hidden' }}">
          <p class="font-semibold">! &nbsp;</p>
          <p class="text-sm">Silakan pilih asrama untuk mendapatkan data.</p>
        </div>
      </div>

      <!-- Start Time -->
      <div>
        <label for="start_time" class="block text-gray-700 font-semibold mb-2">Dari tanggal:</label>
        <input type="date" name="start_time" id="start_time"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          value="{{ old('start_time', $startTime ?? '') }}">
      </div>

      <!-- End Time -->
      <div>
        <label for="end_time" class="block text-gray-700 font-semibold mb-2">Sampai tanggal:</label>
        <input type="date" name="end_time" id="end_time"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          value="{{ old('end_time', $endTime ?? '') }}">
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
    <div class="flex flex-row">
      <div class="mt-4 w-full">
        <!-- Header -->
        <h2 class="text-xl font-bold text-gray-800 mb-2">
          @if(empty($startTime) && empty($endTime))
        Absensi Asrama Sepanjang Waktu
      @elseif(!empty($startTime) && empty($endTime))
      Absensi Asrama Dari Tanggal {{ \Carbon\Carbon::parse($startTime)->format('d M Y') }}
    @elseif(empty($startTime) && !empty($endTime))
      Absensi Asrama Sampai Tanggal {{ \Carbon\Carbon::parse($endTime)->format('d M Y') }}
    @elseif(!empty($startTime) && !empty($endTime))
      Absensi Asrama Dari Tanggal {{ \Carbon\Carbon::parse($startTime)->format('d M Y') }} Sampai Tanggal
      {{ \Carbon\Carbon::parse($endTime)->format('d M Y') }}
    @endif
        </h2>

        <!-- Chart Absensi Asrama -->
        @if(isset($data) && isset($data['data']) && count($data['data']) > 0)
      <div class="flex justify-center mb-5">
        <div class="w-full" style="width: 80%;">
        <canvas id="absensiAsramaChart" style="width: 100%;"></canvas>
        </div>
      </div>
    @endif

        <!-- Display Jumlah Absen, Izin, Sakit -->
        @if(isset($data) && isset($data['result']) && $data['result'] == "OK")
      <p class="text-lg text-green-600 font-semibold">Jumlah Absen: {{ $data['data']['jumlah_absen'] ?? '0' }}</p>
      <p class="text-lg text-green-600 font-semibold">Jumlah Izin: {{ $data['data']['jumlah_izin'] ?? '0' }}</p>
      <p class="text-lg text-green-600 font-semibold">Jumlah Sakit: {{ $data['data']['jumlah_sakit'] ?? '0' }}</p>
    @else
    <p class="text-lg text-red-500 font-semibold">Data belum tersedia.</p>
  @endif
      </div>
    </div>
  </div>
</div>

<!-- Chart.js Library -->
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

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const selectAsrama = document.getElementById("id_asrama");
    const warningMessage = document.getElementById("warning-message");

    // Function to toggle the warning message
    const toggleWarning = () => {
      if (selectAsrama.value === "") {
        warningMessage.classList.remove("hidden");
        warningMessage.classList.add("flex");
      } else {
        warningMessage.classList.remove("flex");
        warningMessage.classList.add("hidden");
      }
    };

    // Attach event listener to the select element
    selectAsrama.addEventListener("change", toggleWarning);

    // Initial check when page loads
    toggleWarning();
  });
</script>
@endsection