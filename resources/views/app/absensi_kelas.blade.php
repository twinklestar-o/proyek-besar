@extends('auth.app')

@section('content')
<div class="container mx-auto px-4 py-8">
  <div class="bg-white shadow rounded-lg p-6 mb-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Absensi Kelas</h1>

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
        <option value="{{ $pid }}" {{ request('prodi_id') == $pid ? 'selected' : '' }}>{{ $pname }}</option>
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
        <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
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
        <option value="{{ $thn }}" {{ request('ta') == $thn ? 'selected' : '' }}>{{ $thn }}</option>
      @endforeach
        </select>
      </div>

      <!-- Tombol Ambil Mata Kuliah -->
      <div>
        <button type="button" id="ambilMatkul"
          class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-200">
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
          <!-- Opsi akan diisi oleh JavaScript -->
        </select>
      </div>

      <div id="tahunKurikulumSection" style="display: none;">
        <label for="id_kur" class="block text-gray-700 font-semibold mb-2">Tahun Kurikulum</label>
        <select name="id_kur" id="id_kur"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          required>
          <option value="">Pilih Tahun Kurikulum</option>
          <!-- Opsi akan diisi oleh JavaScript -->
        </select>
      </div>

      <!-- Start Time -->
      <div>
        <label for="start_time" class="block text-gray-700 font-semibold mb-2">Dari tanggal:</label>
        <input type="date" name="start_time" id="start_time"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          value="{{ request('start_time') }}">
      </div>

      <!-- End Time -->
      <div>
        <label for="end_time" class="block text-gray-700 font-semibold mb-2">Sampai tanggal:</label>
        <input type="date" name="end_time" id="end_time"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
          value="{{ request('end_time') }}">
      </div>

      <!-- Tombol Ambil Data -->
      <div>
        <button type="button" id="ambilData"
          class="bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 focus:outline-none focus:ring focus:ring-green-200">
          Ambil Data
        </button>
      </div>

      <!-- Indikator Loading untuk Ambil Data -->
      <div id="loadingData" style="display: none;">
        <p class="text-green-500">Memuat Data Absensi...</p>
      </div>
    </form>

    <!-- Display Data -->
    <div class="mt-4">
      <h2 class="text-xl font-bold text-gray-800 mb-2">Absensi Data</h2>

      <!-- Chart -->
      <div id="chartSection" style="display: none;">
        <div class="flex justify-center mb-5">
          <div class="sm:w-80">
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
        $('#kode_mk').append('<option value="' + matkul.kode_mk + '">' + matkul.nama_matkul + ' (' + matkul.kode_mk + ') - Semester ' + matkul.sem + '</option>');
      });

      // Populate tahun kurikulum
      $.each(tahunKurikulum, function (index, tahun) {
        $('#id_kur').append('<option value="' + tahun.id_kur + '">' + tahun.id_kur + '</option>');
      });

      // Tampilkan dropdown matkul dan tahun kurikulum
      $('#matkulSection').show();
      $('#tahunKurikulumSection').show();
    }

    // Event handler untuk tombol "Ambil Mata Kuliah"
    $('#ambilMatkul').click(function () {
      const prodi_id = parseInt($('#prodi_id').val());
      const semester = parseInt($('#semester').val());
      const ta = parseInt($('#ta').val());

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
                backgroundColor: ["royalblue", "red"],
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
            }
          }
        });
      } else {
        // Menampilkan Pesan Tidak Ada Data
        $('#tabelAbsensiSection').hide();
        $('#chartSection').hide();
        $('#pesanTidakAdaData').show();
      }
    }

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

    // Jika ada data matkul (misalnya setelah reload dengan data sebelumnya), tampilkan dropdown
    @if(old('kode_mk') || request('kode_mk'))
    $('#matkulSection').show();
    $('#tahunKurikulumSection').show();
  @endif
  });
</script>
@endsection