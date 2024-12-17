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
    <!-- Editable Title -->
    <h1 class="text-2xl font-bold text-gray-800 mb-4 editable" data-section="pelanggaran_asrama" data-type="title">
      {!! $sections['pelanggaran_asrama']->title ?? 'Data Pelanggaran Asrama' !!}
    </h1>
    <!-- Editable Description -->
    <p class="text-gray-600 mb-4 editable" data-section="pelanggaran_asrama" data-type="description">
      {!! $sections['pelanggaran_asrama']->description ?? 'Deskripsi Default' !!}
    </p>

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
        <div id="chartTypeContainer" class="hidden mb-4">
        <label for="chartType" class="block text-gray-700 font-semibold mb-2">Pilih Jenis Chart:</label>
        <select id="chartType"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          data-section="pelanggaran_asrama" data-type="chart_type">
          <option value="bar" {{ ($sections['pelanggaran_asrama']->chart_type ?? 'bar') == 'bar' ? 'selected' : '' }}>
          Bar</option>
          <option value="line" {{ ($sections['pelanggaran_asrama']->chart_type ?? 'bar') == 'line' ? 'selected' : '' }}>Line</option>
          <option value="pie" {{ ($sections['pelanggaran_asrama']->chart_type ?? 'bar') == 'pie' ? 'selected' : '' }}>
          Pie</option>
        </select>
        </div>
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

<!-- Edit Section Script -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const editButton = document.getElementById("editButton");
    const editIcon = document.getElementById("editIcon");
    const editText = document.getElementById("editText");
    const editableElements = document.querySelectorAll(".editable");
    const chartTypeContainer = document.getElementById("chartTypeContainer");
    const chartTypeSelect = document.getElementById("chartType");
    let isEditing = false;

    const changes = {};

    editButton.addEventListener("click", () => {
      isEditing = !isEditing;

      editableElements.forEach((element) => {
        const sectionKey = element.getAttribute("data-section");
        const type = element.getAttribute("data-type");

        if (isEditing) {
          element.contentEditable = true;
          element.style.border = "1px dashed gray";

          if (!changes[sectionKey]) {
            changes[sectionKey] = {};
          }

          if (type === "title") {
            changes[sectionKey].originalTitle = element.innerHTML.trim();
          } else if (type === "description") {
            changes[sectionKey].originalDescription = element.innerHTML.trim();
          }
        } else {
          element.contentEditable = false;
          element.style.border = "none";

          if (type === "title") {
            const updatedTitle = element.innerHTML.trim();
            if (changes[sectionKey].originalTitle !== updatedTitle) {
              changes[sectionKey].updatedTitle = updatedTitle;
            }
          } else if (type === "description") {
            const updatedDescription = element.innerHTML.trim();
            if (changes[sectionKey].originalDescription !== updatedDescription) {
              changes[sectionKey].updatedDescription = updatedDescription;
            }
          }
        }
      });

      // Jika chart type container ada (chartnya sudah muncul), tampilkan/hilangkan saat edit
      if (chartTypeContainer) {
        chartTypeContainer.classList.toggle('hidden', !isEditing);
      }

      // Ganti ikon dan teks tombol
      editIcon.classList.toggle("bi-pencil", !isEditing);
      editIcon.classList.toggle("bi-check-circle", isEditing);
      editIcon.style.color = isEditing ? "green" : "orange";
      editText.textContent = isEditing ? "Done" : "Edit";
      editText.style.color = isEditing ? "green" : "orange";

      if (!isEditing) {
        // Simpan hanya perubahan
        saveChanges();
      }
    });

    // Handle Enter key untuk <br> saat editing
    editableElements.forEach((element) => {
      element.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
          e.preventDefault();
          const selection = window.getSelection();
          if (!selection.rangeCount) return;
          const range = selection.getRangeAt(0);

          const br = document.createElement("br");
          range.deleteContents();
          range.insertNode(br);

          range.setStartAfter(br);
          range.collapse(true);
          selection.removeAllRanges();
          selection.addRange(range);
        }
      });
    });

    // Listener untuk chartTypeSelect (jika ada)
    if (chartTypeSelect) {
      chartTypeSelect.addEventListener("change", function () {
        const sectionKey = this.getAttribute("data-section");
        const type = this.getAttribute("data-type");
        if (!changes[sectionKey]) {
          changes[sectionKey] = {};
        }
        changes[sectionKey].updatedChartType = this.value;
      });
    }

    function saveChanges() {
      const payload = {};

      // Kumpulkan hanya perubahan yang ada
      for (const sectionKey in changes) {
        payload[sectionKey] = {};

        if (changes[sectionKey].updatedTitle) {
          payload[sectionKey].title = changes[sectionKey].updatedTitle;
        }

        if (changes[sectionKey].updatedDescription) {
          payload[sectionKey].description = changes[sectionKey].updatedDescription;
        }

        if (changes[sectionKey].updatedChartType) {
          payload[sectionKey].chart_type = changes[sectionKey].updatedChartType;
        }

        // Jika tidak ada perubahan, hapus keynya
        if (Object.keys(payload[sectionKey]).length === 0) {
          delete payload[sectionKey];
        }
      }

      if (Object.keys(payload).length === 0) {
        Swal.fire("Info", "Tidak ada perubahan untuk disimpan.", "info");
        return;
      }

      // Kirim data ke server
      fetch("{{ route('sections.update') }}", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify(payload),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            Swal.fire("Sukses", data.message, "success").then(() => {
              location.reload();
            });
          } else {
            let errorMessages = '';
            for (const field in data.errors) {
              errorMessages += `${field}: ${data.errors[field].join(', ')}<br>`;
            }
            Swal.fire("Error", "Terjadi kesalahan:<br>" + errorMessages, "error");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire("Error", "Gagal menyimpan perubahan.", "error");
        });
    }
  });
</script>

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
        type: "{{ $sections['pelanggaran_asrama']->chart_type ?? 'bar' }}",
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