@extends('auth.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="bg-white shadow rounded-lg p-6 mb-8">
    <!-- Editable Title -->
    <h1 class="text-2xl font-bold text-gray-800 mb-4 editable" data-section="absensi_kelas" data-type="title">
      {!! $sections['absensi_kelas']->title ?? 'Absensi Kelas' !!}
    </h1>
    <!-- Editable Description -->
    <p class="text-gray-600 mb-4 editable" data-section="absensi_kelas" data-type="description">
      {!! $sections['absensi_kelas']->description ?? 'Default Description' !!}
    </p>

    <!-- Filter Form -->
    <form id="filterForm" class="mb-4 space-y-4">
      <!-- Pilih Prodi -->
      <div>
        <label for="prodi_id" class="block text-gray-700 font-semibold mb-2">Prodi</label>
        <select name="prodi_id" id="prodi_id"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          required>
          <option value="">Pilih Prodi</option>
          @foreach($prodiList as $pid => $pname)
        <option value="{{ $pid }}" {{ ($selectedProdi == $pid) ? 'selected' : '' }}>{{ $pname }}</option>
      @endforeach
        </select>
      </div>

      <!-- Pilih Semester -->
      <div>
        <label for="semester" class="block text-gray-700 font-semibold mb-2">Semester</label>
        <select name="semester" id="semester"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          required>
          <option value="">Pilih Semester</option>
          @for($i = 1; $i <= 8; $i++)
        <option value="{{ $i }}" {{ ($selectedSemester == $i) ? 'selected' : '' }}>Semester {{ $i }}</option>
      @endfor
        </select>
      </div>

      <!-- Pilih Tahun Ajar -->
      <div>
        <label for="ta" class="block text-gray-700 font-semibold mb-2">Tahun Ajar</label>
        <select name="ta" id="ta"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          required>
          <option value="">Pilih Tahun Ajar</option>
          @foreach($tahunAjarList as $thn)
        <option value="{{ $thn }}" {{ ($selectedTa == $thn) ? 'selected' : '' }}>{{ $thn }}</option>
      @endforeach
        </select>
      </div>

      <!-- Tombol Ambil Mata Kuliah -->
      <div>
        <button type="button" id="ambilMatkul"
          class="bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 focus:outline-none focus:ring focus:ring-indigo-200">
          Ambil Mata Kuliah
        </button>
      </div>

      <!-- Indikator Loading untuk Ambil Mata Kuliah -->
      <div id="loadingMatkul" style="display: none;">
        <p class="text-blue-500">Memuat Mata Kuliah...</p>
      </div>

      <!-- Jika matkul sudah diambil, tampilkan dropdown matkul dan tahun kurikulum -->
      <div id="matkulSection" style="display: none;">
        <label for="kode_mk" class="block text-gray-700 font-semibold mb-2">Mata Kuliah</label>
        <select name="kode_mk" id="kode_mk"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          required>
          <option value="">Pilih Mata Kuliah</option>
          @if(isset($matkulList) && count($matkulList) > 0)
        @foreach($matkulList as $matkul)
      <option value="{{ $matkul['kode_mk'] }}" {{ ($selectedMatkul == $matkul['kode_mk']) ? 'selected' : '' }}>
      {{ $matkul['nama_matkul'] }} ({{ $matkul['kode_mk'] }}) - Semester {{ $matkul['sem'] }}
      </option>
    @endforeach
      @endif
        </select>
      </div>

      <div id="tahunKurikulumSection" style="display: none;">
        <label for="id_kur" class="block text-gray-700 font-semibold mb-2">Tahun Kurikulum</label>
        <select name="id_kur" id="id_kur"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          required>
          <option value="">Pilih Tahun Kurikulum</option>
          @if(isset($tahunKurikulum) && count($tahunKurikulum) > 0)
        @foreach($tahunKurikulum as $tahun)
      <option value="{{ $tahun['id_kur'] }}" {{ ($selectedKurikulum == $tahun['id_kur']) ? 'selected' : '' }}>
      {{ $tahun['id_kur'] }}
      </option>
    @endforeach
      @endif
        </select>
      </div>

      <!-- Bagian Tanggal dan Tombol Ambil Data (Disembunyikan Secara Default) -->
      <div id="tanggalDanAmbilDataSection" style="display: none;">
        <!-- Start Time -->
        <div>
          <label for="start_time" class="block text-gray-700 font-semibold mb-2">Dari tanggal:</label>
          <input type="date" name="start_time" id="start_time"
            class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
            value="{{ $selectedStartTime }}">
        </div>

        <!-- End Time -->
        <div>
          <label for="end_time" class="block text-gray-700 font-semibold mb-2">Sampai tanggal:</label>
          <input type="date" name="end_time" id="end_time"
            class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
            value="{{ $selectedEndTime }}">
        </div>

        <!-- Tombol Ambil Data -->
        <div>
          <button type="button" id="ambilData"
            class="bg-green-600 text-white font-bold mt-3 py-2 px-4 rounded hover:bg-green-700 focus:outline-none focus:ring focus:ring-green-200">
            Ambil Data
          </button>
        </div>

        <!-- Indikator Loading untuk Ambil Data -->
        <div id="loadingData" style="display: none;">
          <p class="text-green-500">Memuat Data Absensi...</p>
        </div>
      </div>
    </form>

    <!-- Display Data -->
    <div class="mt-4">
      <h2 class="text-xl font-bold text-gray-800 mb-2">Absensi Data</h2>

      <!-- Chart -->
      <div id="chartSection" style="display: none;">
        <div class="flex flex-col items-center mb-5">
          <!-- Dropdown Jenis Chart -->
          <div id="chartTypeContainerAbsensiKelas" class="hidden mb-4 w-full sm:w-80">
            <label for="chartTypeAbsensiKelas" class="block text-gray-700 font-semibold mb-2">Pilih Jenis Chart:</label>
            <select id="chartTypeAbsensiKelas"
              class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
              data-section="absensi_kelas" data-type="chart_type">
              <option value="bar" {{ ($sections['absensi_kelas']->chart_type ?? 'bar') == 'bar' ? 'selected' : '' }}>Bar
              </option>
              <option value="line" {{ ($sections['absensi_kelas']->chart_type ?? 'bar') == 'line' ? 'selected' : '' }}>
                Line</option>
              <option value="pie" {{ ($sections['absensi_kelas']->chart_type ?? 'bar') == 'pie' ? 'selected' : '' }}>Pie
              </option>
            </select>
          </div>

          <!-- Kontainer Chart -->
          <div id="chartContainerAbsensiKelas" class="w-full sm:w-80 transition-all duration-500">
            <canvas id="absensiKelasChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Tabel Absensi -->
      <div id="tabelAbsensiSection" style="display: none;">
        <div class="overflow-x-auto">
          <table class="min-w-full bg-white border border-gray-300 rounded-md shadow-sm">
            <thead class="bg-gray-200 text-gray-700">
              <tr>
                <th class="py-2 px-4 border-b">No</th>
                <th class="py-2 px-4 border-b">Waktu Mulai</th>
                <th class="py-2 px-4 border-b">Waktu Akhir</th>
                <th class="py-2 px-4 border-b">Sesi</th>
                <th class="py-2 px-4 border-b">Lokasi</th>
                <th class="py-2 px-4 border-b">Total Mhs KRS</th>
                <th class="py-2 px-4 border-b">Total Mhs Hadir</th>
                <th class="py-2 px-4 border-b">Total Mhs Absen</th>
              </tr>
            </thead>
            <tbody id="tabelAbsensiBody">
              <!-- Data Absensi akan diisi oleh JavaScript -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pesan Jika Data Tidak Tersedia -->
      <div id="pesanTidakAdaData" style="display: none;">
        <p class="text-lg text-red-500 font-semibold">Data belum tersedia.</p>
      </div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- jQuery (untuk AJAX) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- SweetAlert2 Library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  $(document).ready(function () {
    // Variabel untuk menyimpan instance Chart
    let absensiKelasChart = null;

    // Fungsi untuk menampilkan Mata Kuliah dan Tahun Kurikulum
    function showMatkulAndTahunKurikulum(matkulList, tahunKurikulum) {
      // Reset dropdown matkul dan tahun kurikulum
      $('#kode_mk').empty().append('<option value="">Pilih Mata Kuliah</option>');
      $('#id_kur').empty().append('<option value="">Pilih Tahun Kurikulum</option>');

      // Populate mata kuliah dengan informasi semester
      $.each(matkulList, function (index, matkul) {
        let selected = "{{ $selectedMatkul }}" === matkul.kode_mk ? 'selected' : '';
        $('#kode_mk').append('<option value="' + matkul.kode_mk + '" ' + selected + '>' + matkul.nama_matkul + ' (' + matkul.kode_mk + ') - Semester ' + matkul.sem + '</option>');
      });

      // Populate tahun kurikulum
      $.each(tahunKurikulum, function (index, tahun) {
        let selected = "{{ $selectedKurikulum }}" === tahun.id_kur ? 'selected' : '';
        $('#id_kur').append('<option value="' + tahun.id_kur + '" ' + selected + '>' + tahun.id_kur + '</option>');
      });

      // Tampilkan dropdown matkul dan tahun kurikulum
      $('#matkulSection').show();
      $('#tahunKurikulumSection').show();
    }

    // Fungsi untuk menampilkan Tanggal dan Tombol Ambil Data
    function showTanggalDanAmbilData() {
      $('#tanggalDanAmbilDataSection').show();
    }

    // Fungsi untuk menampilkan data Absensi
    function showAbsensiData(data) {
      // Reset tabel dan chart
      $('#tabelAbsensiBody').empty();
      if (absensiKelasChart) {
        absensiKelasChart.destroy();
        absensiKelasChart = null;
      }

      if (data && data.length > 0) {
        // Menampilkan Tabel Absensi
        $.each(data, function (index, item) {
          const row = `
            <tr>
              <td class="py-2 px-4 border-b">${index + 1}</td>
              <td class="py-2 px-4 border-b">${item.waktu_mulai ?? 'N/A'}</td>
              <td class="py-2 px-4 border-b">${item.waktu_akhir ?? 'N/A'}</td>
              <td class="py-2 px-4 border-b">${item.sesi ?? 'N/A'}</td>
              <td class="py-2 px-4 border-b">${item.lokasi ?? 'N/A'}</td>
              <td class="py-2 px-4 border-b">${item.total_mhs_krs ?? 'N/A'}</td>
              <td class="py-2 px-4 border-b">${item.total_mhs_hadir ?? 'N/A'}</td>
              <td class="py-2 px-4 border-b">${item.total_mhs_absen ?? 'N/A'}</td>
            </tr>
          `;
          $('#tabelAbsensiBody').append(row);
        });

        // Menampilkan Tabel dan Chart
        $('#tabelAbsensiSection').show();
        $('#pesanTidakAdaData').hide();
        $('#chartSection').show();

        // Menghitung total absensi
        let totalMhsKRS = 0;
        let totalMhsHadir = 0;
        let totalMhsAbsen = 0;

        $.each(data, function (index, item) {
          totalMhsKRS += parseInt(item.total_mhs_krs) || 0;
          totalMhsHadir += parseInt(item.total_mhs_hadir) || 0;
          totalMhsAbsen += parseInt(item.total_mhs_absen) || 0;
        });

        const hadir = totalMhsHadir;
        const tidakHadir = totalMhsAbsen;
        const persentaseHadir = totalMhsKRS > 0 ? (hadir / totalMhsKRS) * 100 : 0;
        const persentaseTidakHadir = totalMhsKRS > 0 ? (tidakHadir / totalMhsKRS) * 100 : 0;
        const jlhKehadiran = [persentaseHadir.toFixed(2), persentaseTidakHadir.toFixed(2)];

        // Menampilkan Chart
        absensiKelasChart = new Chart("absensiKelasChart", {
          type: "pie",
          data: {
            labels: ["Hadir", "Tidak Hadir"],
            datasets: [
              {
                label: 'Persentase Kehadiran Mahasiswa',
                backgroundColor: ["#074799", "#E32227"], // Warna berbeda untuk pie chart
                data: jlhKehadiran
              }
            ]
          },
          options: {
            plugins: {
              legend: { display: true },
              title: {
                display: true,
                text: "Persentase Kehadiran Mahasiswa"
              },
              tooltip: {
                callbacks: {
                  label: function (tooltipItem) {
                    return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                  }
                }
              }
            },
            scales: {}, // Hilangkan skala untuk pie chart
            maintainAspectRatio: false
          }
        });
      } else {
        // Menampilkan Pesan Tidak Ada Data
        $('#tabelAbsensiSection').hide();
        $('#chartSection').hide();
        $('#pesanTidakAdaData').show();
      }
    }

    // Inisialisasi tampilan berdasarkan data yang ada saat page load
    @if(isset($matkulList) && count($matkulList) > 0)
    showMatkulAndTahunKurikulum(@json($matkulList), @json($tahunKurikulum));
    showTanggalDanAmbilData();

    @if(isset($absensiData) && count($absensiData) > 0)
    showAbsensiData(@json($absensiData));
  @elseif(isset($absensiData) && count($absensiData) === 0)
  $('#tabelAbsensiSection').hide();
  $('#chartSection').hide();
  $('#pesanTidakAdaData').show();
@endif
  @endif

    // Event handler untuk tombol "Ambil Mata Kuliah"
    $('#ambilMatkul').click(function () {
      const prodi_id = $('#prodi_id').val();
      const semester = $('#semester').val();
      const ta = $('#ta').val();

      console.log('Mengambil Mata Kuliah dengan prodi_id:', prodi_id, 'semester:', semester, 'ta:', ta);

      if (prodi_id && semester && ta) {
        $('#loadingMatkul').show();
        $.ajax({
          url: "{{ route('absensi.kelas.matkul') }}",
          type: "GET",
          data: {
            prodi_id: prodi_id,
            semester: semester,
            ta: ta,
          },
          success: function (response) {
            $('#loadingMatkul').hide();
            if (response.matkulList && response.tahunKurikulum) {
              showMatkulAndTahunKurikulum(response.matkulList, response.tahunKurikulum);
              showTanggalDanAmbilData(); // Tampilkan bagian tanggal dan tombol setelah Mata Kuliah dimuat

              // Reset Absensi data
              $('#tabelAbsensiSection').hide();
              $('#chartSection').hide();
              $('#pesanTidakAdaData').hide();
            } else {
              alert('Data Mata Kuliah atau Tahun Kurikulum tidak ditemukan.');
            }
          },
          error: function (xhr) {
            $('#loadingMatkul').hide();
            if (xhr.responseJSON && xhr.responseJSON.details) {
              let errorMsg = 'Input tidak valid:\n';
              $.each(xhr.responseJSON.details, function (field, messages) {
                errorMsg += field + ': ' + messages.join(', ') + '\n';
              });
              alert(errorMsg);
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
              alert(xhr.responseJSON.error);
            } else {
              alert('Gagal mengambil data Mata Kuliah. Pastikan Prodi, Semester, dan Tahun Ajar sudah benar.');
            }
          }
        });
      } else {
        alert('Silakan pilih Prodi, Semester, dan Tahun Ajar terlebih dahulu.');
      }
    });

    // Event handler untuk tombol "Ambil Data"
    $('#ambilData').click(function () {
      const kode_mk = $('#kode_mk').val();
      const id_kur = $('#id_kur').val();
      const start_time = $('#start_time').val();
      const end_time = $('#end_time').val();

      console.log('Mengambil Data Absensi dengan kode_mk:', kode_mk, 'id_kur:', id_kur, 'start_time:', start_time, 'end_time:', end_time);

      if (kode_mk && id_kur) {
        $('#loadingData').show();
        $.ajax({
          url: "{{ route('absensi.kelas.absensi') }}",
          type: "GET",
          data: {
            kode_mk: kode_mk,
            id_kur: id_kur,
            start_time: start_time,
            end_time: end_time,
          },
          success: function (response) {
            $('#loadingData').hide();
            if (response.data && response.data.length > 0) {
              showAbsensiData(response.data);
            } else {
              $('#tabelAbsensiSection').hide();
              $('#chartSection').hide();
              $('#pesanTidakAdaData').show();
              alert('Data Absensi tidak ditemukan.');
            }
          },
          error: function (xhr) {
            $('#loadingData').hide();
            if (xhr.responseJSON && xhr.responseJSON.details) {
              let errorMsg = 'Input tidak valid:\n';
              $.each(xhr.responseJSON.details, function (field, messages) {
                errorMsg += field + ': ' + messages.join(', ') + '\n';
              });
              alert(errorMsg);
            } else if (xhr.responseJSON && xhr.responseJSON.error) {
              alert(xhr.responseJSON.error);
            } else {
              alert('Gagal mengambil data Absensi. Pastikan semua field sudah diisi dengan benar.');
            }
          }
        });
      } else {
        alert('Silakan pilih Mata Kuliah dan Tahun Kurikulum terlebih dahulu.');
      }
    });
  });
</script>

<!-- Script for Editable Sections -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const editButton = document.getElementById("editButton");
    const editIcon = document.getElementById("editIcon");
    const editText = document.getElementById("editText");
    const editableElements = document.querySelectorAll(".editable");
    const chartTypeContainers = {
      totalMahasiswa: document.getElementById("chartTypeContainerTotalMahasiswa"),
      prestasi: document.getElementById("chartTypeContainerPrestasi"),
      kegiatanLuar: document.getElementById("chartTypeContainerKegiatanLuar"),
      absensiKelas: document.getElementById("chartTypeContainerAbsensiKelas") // ADD
    };
    let isEditing = false;

    // Gunakan objek global untuk melacak perubahan
    if (!window.sectionChanges) {
      window.sectionChanges = {};
    }

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

      // Toggle visibility dropdown chart type untuk semua section
      for (const key in chartTypeContainers) {
        if (chartTypeContainers[key]) {
          chartTypeContainers[key].classList.toggle('hidden', !isEditing);
        }
      }

      // Toggle ikon dan teks tombol
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

    // Event listener untuk perubahan chart_type pada absensi_kelas
    const chartTypeSelectAbsensiKelas = document.getElementById("chartTypeAbsensiKelas");
    if (chartTypeSelectAbsensiKelas) {
      chartTypeSelectAbsensiKelas.addEventListener("change", function () {
        const selectedChartType = this.value;
        const sectionKey = this.getAttribute("data-section");

        // Simpan perubahan chart_type
        if (!window.sectionChanges[sectionKey]) {
          window.sectionChanges[sectionKey] = {};
        }
        window.sectionChanges[sectionKey].updatedChartType = selectedChartType;

        // Update chart secara langsung (preview)
        if (window.absensiKelasChart) {
          window.absensiKelasChart.destroy();
          window.initializeAbsensiKelasChart(selectedChartType);
        }
      });
    }

    // Fungsi untuk menyimpan perubahan
    function saveChanges() {
      const payload = {};

      // Gabungkan perubahan dari editable elements
      for (const sectionKey in changes) {
        payload[sectionKey] = {};
        if (changes[sectionKey].updatedTitle) {
          payload[sectionKey].title = changes[sectionKey].updatedTitle;
        }
        if (changes[sectionKey].updatedDescription) {
          payload[sectionKey].description = changes[sectionKey].updatedDescription;
        }
      }

      // Gabungkan perubahan dari chart type dropdowns
      for (const sectionKey in window.sectionChanges) {
        if (!payload[sectionKey]) {
          payload[sectionKey] = {};
        }
        if (window.sectionChanges[sectionKey].updatedChartType) {
          payload[sectionKey].chart_type = window.sectionChanges[sectionKey].updatedChartType;
        }
      }

      // Hapus sectionKey yang tidak ada perubahan
      for (const sectionKey in payload) {
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
        })
        .finally(() => {
          // Reset perubahan setelah menyimpan
          for (const sectionKey in changes) {
            delete changes[sectionKey];
          }
          for (const sectionKey in window.sectionChanges) {
            delete window.sectionChanges[sectionKey];
          }
        });
    }
  });
