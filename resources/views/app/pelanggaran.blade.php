@extends('auth.app')

@section('content')
@php
  $asramas = [
    '1' => 'Pniel',
    '2' => 'Kapernaum',
    '3' => 'Silo',
    '7' => 'Mamre',
    '8' => 'Nazareth',
    '17' => 'Kana',
    '20' => 'Jati',
  ];

  // Definisikan label tingkat pelanggaran jika belum didefinisikan
  $tingkatPelanggaranLabels = [
    '1' => 'Ringan Level I (Poin 1-5)',
    '2' => 'Ringan Level II (Poin 6-10)',
    '3' => 'Sedang Level I (Poin 11-15)',
    '4' => 'Sedang Level II (Poin 16-24)',
    '5' => 'Berat Level I (Poin 25-30)',
    '6' => 'Berat Level II (Poin 31-75)',
    '7' => 'Berat Level III (Poin >=76)',
  ];
@endphp

<div class="container mx-auto px-4 py-8">
  <!-- Pelanggaran Section -->
  <div class="relative group bg-white shadow rounded-lg p-6 mb-8">
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
      <div>
        <label for="id_asrama" class="block text-gray-700 font-semibold mb-2">Pilih Asrama</label>
        <select name="id_asrama" id="id_asrama"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2">
          <option value="">Pilih Asrama</option>
          @foreach($asramas as $id => $nama)
        <option value="{{ $id }}" {{ (isset($filters['id_asrama']) && $filters['id_asrama'] == $id) ? 'selected' : '' }}>{{ $nama }}</option>
      @endforeach
        </select>

        <div id="warning-message"
          class="mt-2 flex items-center text-yellow-600 {{ empty($filters['id_asrama']) ? '' : 'hidden' }}">
          <p class="font-semibold"> ! &nbsp;</p>
          <p class="text-sm">Silakan pilih asrama untuk mendapatkan data.</p>
        </div>
      </div>

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

      <div>
        <label for="start_date" class="block text-gray-700 font-semibold mb-2">Dari tanggal :</label>
        <input type="date" name="start_date" id="start_date"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          value="{{ $filters['start_date'] ?? '' }}">
      </div>

      <div>
        <label for="end_date" class="block text-gray-700 font-semibold mb-2">Sampai tanggal :</label>
        <input type="date" name="end_date" id="end_date"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          value="{{ $filters['end_date'] ?? '' }}">
      </div>

      <div>
        <button type="submit"
          class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 focus:outline-none focus:ring focus:ring-indigo-200">
          Ambil Data
        </button>
      </div>
    </form>

    <div class="mt-4">
      <h2 class="text-xl font-bold text-gray-800 mb-2">Data Pelanggaran</h2>

      @if(isset($data) && isset($data['data']) && count($data['data']) > 0)
      <div class="flex flex-col items-center mb-5">
      <!-- Dropdown Jenis Chart -->
      <div id="chartTypeContainer" class="mb-4 w-full sm:w-80 hidden"> <!-- Added 'hidden' class -->
        <label for="chartType" class="block text-gray-700 font-semibold mb-2">Pilih Jenis Chart:</label>
        <select id="chartType"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        data-section="pelanggaran_asrama" data-type="chart_type">
        <option value="bar" {{ ($sections['pelanggaran_asrama']->chart_type ?? 'bar') == 'bar' ? 'selected' : '' }}>
          Bar</option>
        <option value="line" {{ ($sections['pelanggaran_asrama']->chart_type ?? 'bar') == 'line' ? 'selected' : '' }}>
          Line</option>
        <option value="pie" {{ ($sections['pelanggaran_asrama']->chart_type ?? 'bar') == 'pie' ? 'selected' : '' }}>
          Pie</option>
        </select>
      </div>


      <!-- Kontainer Chart -->
      <div id="chartContainerPelanggaran" class="w-full sm:w-80 transition-all duration-500">
        <canvas id="pelanggaranChart" style="width: 100%;"></canvas>
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

    <!-- Tombol "+" di bagian bawah container utama -->
    <button
      class="absolute left-1/2 -translate-x-1/2 bottom-0 mb-[-16px] bg-green-500 text-white rounded-full w-8 h-8 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 hover:bg-green-600 hidden"
      id="addSectionButton" title="Tambah Section Baru">
      +
    </button>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- jQuery (untuk AJAX) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- SweetAlert2 Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- TinyMCE Inline Editor -->
<script src="https://cdn.jsdelivr.net/npm/tinymce@5/tinymce.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const asramaDropdown = document.getElementById('id_asrama');
    const warningMessage = document.getElementById('warning-message');

    asramaDropdown.addEventListener('change', function () {
      if (asramaDropdown.value) {
        warningMessage.classList.add('hidden');
      } else {
        warningMessage.classList.remove('hidden');
      }
    });
  });
