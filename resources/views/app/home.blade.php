@extends('auth.app')

@section('content')
@php
    $currentYear = date('Y');
    $startYear = $currentYear - 6; // Mengambil 7 tahun terakhir termasuk tahun ini
    $angkatanYears = range($startYear, $currentYear);

    // Jika request tidak punya angkatan/prodi, gunakan session
    $selectedAngkatan = request('angkatan', session('last_angkatan', ''));
    $selectedProdi = request('prodi', session('last_prodi', ''));
@endphp

<div class="container mx-auto px-4 py-8">

    <!-- Total Mahasiswa Aktif Section -->
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 editable" data-section="total_mahasiswa_aktif"
            data-field="title">
            {!! $sections['total_mahasiswa_aktif']->title ?? 'Total Mahasiswa Aktif' !!}
        </h1>
        <p class="text-gray-600 mb-4 editable" data-section="total_mahasiswa_aktif" data-field="description">
            {!! $sections['total_mahasiswa_aktif']->description ?? 'Deskripsi Default' !!}
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
                        <!-- Dropdown chart type -->
                        <div class="chart-type-selector mt-2" style="display:none;">
                            <label for="chartType_total_mahasiswa_aktif" class="block mb-1 font-semibold">Jenis Chart:</label>
                            <select id="chartType_total_mahasiswa_aktif" data-section="total_mahasiswa_aktif"
                                data-field="chart_type"
                                class="chart-type-dropdown bg-white border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring">
                                <option value="bar" {{ ($sections['total_mahasiswa_aktif']->chart_type ?? 'bar') === 'bar' ? 'selected' : '' }}>Bar</option>
                                <option value="line" {{ ($sections['total_mahasiswa_aktif']->chart_type ?? 'bar') === 'line' ? 'selected' : '' }}>Line</option>
                                <option value="pie" {{ ($sections['total_mahasiswa_aktif']->chart_type ?? 'bar') === 'pie' ? 'selected' : '' }}>Pie</option>
                            </select>
                        </div>
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

            <!-- Inisialisasi Chart untuk Semua Prodi dan Semua Angkatan -->
            @if(!empty($dataMahasiswa))
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        // Mengumpulkan data prodi dan jumlahnya
                        const prodiLabels = [];
                        const prodiCounts = [];

                        @foreach($dataMahasiswa as $prodiName => $jumlah)
                            @if($prodiName !== 'total')
                                prodiLabels.push("{{ $prodiName }}");
                                prodiCounts.push({{ $jumlah }});
                            @endif
                        @endforeach

                        const ctxSemuaProdiAngkatan = document.getElementById('semuaProdiAngkatanChart').getContext('2d');
                        const chartType = "{{ $sections['total_mahasiswa_aktif']->chart_type ?? 'bar' }}"; // Ambil jenis chart dari database

                        // Definisikan warna yang berbeda untuk setiap prodi
                        const backgroundColors = [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(199, 199, 199, 0.2)'
                        ];

                        const borderColors = [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(199, 199, 199, 1)'
                        ];

                        // Jika chart type adalah pie, sesuaikan beberapa opsi
                        const optionsChart = {};
                        if (chartType === 'pie') {
                            optionsChart.plugins = {
                                legend: { display: true },
                                title: {
                                    display: true,
                                    text: 'Jumlah Mahasiswa di Semua Prodi dan Semua Angkatan'
                                }
                            };
                        } else {
                            optionsChart.plugins = {
                                legend: { display: true },
                                title: {
                                    display: true,
                                    text: 'Jumlah Mahasiswa di Semua Prodi dan Semua Angkatan'
                                }
                            };
                            optionsChart.scales = {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    },
                                    grid: {
                                        display: true
                                    }
                                },
                                x: {
                                    grid: {
                                        display: true
                                    }
                                }
                            };
                        }

                        const semuaProdiAngkatanChart = new Chart(ctxSemuaProdiAngkatan, {
                            type: chartType,
                            data: {
                                labels: prodiLabels,
                                datasets: [{
                                    label: 'Jumlah Mahasiswa per Prodi',
                                    data: prodiCounts,
                                    backgroundColor: chartType === 'pie' ? backgroundColors.slice(0, prodiLabels.length) : 'rgba(54, 162, 235, 0.2)',
                                    borderColor: chartType === 'pie' ? borderColors.slice(0, prodiLabels.length) : 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: optionsChart
                        });
                    });
                </script>
            @endif
        @elseif(!$angkatan && $prodi)
            <!-- Semua Angkatan, Prodi Terisi -->
            <h2 class="text-lg font-bold">Jumlah Mahasiswa di Semua Angkatan untuk Prodi {{ $prodiList[$prodi] ?? '-' }}
            </h2>

            @if(!empty($dataMahasiswa))
                <div class="flex justify-center mb-5">
                    <div class="w-full" style="width: 80%;">
                        <canvas id="semuaAngkatanChart" style=" width: 100%;"></canvas>
                        <!-- Dropdown chart type -->
                        <div class="chart-type-selector mt-2" style="display:none;">
                            <label for="chartType_prestasi" class="block mb-1 font-semibold">Jenis Chart:</label>
                            <select id="chartType_prestasi" data-section="prestasi" data-field="chart_type"
                                class="chart-type-dropdown bg-white border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring">
                                <option value="bar" {{ ($sections['prestasi']->chart_type ?? 'bar') === 'bar' ? 'selected' : '' }}>Bar</option>
                                <option value="line" {{ ($sections['prestasi']->chart_type ?? 'bar') === 'line' ? 'selected' : '' }}>Line</option>
                                <option value="pie" {{ ($sections['prestasi']->chart_type ?? 'bar') === 'pie' ? 'selected' : '' }}>Pie</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endif

            <ul class="text-green-600 font-semibold">
                @if(is_array($dataMahasiswa) && !empty($dataMahasiswa))
                    @foreach($dataMahasiswa as $year => $jumlah)
                        <li>Angkatan {{ $year }}: {{ $jumlah }} mahasiswa</li>
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
                    const chartType = "{{ $sections['prestasi']->chart_type ?? 'bar' }}"; // Ambil jenis chart dari database

                    // Definisikan warna yang berbeda untuk setiap angkatan
                    const backgroundColors = [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(199, 199, 199, 0.2)'
                    ];

                    const borderColors = [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)'
                    ];

                    // Jika chart type adalah pie, sesuaikan beberapa opsi
                    const optionsChart = {};
                    if (chartType === 'pie') {
                        optionsChart.plugins = {
                            legend: { display: true },
                            title: {
                                display: true,
                                text: "Jumlah Mahasiswa per Angkatan untuk Prodi {{ $prodiList[$prodi] ?? '-' }}"
                            }
                        };
                    } else {
                        optionsChart.plugins = {
                            legend: { display: true },
                            title: {
                                display: true,
                                text: "Jumlah Mahasiswa per Angkatan untuk Prodi {{ $prodiList[$prodi] ?? '-' }}"
                            }
                        };
                        optionsChart.scales = {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    display: true
                                }
                            },
                            x: {
                                grid: {
                                    display: true
                                }
                            }
                        };
                    }

                    const semuaAngkatanChart = new Chart("semuaAngkatanChart", {
                        type: chartType,
                        data: {
                            labels: angkatanLabels,
                            datasets: [{
                                label: 'Jumlah Mahasiswa per Angkatan',
                                data: angkatanCounts,
                                backgroundColor: chartType === 'pie' ? backgroundColors.slice(0, angkatanLabels.length) : 'rgba(75, 192, 192, 0.2)',
                                borderColor: chartType === 'pie' ? borderColors.slice(0, angkatanLabels.length) : 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: optionsChart
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
                        <!-- Dropdown chart type -->
                        <div class="chart-type-selector mt-2" style="display:none;">
                            <label for="chartType_kegiatan_luar_kampus" class="block mb-1 font-semibold">Jenis Chart:</label>
                            <select id="chartType_kegiatan_luar_kampus" data-section="kegiatan_luar_kampus"
                                data-field="chart_type"
                                class="chart-type-dropdown bg-white border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring">
                                <option value="bar" {{ ($sections['kegiatan_luar_kampus']->chart_type ?? 'bar') === 'bar' ? 'selected' : '' }}>Bar</option>
                                <option value="line" {{ ($sections['kegiatan_luar_kampus']->chart_type ?? 'bar') === 'line' ? 'selected' : '' }}>Line</option>
                                <option value="pie" {{ ($sections['kegiatan_luar_kampus']->chart_type ?? 'bar') === 'pie' ? 'selected' : '' }}>Pie</option>
                            </select>
                        </div>
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
                    const chartType = "{{ $sections['kegiatan_luar_kampus']->chart_type ?? 'bar' }}"; // Ambil jenis chart dari database

                    // Definisikan warna yang berbeda untuk setiap prodi
                    const backgroundColors = [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(199, 199, 199, 0.2)'
                    ];

                    const borderColors = [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)'
                    ];

                    // Jika chart type adalah pie, sesuaikan beberapa opsi
                    const optionsChart = {};
                    if (chartType === 'pie') {
                        optionsChart.plugins = {
                            legend: { display: true },
                            title: {
                                display: true,
                                text: "Jumlah Mahasiswa per Prodi untuk Angkatan {{ $angkatan }}"
                            }
                        };
                    } else {
                        optionsChart.plugins = {
                            legend: { display: true },
                            title: {
                                display: true,
                                text: "Jumlah Mahasiswa per Prodi untuk Angkatan {{ $angkatan }}"
                            }
                        };
                        optionsChart.scales = {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    display: true
                                }
                            },
                            x: {
                                grid: {
                                    display: true
                                }
                            }
                        };
                    }

                    const semuaProdiChart = new Chart("semuaProdiChart", {
                        type: chartType,
                        data: {
                            labels: prodiLabels,
                            datasets: [{
                                label: 'Jumlah Mahasiswa per Prodi',
                                data: prodiCounts,
                                backgroundColor: chartType === 'pie' ? backgroundColors.slice(0, prodiLabels.length) : 'rgba(75, 192, 192, 0.2)',
                                borderColor: chartType === 'pie' ? borderColors.slice(0, prodiLabels.length) : 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: optionsChart
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
            <p class="text-gray-600 mb-4 editable" data-section="total_mahasiswa_aktif" data-field="description">
                {!! $sections['total_mahasiswa_aktif']->description ?? 'Deskripsi Default' !!}
            </p>

            <!-- Inisialisasi Chart untuk Total Mahasiswa Aktif -->
            <div class="flex justify-center mb-5">
                <div class="w-full" style="width: 80%;">
                    <canvas id="totalMahasiswaAktifChart" class="mt-6"></canvas>
                    <!-- Dropdown chart type -->
                    <div class="chart-type-selector mt-2" style="display:none;">
                        <label for="chartType_total_mahasiswa_aktif_single" class="block mb-1 font-semibold">Jenis
                            Chart:</label>
                        <select id="chartType_total_mahasiswa_aktif_single" data-section="total_mahasiswa_aktif"
                            data-field="chart_type"
                            class="chart-type-dropdown bg-white border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring">
                            <option value="bar" {{ ($sections['total_mahasiswa_aktif']->chart_type ?? 'bar') === 'bar' ? 'selected' : '' }}>Bar</option>
                            <option value="line" {{ ($sections['total_mahasiswa_aktif']->chart_type ?? 'bar') === 'line' ? 'selected' : '' }}>Line</option>
                            <option value="pie" {{ ($sections['total_mahasiswa_aktif']->chart_type ?? 'bar') === 'pie' ? 'selected' : '' }}>Pie</option>
                        </select>
                    </div>
                </div>
            </div>

            <p class="text-green-600 font-semibold">
                Total Mahasiswa Aktif: {{ $dataMahasiswa['total'] }}
            </p>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const ctxTotal = document.getElementById('totalMahasiswaAktifChart').getContext('2d');
                    const chartType = "{{ $sections['total_mahasiswa_aktif']->chart_type ?? 'bar' }}"; // Ambil jenis chart dari database
                    const totalCount = {{ $dataMahasiswa['total'] ?? 0 }};

                    // Definisikan warna yang berbeda jika chart type adalah pie
                    const backgroundColors = [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)',
                        'rgba(199, 199, 199, 0.2)'
                    ];

                    const borderColors = [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)'
                    ];

                    // Jika chart type adalah pie, sesuaikan beberapa opsi
                    const optionsChart = {};
                    if (chartType === 'pie') {
                        optionsChart.plugins = {
                            legend: { display: true },
                            title: {
                                display: true,
                                text: 'Total Mahasiswa Aktif'
                            }
                        };
                    } else {
                        optionsChart.plugins = {
                            legend: { display: true },
                            title: {
                                display: true,
                                text: 'Total Mahasiswa Aktif'
                            }
                        };
                        optionsChart.scales = {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    display: true
                                }
                            },
                            x: {
                                grid: {
                                    display: true
                                }
                            }
                        };
                    }

                    const totalChart = new Chart(ctxTotal, {
                        type: chartType,
                        data: {
                            labels: ['Mahasiswa Aktif'],
                            datasets: [{
                                label: 'Total Mahasiswa Aktif',
                                data: [totalCount],
                                backgroundColor: chartType === 'pie' ? backgroundColors.slice(0, 1) : ['rgba(75, 192, 192, 0.2)'],
                                borderColor: chartType === 'pie' ? borderColors.slice(0, 1) : ['rgba(75, 192, 192, 1)'],
                                borderWidth: 1
                            }]
                        },
                        options: optionsChart
                    });
                });
            </script>
        @else
            <!-- Fallback -->
            <p class="text-red-500">Data belum tersedia.</p>
        @endif
    </div>

    <!-- Prestasi Section -->
    <div class="bg-white shadow-md rounded-lg p-6 mt-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 editable" data-section="prestasi" data-field="title">
            {!! $sections['prestasi']->title ?? 'Prestasi' !!}
        </h1>
        <p class="text-gray-600 mb-4 editable" data-section="prestasi" data-field="description">
            {!! $sections['prestasi']->description ?? 'Deskripsi Default' !!}
        </p>
        <!-- Tambahkan konten lainnya untuk Prestasi jika diperlukan -->

        <!-- Form Filter Prestasi -->
        <form id="filterPrestasi" method="GET" action="{{ route(Auth::check() ? 'home.auth' : 'home.public') }}"
            class="mb-4 space-y-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Filter by:</label>
                <input type="radio" id="tahun" name="waktu" value="tahun" {{ request('waktu') != 'semester' ? 'checked' : '' }}>
                <label for="tahun">Tahun</label><br>
                <input type="radio" id="semester" name="waktu" value="semester" {{ request('waktu') == 'semester' ? 'checked' : '' }}>
                <label for="semester">Semester</label>
            </div>
        </form>
        <div class="flex justify-center mb-5">
            <div class="w-full" style="width: 80%;">
                <canvas id="prestasiTahun"
                    style="{{ request('waktu') == 'semester' ? 'display: none;' : '' }}"></canvas>
                <canvas id="prestasiSemester"
                    style="{{ request('waktu') == 'semester' ? '' : 'display: none;' }}"></canvas>
                <!-- Dropdown chart type for Prestasi Tahun -->
                <div class="chart-type-selector mt-2" style="display:none;">
                    <label for="chartType_prestasi_tahun" class="block mb-1 font-semibold">Jenis Chart:</label>
                    <select id="chartType_prestasi_tahun" data-section="prestasi" data-field="chart_type"
                        class="chart-type-dropdown bg-white border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring">
                        <option value="bar" {{ ($sections['prestasi']->chart_type ?? 'bar') === 'bar' ? 'selected' : '' }}>Bar</option>
                        <option value="line" {{ ($sections['prestasi']->chart_type ?? 'bar') === 'line' ? 'selected' : '' }}>Line</option>
                        <option value="pie" {{ ($sections['prestasi']->chart_type ?? 'bar') === 'pie' ? 'selected' : '' }}>Pie</option>
                    </select>
                </div>
                <!-- Dropdown chart type for Prestasi Semester -->
                <div class="chart-type-selector mt-2" style="display:none;">
                    <label for="chartType_prestasi_semester" class="block mb-1 font-semibold">Jenis Chart:</label>
                    <select id="chartType_prestasi_semester" data-section="prestasi" data-field="chart_type"
                        class="chart-type-dropdown bg-white border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring">
                        <option value="bar" {{ ($sections['prestasi']->chart_type ?? 'bar') === 'bar' ? 'selected' : '' }}>Bar</option>
                        <option value="line" {{ ($sections['prestasi']->chart_type ?? 'bar') === 'line' ? 'selected' : '' }}>Line</option>
                        <option value="pie" {{ ($sections['prestasi']->chart_type ?? 'bar') === 'pie' ? 'selected' : '' }}>Pie</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Kegiatan Luar Kampus Section -->
    <div class="bg-white shadow-md rounded-lg p-6 mt-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 editable" data-section="kegiatan_luar_kampus"
            data-field="title">
            {!! $sections['kegiatan_luar_kampus']->title ?? 'Jumlah Mahasiswa yang Mengikuti Kegiatan di Luar Kampus' !!}
        </h1>
        <p class="text-gray-600 mb-4 editable" data-section="kegiatan_luar_kampus" data-field="description">
            {!! $sections['kegiatan_luar_kampus']->description ?? 'Deskripsi Default' !!}
        </p>
        <div class="space-y-4">
            <h5 class="font-bold text-gray-700">1. MBKM</h5>
            <p class="text-gray-600">
                Program MBKM (Merdeka Belajar Kampus Merdeka) memberikan mahasiswa kesempatan untuk belajar di luar
                kelas,
                mengembangkan keterampilan praktis, dan berkontribusi pada masyarakat. Melalui program ini,
                mahasiswa dapat
                mengikuti magang, proyek sosial, dan kegiatan lainnya yang mendukung pembelajaran holistik.
            </p>

            <h5 class="font-bold text-gray-700">2. IISMA</h5>
            <p class="text-gray-600">
                IISMA (<i>Indonesian International Student Mobility Awards</i>) adalah program yang memungkinkan
                mahasiswa
                Indonesia untuk belajar di universitas luar negeri. Program ini bertujuan untuk memperluas wawasan
                global
                mahasiswa, meningkatkan kemampuan bahasa, dan membangun jaringan internasional yang bermanfaat untuk
                karier
                mereka di masa depan.
            </p>

            <h5 class="font-bold text-gray-700">3. Kerja Praktik</h5>
            <p class="text-gray-600">
                Kerja praktik memberikan mahasiswa pengalaman langsung di dunia kerja, memungkinkan mereka untuk
                menerapkan
                teori yang telah dipelajari di kampus. Melalui kerja praktik, mahasiswa dapat mengembangkan
                keterampilan
                profesional dan membangun koneksi dengan industri.
            </p>

            <h5 class="font-bold text-gray-700">4. Studi Independent</h5>
            <p class="text-gray-600">
                Studi independent adalah kesempatan bagi mahasiswa untuk mengeksplorasi topik atau proyek penelitian
                secara
                mandiri. Program ini mendorong kreativitas dan inisiatif, memungkinkan mahasiswa untuk mendalami
                minat pribadi
                dan mengembangkan kemampuan analitis.
            </p>

            <h5 class="font-bold text-gray-700">5. Pertukaran Pelajar</h5>
            <p class="text-gray-600">
                Program pertukaran pelajar memungkinkan mahasiswa untuk belajar di institusi pendidikan di negara
                lain selama
                periode tertentu. Ini memberikan pengalaman budaya yang berharga, memperluas perspektif akademik,
                dan
                meningkatkan kemampuan adaptasi di lingkungan internasional.
            </p>
        </div>
        <div class="flex justify-center mb-5">
            <div class="w-full" style="width: 80%;">
                <canvas id="jlhMahasiswaKegiatanChart"></canvas>
                <!-- Dropdown chart type -->
                <div class="chart-type-selector mt-2" style="display:none;">
                    <label for="chartType_jlhMahasiswaKegiatan" class="block mb-1 font-semibold">Jenis Chart:</label>
                    <select id="chartType_jlhMahasiswaKegiatan" data-section="kegiatan_luar_kampus"
                        data-field="chart_type"
                        class="chart-type-dropdown bg-white border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring">
                        <option value="bar" {{ ($sections['kegiatan_luar_kampus']->chart_type ?? 'bar') === 'bar' ? 'selected' : '' }}>Bar</option>
                        <option value="line" {{ ($sections['kegiatan_luar_kampus']->chart_type ?? 'bar') === 'line' ? 'selected' : '' }}>Line</option>
                        <option value="pie" {{ ($sections['kegiatan_luar_kampus']->chart_type ?? 'bar') === 'pie' ? 'selected' : '' }}>Pie</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Script untuk Charts -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Chart untuk Prestasi Tahun
        const prestasiTahunCanvas = document.getElementById('prestasiTahun');
        if (prestasiTahunCanvas) {
            const dummyPrestasiTahunData = [137, 145, 137, 159, 156, 151]; // Data Dummy
            const chartTypePrestasiTahun = "{{ $sections['prestasi']->chart_type ?? 'bar' }}"; // Ambil jenis chart dari database

            // Definisikan warna yang berbeda untuk setiap dataset jika diperlukan
            const backgroundColors = [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)'
            ];

            const borderColors = [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)'
            ];

            // Jika chart type adalah pie, sesuaikan beberapa opsi
            const optionsChart = {};
            if (chartTypePrestasiTahun === 'pie') {
                optionsChart.plugins = {
                    legend: { display: true },
                    title: {
                        display: true,
                        text: 'Jumlah Prestasi/Tahun'
                    }
                };
            } else {
                optionsChart.plugins = {
                    legend: { display: true },
                    title: {
                        display: true,
                        text: 'Jumlah Prestasi/Tahun'
                    }
                };
                optionsChart.scales = {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            display: true
                        }
                    },
                    x: {
                        grid: {
                            display: true
                        }
                    }
                };
            }

            const prestasiTahunChart = new Chart(prestasiTahunCanvas, {
                type: chartTypePrestasiTahun,
                data: {
                    labels: @json($angkatanYears),
                    datasets: [
                        {
                            label: 'Akademik',
                            data: dummyPrestasiTahunData,
                            backgroundColor: chartTypePrestasiTahun === 'pie' ? backgroundColors.slice(0, dummyPrestasiTahunData.length) : 'rgba(54, 162, 235, 0.2)',
                            borderColor: chartTypePrestasiTahun === 'pie' ? borderColors.slice(0, dummyPrestasiTahunData.length) : 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Non-Akademik',
                            data: [17, 15, 13, 19, 16, 21], // Data Dummy
                            backgroundColor: chartTypePrestasiTahun === 'pie' ? backgroundColors.slice(0, 2) : 'rgba(201, 203, 207, 0.2)',
                            borderColor: chartTypePrestasiTahun === 'pie' ? borderColors.slice(0, 2) : 'rgba(201, 203, 207, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: optionsChart
            });
        }

        // Chart untuk Prestasi Semester
        const prestasiSemesterCanvas = document.getElementById('prestasiSemester');
        if (prestasiSemesterCanvas) {
            const dummyPrestasiSemesterData = [43, 45]; // Data Dummy
            const chartTypePrestasiSemester = "{{ $sections['prestasi']->chart_type ?? 'bar' }}"; // Ambil jenis chart dari database

            // Definisikan warna yang berbeda untuk setiap dataset jika diperlukan
            const backgroundColors = [
                'rgba(75, 192, 192, 0.2)',
                'rgba(201, 203, 207, 0.2)'
            ];

            const borderColors = [
                'rgba(75, 192, 192, 1)',
                'rgba(201, 203, 207, 1)'
            ];

            // Jika chart type adalah pie, sesuaikan beberapa opsi
            const optionsChart = {};
            if (chartTypePrestasiSemester === 'pie') {
                optionsChart.plugins = {
                    legend: { display: true },
                    title: {
                        display: true,
                        text: 'Jumlah Prestasi/Semester'
                    }
                };
            } else {
                optionsChart.plugins = {
                    legend: { display: true },
                    title: {
                        display: true,
                        text: 'Jumlah Prestasi/Semester'
                    }
                };
                optionsChart.scales = {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            display: true
                        }
                    },
                    x: {
                        grid: {
                            display: true
                        }
                    }
                };
            }

            const prestasiSemesterChart = new Chart(prestasiSemesterCanvas, {
                type: chartTypePrestasiSemester,
                data: {
                    labels: ["Ganjil", "Genap"],
                    datasets: [
                        {
                            label: 'Akademik',
                            data: dummyPrestasiSemesterData,
                            backgroundColor: chartTypePrestasiSemester === 'pie' ? backgroundColors.slice(0, dummyPrestasiSemesterData.length) : 'rgba(75, 192, 192, 0.2)',
                            borderColor: chartTypePrestasiSemester === 'pie' ? borderColors.slice(0, dummyPrestasiSemesterData.length) : 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Non-Akademik',
                            data: [4, 5], // Data Dummy
                            backgroundColor: chartTypePrestasiSemester === 'pie' ? backgroundColors.slice(0, 2) : 'rgba(201, 203, 207, 0.2)',
                            borderColor: chartTypePrestasiSemester === 'pie' ? borderColors.slice(0, 2) : 'rgba(201, 203, 207, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: optionsChart
            });
        }

        // Chart untuk Jumlah Mahasiswa Kegiatan
        const kegiatanCanvas = document.getElementById('jlhMahasiswaKegiatanChart');
        if (kegiatanCanvas) {
            const dummyKegiatanData = [137, 145, 137, 159, 156]; // Data Dummy
            const chartTypeKegiatan = "{{ $sections['kegiatan_luar_kampus']->chart_type ?? 'bar' }}"; // Ambil jenis chart dari database

            // Definisikan warna yang berbeda untuk setiap kegiatan
            const backgroundColors = [
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)',
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(75, 192, 192, 0.2)'
            ];

            const borderColors = [
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(75, 192, 192, 1)'
            ];

            // Jika chart type adalah pie, sesuaikan beberapa opsi
            const optionsChart = {};
            if (chartTypeKegiatan === 'pie') {
                optionsChart.plugins = {
                    legend: { display: true },
                    title: {
                        display: true,
                        text: "Jumlah Mahasiswa yang Mengikuti Kegiatan di Luar Kampus"
                    }
                };
            } else {
                optionsChart.plugins = {
                    legend: { display: true },
                    title: {
                        display: true,
                        text: "Jumlah Mahasiswa yang Mengikuti Kegiatan di Luar Kampus"
                    }
                };
                optionsChart.scales = {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            display: true
                        }
                    },
                    x: {
                        grid: {
                            display: true
                        }
                    }
                };
            }

            const kegiatanChart = new Chart(kegiatanCanvas, {
                type: chartTypeKegiatan,
                data: {
                    labels: ["MBKM", "IISMA", "Kerja Praktik", "Studi Independent", "Pertukaran Pelajar"],
                    datasets: [{
                        label: 'Jumlah Mahasiswa',
                        data: dummyKegiatanData,
                        backgroundColor: chartTypeKegiatan === 'pie' ? backgroundColors.slice(0, dummyKegiatanData.length) : 'rgba(153, 102, 255, 0.2)',
                        borderColor: chartTypeKegiatan === 'pie' ? borderColors.slice(0, dummyKegiatanData.length) : 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: optionsChart
            });
        }
    });
</script>


<!-- Edit Section Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const editButton = document.getElementById("editButton");
        const editIcon = document.getElementById("editIcon");
        const editText = document.getElementById("editText");
        const editableElements = document.querySelectorAll(".editable");
        const chartTypeDropdowns = document.querySelectorAll(".chart-type-dropdown");

        let isEditing = false;

        // Object to track changes per section and field
        const changes = {};

        editButton.addEventListener("click", () => {
            isEditing = !isEditing;

            // Toggle contentEditable for editable elements (text/title/description)
            editableElements.forEach((element) => {
                if (isEditing) {
                    element.contentEditable = true;
                    element.style.border = "1px dashed gray";

                    const sectionKey = element.getAttribute("data-section");
                    const fieldKey = element.getAttribute("data-field");
                    if (!changes[sectionKey]) {
                        changes[sectionKey] = {};
                    }

                    // Store original values
                    if (!changes[sectionKey].original) {
                        changes[sectionKey].original = {};
                    }
                    if (!changes[sectionKey].original[fieldKey]) {
                        changes[sectionKey].original[fieldKey] = element.innerHTML.trim();
                    }

                } else {
                    element.contentEditable = false;
                    element.style.border = "none";

                    const sectionKey = element.getAttribute("data-section");
                    const fieldKey = element.getAttribute("data-field");
                    const updatedValue = element.innerHTML.trim();

                    if (changes[sectionKey].original[fieldKey] !== updatedValue) {
                        if (!changes[sectionKey].updated) {
                            changes[sectionKey].updated = {};
                        }
                        changes[sectionKey].updated[fieldKey] = updatedValue;
                    }
                }
            });

            // Toggle chartTypeDropdowns
            chartTypeDropdowns.forEach((dropdown) => {
                const sectionKey = dropdown.getAttribute("data-section");
                const fieldKey = dropdown.getAttribute("data-field");
                if (isEditing) {
                    // Show and enable dropdown
                    dropdown.parentElement.style.display = "block";
                    dropdown.disabled = false;

                    // Store original chart_type if not stored
                    if (!changes[sectionKey]) {
                        changes[sectionKey] = {};
                    }
                    if (!changes[sectionKey].original) {
                        changes[sectionKey].original = {};
                    }
                    if (!changes[sectionKey].original[fieldKey]) {
                        changes[sectionKey].original[fieldKey] = dropdown.value;
                    }

                } else {
                    // Hide dropdown after done if needed
                    // Atur ulang display dan disable dropdown
                    dropdown.disabled = true;
                    // dropdown.parentElement.style.display = "none"; // Jika ingin disembunyikan setelah done

                    const updatedValue = dropdown.value;
                    if (changes[sectionKey].original[fieldKey] !== updatedValue) {
                        if (!changes[sectionKey].updated) {
                            changes[sectionKey].updated = {};
                        }
                        changes[sectionKey].updated[fieldKey] = updatedValue;
                    }
                }
            });

            // Toggle button icon and text
            editIcon.classList.toggle("bi-pencil", !isEditing);
            editIcon.classList.toggle("bi-check-circle", isEditing);
            editIcon.style.color = isEditing ? "green" : "orange";
            editText.textContent = isEditing ? "Done" : "Edit";
            editText.style.color = isEditing ? "green" : "orange";

            if (!isEditing) {
                // Save changes after exiting edit mode
                saveChanges();
            }
        });

        // Handle Enter key in editable elements
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

        function saveChanges() {
            const payload = {};

            // Compile only updated fields into the payload
            for (const sectionKey in changes) {
                if (changes[sectionKey].updated) {
                    payload[sectionKey] = changes[sectionKey].updated;
                }
            }

            if (Object.keys(payload).length === 0) {
                Swal.fire("Info", "Tidak ada perubahan untuk disimpan.", "info");
                return;
            }

            // Send data to the server
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
                        // Construct error messages
                        let errorMessages = "";
                        if (data.errors) {
                            for (const field in data.errors) {
                                errorMessages += `${field}: ${data.errors[field].join(", ")}\n`;
                            }
                        } else {
                            errorMessages = data.message || "Terjadi kesalahan.";
                        }
                        Swal.fire("Error", "Terjadi kesalahan: " + errorMessages, "error");
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    Swal.fire("Error", "Gagal menyimpan perubahan.", "error");
                });
        }
    });
</script>
@endsection