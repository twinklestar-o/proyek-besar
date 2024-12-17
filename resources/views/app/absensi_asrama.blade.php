@extends('auth.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <!-- Absensi Asrama Section -->
  <div class="bg-white shadow rounded-lg p-6 mb-8">

    <!-- Editable Title -->
    <h1 class="text-2xl font-bold text-gray-800 mb-4 editable" data-section="absensi_asrama" data-type="title">
      {!! $sections['absensi_asrama']->title ?? 'Absensi Asrama' !!}
    </h1>
    <!-- Editable Description -->
    <p class="text-gray-600 mb-4 editable" data-section="absensi_asrama" data-type="description">
      {!! $sections['absensi_asrama']->description ?? 'Deskripsi Default' !!}
    </p>

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

      <!-- Dropdown untuk mengganti chart_type, hidden by default -->
      <div id="chartTypeContainerAbsensi" class="hidden mb-4">
        <label for="chartTypeAbsensi" class="block text-gray-700 font-semibold mb-2">Pilih Jenis Chart:</label>
        <select id="chartTypeAbsensi"
        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
        data-section="absensi_asrama" data-type="chart_type">
        <option value="bar" {{ ($sections['absensi_asrama']->chart_type ?? 'bar') == 'bar' ? 'selected' : '' }}>Bar
        </option>
        <option value="line" {{ ($sections['absensi_asrama']->chart_type ?? 'bar') == 'line' ? 'selected' : '' }}>Line
        </option>
        <option value="pie" {{ ($sections['absensi_asrama']->chart_type ?? 'bar') == 'pie' ? 'selected' : '' }}>Pie
        </option>
        </select>
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
<!-- Bootstrap Icons (Pastikan sudah di-include) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- Chart Initialization Script -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const barColorsAbsensi = ["royalblue", "royalblue", "royalblue"];
    const typeAbsensi = ["Absen", "Izin", "Sakit"];

    // Inisialisasi Chart Absensi Asrama dengan tipe chart dari database
    let absensiAsramaChart;
    function initializeAbsensiChart(chartType) {
      const Absen = {{ $data['data']['jumlah_absen'] ?? '0' }};
      const Izin = {{ $data['data']['jumlah_izin'] ?? '0' }};
      const Sakit = {{ $data['data']['jumlah_sakit'] ?? '0' }};
      const jlhAbsensi = [Absen, Izin, Sakit];

      const maxValue = Math.max(...jlhAbsensi);
      const gap = maxValue;
      const yMax = maxValue + gap;

      const ctx = document.getElementById("absensiAsramaChart").getContext('2d');
      absensiAsramaChart = new Chart(ctx, {
        type: chartType, // Gunakan tipe chart yang diberikan
        data: {
          labels: typeAbsensi,
          datasets: [
            {
              label: 'Jumlah Mahasiswa yang Absen',
              backgroundColor: barColorsAbsensi,
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

    // Inisialisasi chart saat halaman dimuat
    @if(isset($data) && isset($data['data']) && count($data['data']) > 0)
    initializeAbsensiChart("{{ $sections['absensi_asrama']->chart_type ?? 'bar' }}");
  @endif
  });
</script>

<!-- Edit Section and Chart Type Preview Script -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const editButton = document.getElementById("editButton");
    const editIcon = document.getElementById("editIcon");
    const editText = document.getElementById("editText");
    const editableElements = document.querySelectorAll(".editable");
    const chartTypeContainerAbsensi = document.getElementById("chartTypeContainerAbsensi");
    const chartTypeSelectAbsensi = document.getElementById("chartTypeAbsensi");
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

      // Tampilkan/hilangkan dropdown chart type absensi_asrama jika chart ada
      if (chartTypeContainerAbsensi) {
        chartTypeContainerAbsensi.classList.toggle('hidden', !isEditing);
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

    // Event listener untuk perubahan chart_type pada absensi_asrama
    if (chartTypeSelectAbsensi) {
      chartTypeSelectAbsensi.addEventListener("change", function () {
        const selectedChartType = this.value;
        const sectionKey = this.getAttribute("data-section");

        // Simpan perubahan chart_type
        if (!changes[sectionKey]) {
          changes[sectionKey] = {};
        }
        changes[sectionKey].updatedChartType = selectedChartType;

        // Update chart secara langsung (preview)
        if (absensiAsramaChart) {
          absensiAsramaChart.destroy();
          initializeAbsensiChart(selectedChartType);
        }
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

    // Fungsi untuk menginisialisasi chart, perlu didefinisikan ulang di sini untuk akses global
    function initializeAbsensiChart(chartType) {
      const Absen = {{ $data['data']['jumlah_absen'] ?? '0' }};
      const Izin = {{ $data['data']['jumlah_izin'] ?? '0' }};
      const Sakit = {{ $data['data']['jumlah_sakit'] ?? '0' }};
      const jlhAbsensi = [Absen, Izin, Sakit];

      const maxValue = Math.max(...jlhAbsensi);
      const gap = maxValue;
      const yMax = maxValue + gap;

      const ctx = document.getElementById("absensiAsramaChart").getContext('2d');
      absensiAsramaChart = new Chart(ctx, {
        type: chartType, // Gunakan tipe chart yang diberikan
        data: {
          labels: typeAbsensi,
          datasets: [
            {
              label: 'Jumlah Mahasiswa yang Absen',
              backgroundColor: barColorsAbsensi,
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

    // Pastikan fungsi initializeAbsensiChart didefinisikan sebelum digunakan
    // Inisialisasi chart saat halaman dimuat
    @if(isset($data) && isset($data['data']) && count($data['data']) > 0)
    initializeAbsensiChart("{{ $sections['absensi_asrama']->chart_type ?? 'bar' }}");
  @endif
  });
</script>

<!-- Warning Message Script -->
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