</script>

<script>
  let editableElements;
  let isEditing = false;
  const changes = {};

  document.addEventListener("DOMContentLoaded", function () {
    const editButton = document.getElementById("editButton");
    const editIcon = document.getElementById("editIcon");
    const editText = document.getElementById("editText");
    const chartTypeContainer = document.getElementById("chartTypeContainer");
    const chartTypeSelect = document.getElementById("chartType");
    const addSectionButton = document.getElementById('addSectionButton');

    // Inisialisasi TinyMCE Inline untuk element yang sudah ada
    initTinyMCEInline();
    updateEditableElements();

    editButton.addEventListener("click", () => {
      isEditing = !isEditing;

      editableElements.forEach((element) => {
        const sectionKey = element.getAttribute("data-section");
        const type = element.getAttribute("data-type");

        if (isEditing) {
          element.style.border = "1px dashed gray";
          if (!changes[sectionKey]) {
            changes[sectionKey] = {};
          }
          // Simpan original content
          if (typeof changes[sectionKey].originalTitle === 'undefined' && type === "title") {
            changes[sectionKey].originalTitle = element.innerHTML.trim();
          }
          if (typeof changes[sectionKey].originalDescription === 'undefined' && type === "description") {
            changes[sectionKey].originalDescription = element.innerHTML.trim();
          }
        } else {
          element.style.border = "none";
          // Cek perubahan
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

      // Tampilkan/hilangkan chartTypeContainer saat edit
      if (chartTypeContainer) {
        chartTypeContainer.classList.toggle('hidden', !isEditing);
      }

      // Tampilkan/hilangkan tombol "+" untuk menambah section baru
      if (addSectionButton) {
        if (isEditing) {
          addSectionButton.classList.remove('hidden');
        } else {
          addSectionButton.classList.add('hidden');
        }
      }

      editIcon.classList.toggle("bi-pencil", !isEditing);
      editIcon.classList.toggle("bi-check-circle", isEditing);
      editIcon.style.color = isEditing ? "green" : "orange";
      editText.textContent = isEditing ? "Done" : "Edit";
      editText.style.color = isEditing ? "green" : "orange";

      if (!isEditing) {
        // Simpan perubahan saat mode edit dimatikan
        saveChanges();
      }
    });

    // Handler saat tombol "+" ditekan
    addSectionButton.addEventListener('click', function () {
      const newSectionKey = 'new_section_' + Date.now();

      const newSectionContainer = document.createElement('div');
      newSectionContainer.className = 'bg-white shadow rounded-lg p-6 mb-8 relative group';

      // Title
      const newTitle = document.createElement('h2');
      newTitle.className = 'text-2xl font-bold text-gray-800 mb-4 editable';
      newTitle.setAttribute('data-section', newSectionKey);
      newTitle.setAttribute('data-type', 'title');
      newTitle.innerHTML = 'New Section Title';

      // Description
      const newDescription = document.createElement('div');
      newDescription.className = 'text-gray-600 mb-4 editable';
      newDescription.setAttribute('data-section', newSectionKey);
      newDescription.setAttribute('data-type', 'description');
      newDescription.innerHTML = 'Masukkan teks disini...';

      newSectionContainer.appendChild(newTitle);
      newSectionContainer.appendChild(newDescription);

      // Tambahkan ke DOM
      addSectionButton.parentElement.parentElement.appendChild(newSectionContainer);

      // Register original content untuk section baru
      changes[newSectionKey] = {
        originalTitle: 'New Section Title',
        originalDescription: 'Masukkan teks disini...'
      };

      // Re-init TinyMCE inline pada elemen baru
      initTinyMCEInline();
      updateEditableElements();
    });

    if (chartTypeSelect) {
      chartTypeSelect.addEventListener("change", function () {
        const sectionKey = this.getAttribute("data-section");
        if (!changes[sectionKey]) {
          changes[sectionKey] = {};
        }
        changes[sectionKey].updatedChartType = this.value;
      });
    }

    function initTinyMCEInline() {
      // Hapus semua instance agar reinit bersih
      tinymce.remove();

      tinymce.init({
        selector: '.editable',
        inline: true,
        menubar: false,
        plugins: 'link lists',
        toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link'
      });
    }

    function updateEditableElements() {
      editableElements = document.querySelectorAll(".editable");
    }

    function saveChanges() {
      const payloadUpdate = {};
      const payloadNewSections = [];

      for (const sectionKey in changes) {
        const data = changes[sectionKey];
        let updatedTitle = data.updatedTitle || data.originalTitle;
        let updatedDescription = data.updatedDescription || data.originalDescription;

        // Cek apakah ini section baru
        if (sectionKey.startsWith('new_section_')) {
          // Section baru
          payloadNewSections.push({
            section: sectionKey,
            title: updatedTitle,
            description: updatedDescription,
            chart_type: 'bar' // Default chart type
          });
        } else {
          // Section existing
          let sectionPayload = {};
          if (data.updatedTitle) {
            sectionPayload.title = data.updatedTitle;
          }
          if (data.updatedDescription) {
            sectionPayload.description = data.updatedDescription;
          }
          if (data.updatedChartType) {
            sectionPayload.chart_type = data.updatedChartType;
          }

          if (Object.keys(sectionPayload).length > 0) {
            payloadUpdate[sectionKey] = sectionPayload;
          }
        }
      }

      if (Object.keys(payloadUpdate).length === 0 && payloadNewSections.length === 0) {
        Swal.fire("Info", "Tidak ada perubahan untuk disimpan.", "info");
        return;
      }

      const saveNewSections = () => {
        if (payloadNewSections.length === 0) {
          return Promise.resolve();
        }

        // Simpan section baru satu per satu
        const requests = payloadNewSections.map(sectionData => {
          return fetch("{{ route('sections.store') }}", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            },
            body: JSON.stringify(sectionData)
          })
            .then(res => res.json())
            .then(data => {
              if (data.status !== 'success') {
                throw new Error(data.message || 'Gagal menyimpan section baru.');
              }
            });
        });

        return Promise.all(requests);
      };

      const updateExistingSections = () => {
        if (Object.keys(payloadUpdate).length === 0) {
          return Promise.resolve();
        }

        return fetch("{{ route('sections.update') }}", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
          },
          body: JSON.stringify(payloadUpdate),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.status !== "success") {
              let errorMessages = '';
              for (const field in data.errors) {
                errorMessages += `${field}: ${data.errors[field].join(', ')}<br>`;
              }
              throw new Error("Terjadi kesalahan:<br>" + errorMessages);
            }
          });
      };

      saveNewSections()
        .then(() => updateExistingSections())
        .then(() => {
          Swal.fire("Sukses", "Perubahan berhasil disimpan.", "success").then(() => {
            location.reload();
          });
        })
        .catch((error) => {
          console.error("Error:", error);
          Swal.fire("Error", error.message, "error");
        });
    }

  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const barColors = ["#074799", "orange", "#E32227", "green", "purple", "teal", "gray"];
    const typeLabels = ["Ringan Level I", "Ringan Level II", "Sedang Level I", "Sedang Level II", "Berat Level I", "Berat Level II", "Berat Level III"];

    function updateChart(chartType) {
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

      if (typeof pelanggaranChart !== 'undefined' && pelanggaranChart) {
        pelanggaranChart.destroy();
      }

      // Referensi ke kontainer chart
      const chartContainerPelanggaran = document.getElementById("chartContainerPelanggaran");

      // Sesuaikan ukuran kontainer berdasarkan jenis chart
      if (chartType === 'pie') {
        chartContainerPelanggaran.style.width = "60%"; // Perkecil lebar untuk pie chart
        chartContainerPelanggaran.style.height = "400px"; // Perkecil tinggi jika diperlukan
      } else {
        chartContainerPelanggaran.style.width = "80%"; // Kembali ke lebar semula
        chartContainerPelanggaran.style.height = "500px"; // Kembali ke tinggi semula
      }

      pelanggaranChart = new Chart("pelanggaranChart", {
        type: chartType,
        data: {
          labels: typeLabels,
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
          scales: chartType === 'pie' ? {} : { // Hilangkan skala untuk pie chart
            y: {
              beginAtZero: true,
              max: yMax,
              grid: {
                display: true
              }
            },
            x: {
              grid: {
                display: true
              }
            }
          },
          maintainAspectRatio: false // Agar bisa mengontrol tinggi chart
        }
      });
    }

    let pelanggaranChart;
    const chartTypeSelect = document.getElementById("chartType");

    if ({{ isset($data) && isset($data['data']) && count($data['data']) > 0 ? 'true' : 'false' }}) {
      const initialChartType = "{{ $sections['pelanggaran_asrama']->chart_type ?? 'bar' }}";
      updateChart(initialChartType);
    }

    if (chartTypeSelect) {
      chartTypeSelect.addEventListener("change", function () {
        const selectedChartType = this.value;
        updateChart(selectedChartType);
      });
    }
  });
</script>

<style>
  /* Transisi untuk perubahan ukuran chart */
  #chartContainerPelanggaran {
    transition: width 0.5s ease, height 0.5s ease;
  }

  /* Tampilkan tombol "+" saat grup di-hover */
  .group:hover #addSectionButton {
    opacity: 1 !important;
  }

  #addSectionButton {
    transition: opacity 0.3s ease;
  }

  /* Pastikan chartTypeContainer tidak menumpuk tabel data */
  #chartTypeContainer {
    z-index: 10;
    /* Sesuaikan jika diperlukan */
  }
</style>

@endsection