@extends('auth.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <!-- Log Mahasiswa Section -->
  <div class="bg-white shadow rounded-lg p-6 mb-8 relative group">
    <h1 class="text-2xl font-bold text-gray-800 mb-4 editable" data-section="log_keluar_masuk_mahasiswa"
      data-type="title">
      {!! $sections['log_keluar_masuk_mahasiswa']->title ?? 'Log Keluar/Masuk Mahasiswa' !!}
    </h1>
    <p class="text-gray-600 mb-4 editable" data-section="log_keluar_masuk_mahasiswa" data-type="description">
      {!! $sections['log_keluar_masuk_mahasiswa']->description ?? 'Deskripsi Default' !!}
    </p>

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
          class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 focus:outline-none focus:ring focus:ring-indigo-200">
          Ambil Data
        </button>
      </div>
    </form>

    @php
    // Cek apakah ada data yang OK
    $showChart = false;
    if ((isset($dataMasuk) && $dataMasuk['result'] === 'OK') || (isset($dataKeluar) && $dataKeluar['result'] === 'OK')) {
      $showChart = true;
    }
  @endphp

    <!-- Dropdown Jenis Chart (Hanya Muncul Saat Edit) -->
    <div id="chartTypeContainerLog" class="hidden mb-4 w-full sm:w-96">
      <label for="chartTypeLog" class="block text-gray-700 font-semibold mb-2">Pilih Jenis Chart:</label>
      <select id="chartTypeLog"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        data-section="log_keluar_masuk_mahasiswa" data-type="chart_type">
        <option value="bar" {{ ($sections['log_keluar_masuk_mahasiswa']->chart_type ?? 'bar') == 'bar' ? 'selected' : '' }}>Bar</option>
        <option value="line" {{ ($sections['log_keluar_masuk_mahasiswa']->chart_type ?? 'bar') == 'line' ? 'selected' : '' }}>Line</option>
        <option value="pie" {{ ($sections['log_keluar_masuk_mahasiswa']->chart_type ?? 'bar') == 'pie' ? 'selected' : '' }}>Pie</option>
      </select>
    </div>

    @if($showChart)
    <div class="w-full sm:w-96 h-96 transition-all duration-500" id="chartContainerLog" style="margin: 0 auto;">
      <canvas id="logKeluarMasukChart" style="width: 100%; height: 100%;"></canvas>
    </div>
  @endif

    <!-- Display Data -->
    <div class="mt-4">
      @if(isset($dataMasuk) || isset($dataKeluar))
      @if($dataMasuk && $dataMasuk['result'] === 'OK')
      <p class="text-lg text-blue-600 font-semibold">
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
      <p class="text-lg text-red-600 font-semibold mt-6">
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

<!-- Edit Section Script -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const editButton = document.getElementById("editButton");
    const editIcon = document.getElementById("editIcon");
    const editText = document.getElementById("editText");
    const editableElements = document.querySelectorAll(".editable");
    const chartTypeContainerLog = document.getElementById("chartTypeContainerLog");
    const chartTypeSelectLog = document.getElementById("chartTypeLog");
    let isEditing = false;

    // Variabel untuk melacak perubahan
    const changes = {};

    editButton.addEventListener("click", () => {
      isEditing = !isEditing;

      // Toggle contentEditable untuk elemen yang dapat diedit
      editableElements.forEach((element) => {
        const sectionKey = element.getAttribute("data-section");
        const type = element.getAttribute("data-type"); // 'title' atau 'description'

        if (isEditing) {
          // Aktifkan mode edit
          element.contentEditable = true;
          element.style.border = "1px dashed gray";

          // Inisialisasi objek changes jika belum ada
          if (!changes[sectionKey]) {
            changes[sectionKey] = {};
          }

          // Simpan nilai awal berdasarkan tipe
          if (type === "title") {
            changes[sectionKey].originalTitle = element.innerHTML.trim();
          } else if (type === "description") {
            changes[sectionKey].originalDescription = element.innerHTML.trim();
          }

        } else {
          // Nonaktifkan mode edit
          element.contentEditable = false;
          element.style.border = "none";

          // Cek perubahan berdasarkan tipe
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

      // Tampilkan/hilangkan dropdown chart type saat edit
      if (chartTypeContainerLog) {
        chartTypeContainerLog.classList.toggle('hidden', !isEditing);
      }

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

    // Handle Enter key untuk menyisipkan <br> dalam mode edit
    editableElements.forEach((element) => {
      element.addEventListener("keydown", function (e) {
        if (e.key === "Enter") {
          e.preventDefault();
          const selection = window.getSelection();
          if (!selection.rangeCount) return;
          const range = selection.getRangeAt(0);

          // Sisipkan <br> di posisi kursor
          const br = document.createElement("br");
          range.deleteContents();
          range.insertNode(br);

          // Pindahkan kursor ke setelah <br>
          range.setStartAfter(br);
          range.collapse(true);
          selection.removeAllRanges();
          selection.addRange(range);
        }
      });
    });

    function saveChanges() {
      const payload = {};

      // Kumpulkan hanya perubahan
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

        // Hapus keys jika tidak ada perubahan
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

    // Event listener untuk chart type pada log_keluar_masuk_mahasiswa
    if (chartTypeSelectLog) {
      chartTypeSelectLog.addEventListener("change", function () {
        const sectionKey = this.getAttribute("data-section");
        if (!changes[sectionKey]) {
          changes[sectionKey] = {};
        }
        changes[sectionKey].updatedChartType = this.value;
        if (typeof updateChart === 'function') {
          updateChart(this.value);
        }
      });
    }
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    @if($showChart)
    const Masuk = {{ $dataMasuk['total'] ?? '0' }};
    const Keluar = {{ $dataKeluar['total'] ?? '0' }};
    const typeLabels = ["Masuk", "Keluar"];
    let logKeluarMasukChart;

    function updateChart(chartType = "{{ $sections['log_keluar_masuk_mahasiswa']->chart_type ?? 'bar' }}") {
      const jlhAbsensi = [Masuk, Keluar];
      const maxValue = Math.max(...jlhAbsensi);
      const gap = maxValue;
      const yMax = maxValue + 1000;

      if (logKeluarMasukChart) {
      logKeluarMasukChart.destroy();
      }

      logKeluarMasukChart = new Chart("logKeluarMasukChart", {
      type: chartType,
      data: {
        labels: typeLabels,
        datasets: [
        {
          label: 'Jumlah Mahasiswa',
          backgroundColor: ['#074799', '#E32227'],
          data: jlhAbsensi
        }
        ]
      },
      options: {
        plugins: {
        legend: { display: true },
        title: {
          display: true,
          text: "Log Keluar/Masuk Mahasiswa"
        }
        },
        scales: chartType === 'pie' ? {} : {
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

    // Inisialisasi chart pertama kali
    updateChart();

    // Event listener untuk live preview saat dropdown chart type berubah
    const chartTypeSelectLog = document.getElementById("chartTypeLog");
    if (chartTypeSelectLog) {
      chartTypeSelectLog.addEventListener("change", function () {
      updateChart(this.value);
      });
    }
  @endif
  });
</script>

<style>
  /* Transisi untuk perubahan ukuran chart */
  #chartContainerLog {
    transition: width 0.5s ease, height 0.5s ease;
  }

  /* Pastikan chartTypeContainerLog tidak menumpuk elemen lain */
  #chartTypeContainerLog {
    z-index: 10;
  }

  /* Saat edit mode aktif, dropdown chart type muncul */
  /* Sudah di-handle oleh JS toggle pada edit mode */

  /* Responsive adjustments */
  @media (min-width: 640px) {
    #chartContainerLog {
      width: 384px;
      /* sm:w-96 */
      height: 450px;
    }
  }

  @media (max-width: 639px) {
    #chartContainerLog {
      width: 100%;
      height: 450px;
    }
  }
</style>

@endsection