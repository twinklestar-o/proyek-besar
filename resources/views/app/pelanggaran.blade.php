@extends('auth.app')

@section('content')
@php
  // Definisikan mapping id_asrama ke nama_asrama
  $asramas = [
    '1' => 'Pniel',
    '2' => 'Kapernaum',
    '3' => 'Silo',
    '7' => 'Mamre',
    '8' => 'Nazareth',
    '17' => 'Kana',
    '20' => 'Jati',
  ];
@endphp

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
          <option value="1" {{ (isset($filters['id_asrama']) && $filters['id_asrama'] == '1') ? 'selected' : '' }}>Pniel
          </option>
          <option value="2" {{ (isset($filters['id_asrama']) && $filters['id_asrama'] == '2') ? 'selected' : '' }}>
            Kapernaum</option>
          <option value="3" {{ (isset($filters['id_asrama']) && $filters['id_asrama'] == '3') ? 'selected' : '' }}>Silo
          </option>
          <option value="7" {{ (isset($filters['id_asrama']) && $filters['id_asrama'] == '7') ? 'selected' : '' }}>Mamre
          </option>
          <option value="8" {{ (isset($filters['id_asrama']) && $filters['id_asrama'] == '8') ? 'selected' : '' }}>
            Nazareth</option>
          <option value="17" {{ (isset($filters['id_asrama']) && $filters['id_asrama'] == '17') ? 'selected' : '' }}>Kana
          </option>
          <option value="20" {{ (isset($filters['id_asrama']) && $filters['id_asrama'] == '20') ? 'selected' : '' }}>Jati
          </option>
        </select>

        <!-- Warning -->
        <div id="warning-message"
          class="mt-2 flex items-center text-yellow-600 {{ empty($filters['id_asrama']) ? '' : 'hidden' }}">
          <p class="font-semibold"> ! &nbsp;</p>
          <p class="text-sm">Silakan pilih asrama untuk mendapatkan data.</p>
        </div>
      </div>

      <!-- Tingkat Pelanggaran -->
      <div>
        <label for="tingkat_pelanggaran" class="block text-gray-700 font-semibold mb-2">Tingkat Pelanggaran</label>
        <select name="tingkat_pelanggaran" id="tingkat_pelanggaran"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2">
          <option value="">Semua Tingkat</option>
          <option value="1" {{ (isset($filters['tingkat_pelanggaran']) && $filters['tingkat_pelanggaran'] == '1') ? 'selected' : '' }}>Ringan Level I (Poin 1-5)</option>
          <option value="2" {{ (isset($filters['tingkat_pelanggaran']) && $filters['tingkat_pelanggaran'] == '2') ? 'selected' : '' }}>Ringan Level II (Poin 6-10)</option>
          <option value="3" {{ (isset($filters['tingkat_pelanggaran']) && $filters['tingkat_pelanggaran'] == '3') ? 'selected' : '' }}>Sedang Level I (Poin 11-15)</option>
          <option value="4" {{ (isset($filters['tingkat_pelanggaran']) && $filters['tingkat_pelanggaran'] == '4') ? 'selected' : '' }}>Sedang Level II (Poin 16-24)</option>
          <option value="5" {{ (isset($filters['tingkat_pelanggaran']) && $filters['tingkat_pelanggaran'] == '5') ? 'selected' : '' }}>Berat Level I (Poin 25-30)</option>
          <option value="6" {{ (isset($filters['tingkat_pelanggaran']) && $filters['tingkat_pelanggaran'] == '6') ? 'selected' : '' }}>Berat Level II (Poin 31-75)</option>
          <option value="7" {{ (isset($filters['tingkat_pelanggaran']) && $filters['tingkat_pelanggaran'] == '7') ? 'selected' : '' }}>Berat Level III (Poin >=76)</option>
        </select>
      </div>

      <!-- Start Date -->
      <div>
        <label for="start_date" class="block text-gray-700 font-semibold mb-2">Dari tanggal : </label>
        <input type="date" name="start_date" id="start_date"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          value="{{ $filters['start_date'] ?? '' }}">
      </div>

      <!-- End Date -->
      <div>
        <label for="end_date" class="block text-gray-700 font-semibold mb-2">Sampai tanggal : </label>
        <input type="date" name="end_date" id="end_date"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          value="{{ $filters['end_date'] ?? '' }}">
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
      <h2 class="text-xl font-bold text-gray-800 mb-2">Data Pelanggaran</h2>

      <!-- Chart Pelanggaran -->
      @if(isset($data) && isset($data['data']) && count($data['data']) > 0)
      <div class="flex justify-center mb-5">
      <div class="w-full" style="width: 80%;">
        <canvas id="pelanggaranChart" style=" width: 100%;"></canvas>
      </div>
      </div>
    @endif

      @if(isset($data) && isset($data['result']) && $data['result'] == 'OK')
      <h2 class="text-lg font-bold text-gray-800">Data Pelanggaran</h2>
      <p class="text-md text-green-600">
      <strong>Asrama:</strong> {{ $asramas[$filters['id_asrama']] ?? '-' }}
      </p>

      @if(empty($filters['tingkat_pelanggaran']))
      <h3 class="text-md font-bold text-gray-800 mt-4">Data Pelanggaran per Tingkat</h3>
      <ul class="list-disc ml-6 text-green-600">
      @if(isset($data['data']['pelanggaran_per_level']) && is_array($data['data']['pelanggaran_per_level']))
      @foreach($data['data']['pelanggaran_per_level'] as $level => $total)
      <li>
      {{ $tingkatPelanggaranLabels[$level] ?? 'Unknown Level' }}:
      @if($total !== null)
      {{ $total }} pelanggaran
    @else
      <span class="text-red-500">Data tidak tersedia.</span>
    @endif
      </li>
    @endforeach
    @else
      <li class="text-red-500">Data pelanggaran per level tidak tersedia.</li>
    @endif
      </ul>
      <p class="text-md text-green-600 mt-2">
      <strong>Total Keseluruhan:</strong> {{ $data['data']['total_keseluruhan'] ?? '-' }}
      </p>
    @else
      <p class="text-md text-green-600">
      <strong>Total Pelanggaran:</strong> {{ $data['data']['total_pelanggaran'] ?? '-' }}
      </p>
    @endif
    @else
      <p class="text-lg text-red-500 font-semibold">
      Data belum tersedia.
      </p>
    @endif
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const asramaDropdown = document.getElementById('id_asrama');
    const warningMessage = document.getElementById('warning-message');

    // Listen for changes in the dropdown
    asramaDropdown.addEventListener('change', function () {
      if (asramaDropdown.value) {
        warningMessage.classList.add('hidden');
      } else {
        warningMessage.classList.remove('hidden');
      }
    });
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const barColors = ["blue", "orange", "red"];
    const type = ["Ringan Level I", "Ringan Level II", "Sedang Level I", "Sedang Level II", "Berat Level I", "Berat Level II", "Berat Level III"];

    function updateChart() {
      const ringanLevel1 = {{ $data['data']['pelanggaran_per_level'][1] ?? 0 }};
      const ringanLevel2 = {{ $data['data']['pelanggaran_per_level'][2] ?? 0 }};
      const sedangLevel1 = {{ $data['data']['pelanggaran_per_level'][3] ?? 0 }};
      const sedangLevel2 = {{ $data['data']['pelanggaran_per_level'][4] ?? 0 }};
      const beratLevel1 = {{ $data['data']['pelanggaran_per_level'][5] ?? 0 }};
      const beratLevel2 = {{ $data['data']['pelanggaran_per_level'][6] ?? 0 }};
      const beratLevel3 = {{ $data['data']['pelanggaran_per_level'][7] ?? 0 }};

      const jlhPelanggaran = [ringanLevel1, ringanLevel2, sedangLevel1, sedangLevel2, beratLevel1, beratLevel2, beratLevel3];

      const maxValue = Math.max(...jlhPelanggaran);
      const yMax = maxValue + 2;

      if (pelanggaranChart) {
        pelanggaranChart.destroy();
      }

      pelanggaranChart = new Chart("pelanggaranChart", {
        type: "bar",
        data: {
          labels: type,
          datasets: [
            {
              label: 'Jumlah Pelanggaran',
              backgroundColor: barColors,
              data: jlhPelanggaran
            }
          ]
        },
        options: {
          plugins: {
            legend: { display: true },
            title: {
              display: true,
              text: "Data Pelanggaran Asrama"
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

    let pelanggaranChart;
    if ({{ isset($data) && isset($data['data']) && count($data['data']) > 0 ? 'true' : 'false' }}) {
      updateChart();
    }
  });
</script>

@endsection