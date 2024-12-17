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
            data-type="title">
            {!! $sections['total_mahasiswa_aktif']->title ?? 'Total Mahasiswa Aktif' !!}
        </h1>
        <p class="text-gray-600 mb-4 editable" data-section="total_mahasiswa_aktif" data-type="description">
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

        @php
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

                <!-- MODIFIKASI UNTUK CHART TYPE EDIT: Dropdown untuk total_mahasiswa_aktif -->
                <div id="chartTypeContainerTotalMahasiswa" class="hidden mb-4">
                    <label for="chartTypeTotalMahasiswa" class="block text-gray-700 font-semibold mb-2">Pilih Jenis
                        Chart:</label>
                    <select id="chartTypeTotalMahasiswa"
                        class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
                        data-section="total_mahasiswa_aktif" data-type="chart_type">
                        <option value="bar" {{ ($sections['total_mahasiswa_aktif']->chart_type ?? 'bar') == 'bar' ? 'selected' : '' }}>Bar</option>
                        <option value="line" {{ ($sections['total_mahasiswa_aktif']->chart_type ?? 'bar') == 'line' ? 'selected' : '' }}>Line</option>
                        <option value="pie" {{ ($sections['total_mahasiswa_aktif']->chart_type ?? 'bar') == 'pie' ? 'selected' : '' }}>Pie</option>
                    </select>
                </div>
            @endif

            <ul class="text-green-600 font-semibold">
                @if(!empty($dataMahasiswa))
                    @foreach ($dataMahasiswa as $prodiName => $jumlah)
                        @if($prodiName !== 'total')
                            <li>{{ $prodiName }}: {{ $jumlah }} mahasiswa</li>
                        @endif
                    @endforeach
                    <li class="font-bold text-green-600">Total mahasiswa: {{ $dataMahasiswa['total'] ?? 0 }} mahasiswa</li>
                @else
                    <li class="text-red-500">Data belum tersedia.</li>
                @endif
            </ul>

            @if(!empty($dataMahasiswa))
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        let semuaProdiAngkatanChart;

                        function initializeSemuaProdiAngkatanChart(chartType) {
                            const prodiLabels = [];
                            const prodiCounts = [];

                            @foreach($dataMahasiswa as $prodiName => $jumlah)
                                @if($prodiName !== 'total')
                                    prodiLabels.push("{{ $prodiName }}");
                                    prodiCounts.push({{ $jumlah }});
                                @endif
                            @endforeach

                            const ctxSemuaProdiAngkatan = document.getElementById('semuaProdiAngkatanChart').getContext('2d');
                            semuaProdiAngkatanChart = new Chart(ctxSemuaProdiAngkatan, {
                                type: chartType,
                                data: {
                                    labels: prodiLabels,
                                    datasets: [{
                                        label: 'Jumlah Mahasiswa per Prodi',
                                        data: prodiCounts,
                                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    plugins: {
                                        legend: { display: true },
                                        title: {
                                            display: true,
                                            text: 'Jumlah Mahasiswa di Semua Prodi dan Semua Angkatan'
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: { precision: 0 }
                                        }
                                    }
                                }
                            });
                            return semuaProdiAngkatanChart;
                        }

                        let currentChartType = "{{ $sections['total_mahasiswa_aktif']->chart_type ?? 'bar' }}";
                        let currentChart = initializeSemuaProdiAngkatanChart(currentChartType);

                        const chartTypeSelectTotalMahasiswa = document.getElementById("chartTypeTotalMahasiswa");
                        if (chartTypeSelectTotalMahasiswa) {
                            chartTypeSelectTotalMahasiswa.addEventListener("change", function () {
                                const selectedChartType = this.value;
                                currentChart.destroy();
                                currentChart = initializeSemuaProdiAngkatanChart(selectedChartType);

                                // Simpan perubahan chart_type
                                const sectionKey = this.getAttribute("data-section");
                                if (!window.sectionChanges) {
                                    window.sectionChanges = {};
                                }
                                if (!window.sectionChanges[sectionKey]) {
                                    window.sectionChanges[sectionKey] = {};
                                }
                                window.sectionChanges[sectionKey].updatedChartType = selectedChartType;
                            });
                        }

                        window.initializeSemuaProdiAngkatanChart = initializeSemuaProdiAngkatanChart;
                        window.currentChartTotalMahasiswa = currentChart;
                    });
                </script>
            @endif
        @elseif(!$angkatan && $prodi)
            <!-- Kondisi lain, Anda bisa menambahkan dropdown chart_type dengan logika yang sama -->
            <!-- ... -->
        @elseif($angkatan && !$prodi)
            <!-- Kondisi lain, Anda bisa menambahkan dropdown chart_type dengan logika yang sama -->
            <!-- ... -->
        @elseif($angkatan && $prodi)
            <!-- Kondisi lain, data statis (tanpa chart), tidak perlu dropdown -->
            <!-- ... -->
        @elseif(isset($dataMahasiswa['total']))
            <!-- Kondisi lain dengan chart, sama seperti di atas, tambahkan dropdown chart_type -->
            <!-- ... -->
        @else
            <p class="text-red-500">Data belum tersedia.</p>
        @endif
    </div>

    <!-- Prestasi Section -->
    <div class="bg-white shadow-md rounded-lg p-6 mt-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 editable" data-section="prestasi" data-type="title">
            {!! $sections['prestasi']->title ?? 'Prestasi' !!}
        </h1>
        <p class="text-gray-600 mb-4 editable" data-section="prestasi" data-type="description">
            {!! $sections['prestasi']->description ?? 'Deskripsi Default' !!}
        </p>

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
            </div>
        </div>

        <!-- MODIFIKASI UNTUK CHART TYPE EDIT: Dropdown untuk prestasi -->
        <!-- Tampilkan hanya jika salah satu chart prestasi terlihat -->
        @if(request('waktu') == 'semester' || !request('waktu'))
            <div id="chartTypeContainerPrestasi" class="hidden mb-4">
                <label for="chartTypePrestasi" class="block text-gray-700 font-semibold mb-2">Pilih Jenis Chart
                    (Prestasi):</label>
                <select id="chartTypePrestasi"
                    class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
                    data-section="prestasi" data-type="chart_type">
                    <option value="bar" {{ ($sections['prestasi']->chart_type ?? 'bar') == 'bar' ? 'selected' : '' }}>Bar
                    </option>
                    <option value="line" {{ ($sections['prestasi']->chart_type ?? 'bar') == 'line' ? 'selected' : '' }}>Line
                    </option>
                    <option value="pie" {{ ($sections['prestasi']->chart_type ?? 'bar') == 'pie' ? 'selected' : '' }}>Pie
                    </option>
                </select>
            </div>
        @endif
    </div>

    <!-- Kegiatan Luar Kampus Section -->
    <div class="bg-white shadow-md rounded-lg p-6 mt-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 editable" data-section="kegiatan_luar_kampus"
            data-type="title">
            {!! $sections['kegiatan_luar_kampus']->title ?? 'Jumlah Mahasiswa yang Mengikuti Kegiatan di Luar Kampus' !!}
        </h1>
        <p class="text-gray-600 mb-4 editable" data-section="kegiatan_luar_kampus" data-type="description">
            {!! $sections['kegiatan_luar_kampus']->description ?? 'Deskripsi Default' !!}
        </p>
        <div class="space-y-4">
            <!-- Deskripsi Kegiatan -->
            <!-- ... (sama seperti sebelumnya) -->
        </div>
        <div class="flex justify-center mb-5">
            <div class="w-full" style="width: 80%;">
                <canvas id="jlhMahasiswaKegiatanChart"></canvas>
            </div>
        </div>

        <!-- MODIFIKASI UNTUK CHART TYPE EDIT: Dropdown untuk kegiatan_luar_kampus -->
        <div id="chartTypeContainerKegiatanLuar" class="hidden mb-4">
            <label for="chartTypeKegiatanLuar" class="block text-gray-700 font-semibold mb-2">Pilih Jenis Chart
                (Kegiatan Luar):</label>
            <select id="chartTypeKegiatanLuar"
                class="block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-500 px-4 py-2"
                data-section="kegiatan_luar_kampus" data-type="chart_type">
                <option value="bar" {{ ($sections['kegiatan_luar_kampus']->chart_type ?? 'bar') == 'bar' ? 'selected' : '' }}>Bar</option>
                <option value="line" {{ ($sections['kegiatan_luar_kampus']->chart_type ?? 'bar') == 'line' ? 'selected' : '' }}>Line</option>
                <option value="pie" {{ ($sections['kegiatan_luar_kampus']->chart_type ?? 'bar') == 'pie' ? 'selected' : '' }}>Pie</option>
            </select>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Prestasi Chart Inisialisasi
        const prestasiTahunCanvas = document.getElementById('prestasiTahun');
        const prestasiSemesterCanvas = document.getElementById('prestasiSemester');
        let prestasiChart;
        let currentPrestasiChartType = "{{ $sections['prestasi']->chart_type ?? 'bar' }}";

        function initializePrestasiChart(chartType) {
            // Hancurkan chart lama jika ada
            if (prestasiChart) {
                prestasiChart.destroy();
            }

            if (prestasiSemesterCanvas && prestasiSemesterCanvas.style.display !== 'none') {
                // Mode semester
                prestasiChart = new Chart(prestasiSemesterCanvas.getContext('2d'), {
                    type: chartType,
                    data: {
                        labels: ["Ganjil", "Genap"],
                        datasets: [
                            {
                                label: 'Akademik',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1,
                                data: [43, 45]
                            },
                            {
                                label: 'Non-Akademik',
                                backgroundColor: 'rgba(201, 203, 207, 0.2)',
                                borderColor: 'rgba(201, 203, 207, 1)',
                                borderWidth: 1,
                                data: [4, 5]
                            }
                        ]
                    },
                    options: {
                        plugins: {
                            legend: { display: true },
                            title: {
                                display: true,
                                text: 'Jumlah Prestasi/Semester'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            }
                        }
                    }
                });
            } else if (prestasiTahunCanvas && prestasiTahunCanvas.style.display !== 'none') {
                // Mode tahun
                prestasiChart = new Chart(prestasiTahunCanvas.getContext('2d'), {
                    type: chartType,
                    data: {
                        labels: @json($angkatanYears),
                        datasets: [
                            {
                                label: 'Akademik',
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                                data: [137, 145, 137, 159, 156, 151]
                            },
                            {
                                label: 'Non-Akademik',
                                backgroundColor: 'rgba(201, 203, 207, 0.2)',
                                borderColor: 'rgba(201, 203, 207, 1)',
                                borderWidth: 1,
                                data: [17, 15, 13, 19, 16, 21]
                            }
                        ]
                    },
                    options: {
                        plugins: {
                            legend: { display: true },
                            title: {
                                display: true,
                                text: 'Jumlah Prestasi/Tahun'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            }
                        }
                    }
                });
            }
        }

        // Inisialisasi chart Prestasi jika ada
        if ((prestasiTahunCanvas && prestasiTahunCanvas.style.display !== 'none') ||
            (prestasiSemesterCanvas && prestasiSemesterCanvas.style.display !== 'none')) {
            initializePrestasiChart(currentPrestasiChartType);
        }

        const chartTypeSelectPrestasi = document.getElementById("chartTypePrestasi");
        if (chartTypeSelectPrestasi) {
            chartTypeSelectPrestasi.addEventListener("change", function () {
                const selectedChartType = this.value;
                initializePrestasiChart(selectedChartType);

                // Simpan perubahan chart_type
                const sectionKey = this.getAttribute("data-section");
                if (!window.sectionChanges) {
                    window.sectionChanges = {};
                }
                if (!window.sectionChanges[sectionKey]) {
                    window.sectionChanges[sectionKey] = {};
                }
                window.sectionChanges[sectionKey].updatedChartType = selectedChartType;
            });
        }

        // Kegiatan Luar Kampus Chart Inisialisasi
        const kegiatanCanvas = document.getElementById('jlhMahasiswaKegiatanChart');
        let kegiatanChart;
        let currentKegiatanChartType = "{{ $sections['kegiatan_luar_kampus']->chart_type ?? 'bar' }}";

        function initializeKegiatanChart(chartType) {
            if (kegiatanChart) {
                kegiatanChart.destroy();
            }
            if (kegiatanCanvas) {
                const dummyKegiatanData = [137, 145, 137, 159, 156]; // Data Dummy
                kegiatanChart = new Chart(kegiatanCanvas.getContext('2d'), {
                    type: chartType,
                    data: {
                        labels: ["MBKM", "IISMA", "Kerja Praktik", "Studi Independent", "Pertukaran Pelajar"],
                        datasets: [{
                            label: 'Jumlah Mahasiswa',
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1,
                            data: dummyKegiatanData
                        }]
                    },
                    options: {
                        plugins: {
                            legend: { display: true },
                            title: {
                                display: true,
                                text: "Jumlah Mahasiswa yang Mengikuti Kegiatan di Luar Kampus"
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            }
                        }
                    }
                });
            }
        }

        if (kegiatanCanvas) {
            initializeKegiatanChart(currentKegiatanChartType);
        }

        const chartTypeSelectKegiatanLuar = document.getElementById("chartTypeKegiatanLuar");
        if (chartTypeSelectKegiatanLuar) {
            chartTypeSelectKegiatanLuar.addEventListener("change", function () {
                const selectedChartType = this.value;
                initializeKegiatanChart(selectedChartType);

                // Simpan perubahan chart_type
                const sectionKey = this.getAttribute("data-section");
                if (!window.sectionChanges) {
                    window.sectionChanges = {};
                }
                if (!window.sectionChanges[sectionKey]) {
                    window.sectionChanges[sectionKey] = {};
                }
                window.sectionChanges[sectionKey].updatedChartType = selectedChartType;
            });
        }

        // Simpan fungsi inisialisasi agar bisa diakses global (jika diperlukan)
        window.initializeKegiatanChart = initializeKegiatanChart;
    });