</script>

<script>
  $(document).ready(function () {
    function fetchMatkul(prodi_id, semester, ta) {
      $('#loadingMatkul').show();

      $.ajax({
        url: "{{ route('absensi.kelas.matkul') }}",
        type: "GET",
        data: { prodi_id, semester, ta },
        success: function (response) {
          $('#loadingMatkul').hide();
          if (response.matkulList && response.tahunKurikulum) {
            showMatkulAndTahunKurikulum(response.matkulList, response.tahunKurikulum);
            showTanggalDanAmbilData(); // Tampilkan bagian tanggal dan tombol setelah Mata Kuliah dimuat

            // Reset Absensi data
            $('#tabelAbsensiSection').hide();
            $('#chartSection').hide();
            $('#pesanTidakAdaData').hide();
          } else {
            alert('Data Mata Kuliah atau Tahun Kurikulum tidak ditemukan.');
          }
        },
        error: function () {
          $('#loadingMatkul').hide();
          alert('Gagal memuat data Mata Kuliah.');
        }
      });
    }

    function fetchAbsensiData(kode_mk, id_kur, start_time, end_time) {
      $('#loadingData').show();

      $.ajax({
        url: "{{ route('absensi.kelas.absensi') }}",
        type: "GET",
        data: { kode_mk, id_kur, start_time, end_time },
        success: function (response) {
          $('#loadingData').hide();
          if (response.data && response.data.length > 0) {
            showAbsensiData(response.data);
          } else {
            $('#tabelAbsensiSection').hide();
            $('#chartSection').hide();
            $('#pesanTidakAdaData').show();
            alert('Data Absensi tidak ditemukan.');
          }
        },
        error: function () {
          $('#loadingData').hide();
          alert('Gagal memuat data Absensi.');
        }
      });
    }

    // Panggil fetchMatkul ketika tombol diklik
    $('#ambilMatkul').click(function () {
      const prodi_id = $('#prodi_id').val();
      const semester = $('#semester').val();
      const ta = $('#ta').val();

      if (prodi_id && semester && ta) {
        fetchMatkul(prodi_id, semester, ta);
      } else {
        alert('Pilih Prodi, Semester, dan Tahun Ajar.');
      }
    });

    // Panggil fetchAbsensiData ketika tombol diklik
    $('#ambilData').click(function () {
      const kode_mk = $('#kode_mk').val();
      const id_kur = $('#id_kur').val();
      const start_time = $('#start_time').val();
      const end_time = $('#end_time').val();

      if (kode_mk && id_kur) {
        fetchAbsensiData(kode_mk, id_kur, start_time, end_time);
      } else {
        alert('Pilih Mata Kuliah dan Tahun Kurikulum.');
      }
    });
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    // Inisialisasi Chart Absensi Kelas dengan jenis chart dinamis
    const absensiKelasCanvas = document.getElementById('absensiKelasChart');
    let absensiKelasChart = null;
    let currentChartTypeAbsensiKelas = "{{ $sections['absensi_kelas']->chart_type ?? 'bar' }}";

    function initializeAbsensiKelasChart(chartType) {
      if (absensiKelasChart) {
        absensiKelasChart.destroy();
      }

      const ctxAbsensiKelas = absensiKelasCanvas.getContext('2d');

      // Referensi ke kontainer chart
      const chartContainerAbsensiKelas = document.getElementById("chartContainerAbsensiKelas");

      // Tentukan warna berdasarkan jenis chart
      let backgroundColors;
      if (chartType === 'pie') {
        backgroundColors = ["#36A2EB", "#FF6384"]; // Warna berbeda untuk pie chart
      } else {
        backgroundColors = ["rgba(75, 192, 192, 0.2)", "rgba(255, 99, 132, 0.2)"]; // Warna untuk bar/line chart
      }

      // Sesuaikan ukuran kontainer berdasarkan jenis chart
      if (chartType === 'pie') {
        chartContainerAbsensiKelas.style.width = "60%"; // Perbesar lebar untuk pie chart
        chartContainerAbsensiKelas.style.height = "500px"; // Perbesar tinggi jika diperlukan
      } else {
        chartContainerAbsensiKelas.style.width = "80%"; // Kembali ke lebar semula
        chartContainerAbsensiKelas.style.height = "400px"; // Kembali ke tinggi semula
      }

      absensiKelasChart = new Chart(ctxAbsensiKelas, {
        type: chartType,
        data: {
          labels: ["Hadir", "Tidak Hadir"],
          datasets: [{
            label: 'Persentase Kehadiran Mahasiswa',
            data: [75, 25], // Ganti dengan data dinamis Anda
            backgroundColor: backgroundColors,
            borderColor: chartType === 'pie' ? ["#36A2EB", "#FF6384"] : ["rgba(75, 192, 192, 1)", "rgba(255,99,132,1)"],
            borderWidth: 1
          }]
        },
        options: {
          plugins: {
            legend: { display: true },
            title: {
              display: true,
              text: "Persentase Kehadiran Mahasiswa"
            },
            tooltip: {
              callbacks: {
                label: function (tooltipItem) {
                  return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                }
              }
            }
          },
          scales: chartType === 'pie' ? {} : { // Hilangkan skala untuk pie chart
            y: {
              beginAtZero: true,
              ticks: { precision: 0 },
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

    // Inisialisasi chart dengan chart_type saat ini
    if (absensiKelasCanvas) {
      initializeAbsensiKelasChart(currentChartTypeAbsensiKelas);
    }

    // Event listener untuk perubahan jenis chart
    const chartTypeSelectAbsensiKelas = document.getElementById("chartTypeAbsensiKelas");
    if (chartTypeSelectAbsensiKelas) {
      chartTypeSelectAbsensiKelas.addEventListener("change", function () {
        const selectedChartType = this.value;
        initializeAbsensiKelasChart(selectedChartType);

        // Simpan perubahan chart_type ke window.sectionChanges
        const sectionKey = this.getAttribute("data-section");
        if (!window.sectionChanges[sectionKey]) {
          window.sectionChanges[sectionKey] = {};
        }
        window.sectionChanges[sectionKey].updatedChartType = selectedChartType;
      });
    }

    // Buat fungsi inisialisasi dan referensi chart secara global jika diperlukan
    window.initializeAbsensiKelasChart = initializeAbsensiKelasChart;
    window.absensiKelasChart = absensiKelasChart;
  });
</script>

@endsection