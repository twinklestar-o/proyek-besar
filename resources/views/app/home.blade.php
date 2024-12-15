@extends('auth.app')

@section('content')
@php
  $filePath = storage_path('app/public/edited_content.json');
  $editableContent = [];

  if (file_exists($filePath)) {
    $editableContent = json_decode(file_get_contents($filePath), true);
  }

  $currentYear = date('Y');
  $startYear = $currentYear - 6; // Mengambil 7 tahun terakhir termasuk tahun ini
  $angkatanYears = range($startYear, $currentYear);

  // Jika request tidak punya angkatan/prodi, gunakan session
  $selectedAngkatan = request('angkatan', session('last_angkatan', ''));
  $selectedProdi = request('prodi', session('last_prodi', ''));
@endphp

<div class="container mx-auto px-4 py-8">
  <!-- Total Mahasiswa Aktif Section -->
  <div class="bg-white shadow rounded-lg p-6 mb-8 editable">
    <h1 class="text-2xl font-bold text-gray-800 mb-4"> Total Mahasiswa Aktif</h1>
    <p class="text-gray-600 mb-4">
      Total Mahasiswa Aktif memberikan informasi mengenai jumlah total mahasiswa aktif yang terdaftar di institusi. Dari dashboard ini, dapat dilihat grafik persebaran mahasiswa setiap angkatan dan setiap prodi. Data spesifik untuk prodi dan angkatan tertentu juga dapat diakses untuk analisis lebih mendalam.
    </p>

    <!-- Filter form for angkatan and prodi -->
    <form id="filterForm" method="GET" action="{{ route(Auth::check() ? 'home.auth' : 'home.public') }}"
      class="mb-4 space-y-4">
      <!-- Dropdown for angkatan -->
      <div>
        <label for="angkatan" class="block text-gray-700 font-semibold mb-2">Filter by Angkatan :</label>
        <select name="angkatan" id="angkatan"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2">
          <option value="">Semua Angkatan</option>
          @foreach($angkatanYears as $year)
            <option value="{{ $year }}" {{ $selectedAngkatan == $year ? 'selected' : '' }}>{{ $year }}</option>
          @endforeach
        </select>
      </div>

      <!-- Dropdown for prodi -->
      <div>
        <label for="prodi" class="block text-gray-700 font-semibold mb-2">Filter by Prodi :</label>
        <select name="prodi" id="prodi"
          class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2">
          <option value="">Semua Prodi</option>
          @foreach($prodiList as $id => $name)
            <option value="{{ $id }}" {{ $selectedProdi == $id ? 'selected' : '' }}>{{ $name }}</option>
          @endforeach
        </select>
      </div>

      <!-- Filter button -->
      <div>
        <button type="submit"
          class="bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 focus:outline-none focus:ring focus:ring-indigo-200">
          Ambil Data
        </button>
      </div>
    </form>

    <!-- Display Data -->
    @php
      // Gunakan variabel $selectedAngkatan & $selectedProdi di bawah ini agar lebih konsisten
      $angkatan = $selectedAngkatan;
      $prodi = $selectedProdi;
    @endphp

    @if(!$angkatan && !$prodi)
      <!-- Semua Prodi, Semua Angkatan -->
      <h2 class="text-lg font-bold">Jumlah Mahasiswa di Semua Prodi dan Semua Angkatan</h2>

      @if(!empty($dataMahasiswa))
        <div class="flex justify-center mb-5">
          <div class="w-full" style="width: 80%;">
            <canvas id="semuaProdiAngkatanChart" style=" width: 100%;"></canvas>
          </div>
        </div>
      @endif

      <ul class="text-green-600 font-semibold">
        @if(!empty($dataMahasiswa))
          @foreach ($dataMahasiswa as $prodiName => $jumlah)
            @if($prodiName !== 'total') <!-- Abaikan key 'total' -->
              <li>{{ $prodiName }}: {{ $jumlah }} mahasiswa</li>
            @endif
          @endforeach
          <li class="font-bold text-green-600">Total mahasiswa: {{ $dataMahasiswa['total'] ?? 0 }} mahasiswa</li>
        @else
          <li class="text-red-500">Data belum tersedia.</li>
        @endif
      </ul>
    @elseif(!$angkatan && $prodi)
      <!-- Semua Angkatan, Prodi Terisi -->
      <h2 class="text-lg font-bold">Jumlah Mahasiswa di Semua Angkatan untuk Prodi {{ $prodiList[$prodi] ?? '-' }}</h2>

      @if(!empty($dataMahasiswa))
        <div class="flex justify-center mb-5">
          <div class="w-full" style="width: 80%;">
            <canvas id="semuaAngkatanChart" style=" width: 100%;"></canvas>
          </div>
        </div>
      @endif

      <ul class="text-green-600 font-semibold">
        @if(is_array($dataMahasiswa) && !empty($dataMahasiswa))
          @foreach($dataMahasiswa as $angkatanKey => $jumlah)
            <li>Angkatan {{ $angkatanKey }}: {{ $jumlah }} mahasiswa</li>
          @endforeach
          <li class="font-bold text-green-600">Total di Prodi: {{ array_sum($dataMahasiswa) }} mahasiswa</li>
        @else
          <li class="text-red-500">Data tidak tersedia.</li>
        @endif
      </ul>

      <script>
        document.addEventListener("DOMContentLoaded", function () {
          const angkatanData = @json($dataMahasiswa); // Ambil data angkatan
          const angkatanLabels = Object.keys(angkatanData); // Ambil label angkatan
          const angkatanCounts = Object.values(angkatanData); // Ambil jumlah mahasiswa per angkatan

          const semuaAngkatanChart = new Chart("semuaAngkatanChart", {
            type: "bar",
            data: {
              labels: angkatanLabels,
              datasets: [{
                label: 'Jumlah Mahasiswa per Angkatan',
                backgroundColor: angkatanLabels.map(label => label === '{{ $angkatan }}' ? 'red' : 'royalblue'),
                data: angkatanCounts
              }]
            },
            options: {
              plugins: {
                legend: { display: true },
                title: {
                  display: true,
                  text: "Jumlah Mahasiswa per Angkatan untuk Prodi {{ $prodiList[$prodi] ?? '-' }}"
                }
              },
              scales: {
                y: {
                  beginAtZero: true
                }
              }
            }
          });
        });
      </script>
    @elseif($angkatan && !$prodi)
      <!-- Angkatan Diisi, Semua Prodi -->
      <h2 class="text-lg font-bold">Jumlah Mahasiswa di Semua Prodi untuk Angkatan {{ $angkatan }}</h2>

      @if(!empty($dataMahasiswa))
        <div class="flex justify-center mb-5">
          <div class="w-full" style="width: 80%;">
            <canvas id="semuaProdiChart" style=" width: 100%;"></canvas>
          </div>
        </div>
      @endif

      <ul class="text-green-600 font-semibold">
        @if(is_array($dataMahasiswa) && !empty($dataMahasiswa))
          @foreach($dataMahasiswa as $prodiName => $jumlah)
            <li>{{ $prodiName }}: {{ $jumlah }} mahasiswa</li>
          @endforeach
          <li class="font-bold text-green-600">Angkatan total: {{ array_sum($dataMahasiswa) }} mahasiswa</li>
        @else
          <li class="text-red-500">Data belum tersedia.</li>
        @endif
      </ul>

      <script>
        document.addEventListener("DOMContentLoaded", function () {
          const prodiData = @json($dataMahasiswa); // Ambil data prodi
          const prodiLabels = Object.keys(prodiData); // Ambil label prodi
          const prodiCounts = Object.values(prodiData); // Ambil jumlah mahasiswa per prodi

          const semuaProdiChart = new Chart("semuaProdiChart", {
            type: "bar",
            data: {
              labels: prodiLabels,
              datasets: [{
                label: 'Jumlah Mahasiswa per Prodi',
                backgroundColor: 'royalblue',
                data: prodiCounts
              }]
            },
            options: {
              plugins: {
                legend: { display: true },
                title: {
                  display: true,
                  text: "Jumlah Mahasiswa per Prodi untuk Angkatan {{ $angkatan }}"
                }
              },
              scales: {
                y: {
                  beginAtZero: true
                }
              }
            }
          });
        });
      </script>
    @elseif($angkatan && $prodi)
      <!-- Kedua Parameter Terisi -->
      <h2 class="text-lg font-bold">
        Jumlah Mahasiswa untuk Prodi {{ $prodiList[$prodi] ?? '-' }} Angkatan {{ $angkatan }}
      </h2>
      <p class="text-green-600 font-semibold">
        @if(isset($dataMahasiswa['total']))
          Total Mahasiswa: {{ $dataMahasiswa['total'] }}
        @else
          <span class="text-red-500">Data belum tersedia.</span>
        @endif
      </p>
    @elseif(isset($dataMahasiswa['total']))
      <!-- Default Total Mahasiswa Aktif -->
      <h2 class="text-lg font-bold">Total Mahasiswa Aktif</h2>
      <p class="text-green-600 font-semibold">
        Total Mahasiswa Aktif: {{ $dataMahasiswa['total'] }}
      </p>
    @else
      <!-- Fallback -->
      <p class="text-red-500">Data belum tersedia.</p>
    @endif

    <!-- Chart for Total Mahasiswa Aktif -->
    <canvas id="totalMahasiswaAktifChart" class="mt-6"></canvas>
  </div>

  <!-- Prestasi Section -->
  <div class="bg-white shadow-md rounded-lg p-6 mt-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Prestasi</h1>
    <p class="text-gray-600 mb-4">
      Prestasi akademik dan non-akademik mahasiswa mencerminkan dedikasi dan usaha para mahasiswa dalam mencapai tujuan pendidikan. Melalui berbagai kompetisi dan kegiatan, mahasiswa berkesempatan untuk menunjukkan kemampuan dan keterampilan yang telah mereka pelajari.
    </p>
    <form id="filterPrestasi" method="GET" action="{{ route(Auth::check() ? 'home.auth' : 'home.public') }}"
      class="mb-4 space-y-4">
      <div>
        <label class="block text-gray-700 font-semibold mb-2">Filter by:</label>
        <input type="radio" id="tahun" name="waktu" value="tahun" checked>
        <label for="tahun">Tahun</label><br>
        <input type="radio" id="semester" name="waktu" value="semester">
        <label for="semester">Semester</label>
      </div>
    </form>
    <div class="flex justify-center mb-5">
      <div class="w-full" style="width: 80%;">
        <canvas id="prestasiTahun"></canvas>
    <canvas id="prestasiSemester" style="display: none;"></canvas>
      </div>
    </div>
  </div>

  <!-- Kegiatan Luar Kampus Section -->
  <div class="bg-white shadow-md rounded-lg p-6 mt-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Jumlah Mahasiswa yang Mengikuti Kegiatan di Luar Kampus</h1>
    <div class="space-y-4">
      <h5 class="font-bold text-gray-700">1. MBKM</h5>
      <p class="text-gray-600">
        Program MBKM (Merdeka Belajar Kampus Merdeka) memberikan mahasiswa kesempatan untuk belajar di luar kelas, mengembangkan keterampilan praktis, dan berkontribusi pada masyarakat. Melalui program ini, mahasiswa dapat mengikuti magang, proyek sosial, dan kegiatan lainnya yang mendukung pembelajaran holistik.
      </p>

      <h5 class="font-bold text-gray-700">2. IISMA</h5>
      <p class="text-gray-600">
        IISMA (<i>Indonesian International Student Mobility Awards</i>) adalah program yang memungkinkan mahasiswa Indonesia untuk belajar di universitas luar negeri. Program ini bertujuan untuk memperluas wawasan global mahasiswa, meningkatkan kemampuan bahasa, dan membangun jaringan internasional yang bermanfaat untuk karier mereka di masa depan.
      </p>

      <h5 class="font-bold text-gray-700"> 3. Kerja Praktik</h5>
      <p class="text-gray-600">
        Kerja praktik memberikan mahasiswa pengalaman langsung di dunia kerja, memungkinkan mereka untuk menerapkan teori yang telah dipelajari di kampus. Melalui kerja praktik, mahasiswa dapat mengembangkan keterampilan profesional dan membangun koneksi dengan industri.
      </p>

      <h5 class="font-bold text-gray-700">4. Studi Independent</h5>
      <p class="text-gray-600">
        Studi independent adalah kesempatan bagi mahasiswa untuk mengeksplorasi topik atau proyek penelitian secara mandiri. Program ini mendorong kreativitas dan inisiatif, memungkinkan mahasiswa untuk mendalami minat pribadi dan mengembangkan kemampuan analitis.
      </p>

      <h5 class="font-bold text-gray-700">5. Pertukaran Pelajar</h5>
      <p class="text-gray-600">
        Program pertukaran pelajar memungkinkan mahasiswa untuk belajar di institusi pendidikan di negara lain selama periode tertentu. Ini memberikan pengalaman budaya yang berharga, memperluas perspektif akademik, dan meningkatkan kemampuan adaptasi di lingkungan internasional.
      </p>
    </div>
    <div class="flex justify-center mb-5">
      <div class="w-full" style="width: 80%;">
        <canvas id="jlhMahasiswaKegiatanChart"></canvas>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const barColors = ["royalblue", "royalblue", "royalblue"];
    const prodiNames = @json(array_keys($dataMahasiswa)); // Ambil nama prodi
    const prodiCounts = @json(array_values($dataMahasiswa)); // Ambil jumlah mahasiswa

    function updateChart() {
      const jlhMahasiswa = prodiCounts.filter((_, index) => prodiNames[index] !== 'total'); // Filter untuk mengabaikan 'total'

      if (semuaProdiAngkatanChart) {
        semuaProdiAngkatanChart.destroy();
      }

      semuaProdiAngkatanChart = new Chart("semuaProdiAngkatanChart", {
        type: "bar",
        data: {
          labels: prodiNames.filter(name => name !== 'total'), // Ambil nama prodi tanpa 'total'
          datasets: [
            {
              label: 'Jumlah Mahasiswa per Prodi',
              backgroundColor: barColors,
              data: jlhMahasiswa
            }
          ]
        },
        options: {
          plugins: {
            legend: { display: true },
            title: {
              display: true,
              text: "Jumlah Mahasiswa per Prodi"
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }

    let semuaProdiAngkatanChart;
    updateChart();
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const semester = ["Ganjil", "Genap"];
    const kegiatan = ["MBKM", "IISMA", "Kerja Praktik", "Studi Independent", "Pertukaran Pelajar"];
    const jlhMahasiswaKegiatan = [137, 145, 137, 159, 156]; // Data jumlah mahasiswa untuk setiap kegiatan

    const prestasiSemesterChart = new Chart("prestasiSemester", {
      type: "bar",
      data: {
        labels: semester,
        datasets: [
          {
            label: 'Akademik',
            backgroundColor: 'royalblue',
            data: [43, 45] // Data prestasi akademik per semester
          },
          {
            label: 'NonAkademik',
            backgroundColor: 'darkgray',
            data: [4, 5] // Data prestasi non-akademik per semester
          }
        ]
      },
      options: {
        title: {
          display: true,
          text: 'Jumlah Prestasi/Semester'
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    const prestasiTahunChart = new Chart("prestasiTahun", {
      type: "bar",
      data: {
        labels: @json($angkatanYears),
        datasets: [
          {
            label: 'Akademik',
            backgroundColor: 'royalblue',
            data: [137, 145, 137, 159, 156, 151] // Data prestasi akademik per tahun
          },
          {
            label: 'NonAkademik',
            backgroundColor: 'darkgray',
            data: [17, 15, 13, 19, 16, 21] // Data prestasi non-akademik per tahun
          }
        ]
      },
      options: {
        title: {
          display: true,
          text: 'Jumlah Prestasi/Tahun'
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    const jlhMahasiswaKegiatanChart = new Chart("jlhMahasiswaKegiatanChart", {
      type: "bar",
      data: {
        labels: kegiatan,
        datasets: [{
          label: 'Jumlah Mahasiswa',
          backgroundColor: 'royalblue',
          data: jlhMahasiswaKegiatan
        }]
      },
      options: {
        title: {
          display: true,
          text: "Jumlah Mahasiswa yang Mengikuti Kegiatan di Luar Kampus"
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    // Menyembunyikan dan menampilkan canvas berdasarkan radio button dengan name waktu
    document.querySelectorAll('input[name="waktu"]').forEach((radio) => {
      radio.addEventListener('change', () => {
        if (radio.value === 'semester') {
          document.getElementById('prestasiSemester').style.display = 'block';
          document.getElementById('prestasiTahun').style.display = 'none';
        } else {
          document.getElementById('prestasiSemester').style.display = 'none';
          document.getElementById('prestasiTahun').style.display = 'block';
        }
      });
    });

    // Inisialisasi tampilan awal
    document.getElementById('prestasiSemester').style.display = 'none';
  });
</script>

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const ctx = document.getElementById('totalMahasiswaAktifChart').getContext('2d');
      const chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Mahasiswa Aktif'],
          datasets: [{
            label: 'Total Mah asiswa Aktif',
            data: ['{{ $dataMahasiswa['total'] ?? 0 }}'],
            backgroundColor: ['rgba(75, 192, 192, 0.2)'],
            borderColor: ['rgba(75, 192, 192, 1)'],
            borderWidth: 1
          }]
        },
        options: {
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    });
  </script>
@endpush

@endsection