</script>

<!-- Edit Section Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const editButton = document.getElementById("editButton");
        const editIcon = document.getElementById("editIcon");
        const editText = document.getElementById("editText");
        const editableElements = document.querySelectorAll(".editable");

        const chartTypeContainerTotalMahasiswa = document.getElementById("chartTypeContainerTotalMahasiswa");
        const chartTypeContainerPrestasi = document.getElementById("chartTypeContainerPrestasi");
        const chartTypeContainerKegiatanLuar = document.getElementById("chartTypeContainerKegiatanLuar");

        let isEditing = false;

        const changes = {};
        if (!window.sectionChanges) {
            window.sectionChanges = {};
        }

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

            // Tampilkan/hilangkan dropdown chart type saat mode edit aktif (jika chart tersedia)
            if (chartTypeContainerTotalMahasiswa) {
                chartTypeContainerTotalMahasiswa.classList.toggle('hidden', !isEditing);
            }
            if (chartTypeContainerPrestasi && ((document.getElementById('prestasiTahun') && document.getElementById('prestasiTahun').style.display !== 'none')
                || (document.getElementById('prestasiSemester') && document.getElementById('prestasiSemester').style.display !== 'none'))) {
                chartTypeContainerPrestasi.classList.toggle('hidden', !isEditing);
            }
            if (chartTypeContainerKegiatanLuar && document.getElementById('jlhMahasiswaKegiatanChart')) {
                chartTypeContainerKegiatanLuar.classList.toggle('hidden', !isEditing);
            }

            // Ganti ikon dan teks tombol
            editIcon.classList.toggle("bi-pencil", !isEditing);
            editIcon.classList.toggle("bi-check-circle", isEditing);
            editIcon.style.color = isEditing ? "green" : "orange";
            editText.textContent = isEditing ? "Done" : "Edit";
            editText.style.color = isEditing ? "green" : "orange";

            if (!isEditing) {
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

        function saveChanges() {
            const payload = {};

            // Gabungkan perubahan title/description dari changes
            for (const sectionKey in changes) {
                if (!payload[sectionKey]) {
                    payload[sectionKey] = {};
                }
                if (changes[sectionKey].updatedTitle) {
                    payload[sectionKey].title = changes[sectionKey].updatedTitle;
                }
                if (changes[sectionKey].updatedDescription) {
                    payload[sectionKey].description = changes[sectionKey].updatedDescription;
                }
            }

            // Gabungkan perubahan chart_type dari window.sectionChanges
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

@endsection