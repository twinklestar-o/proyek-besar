<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Del</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
</head>

<body class="bg-gray-50">
    <!-- Include Header -->
    @include('layouts.headerbar')

    <!-- Include Sidebar -->
    @include('layouts.sidebar.sidebar_display')

    <!-- Main Content -->
    <main class="pl-64 mr-5 pt-20">
        <div class="flex flex-col md:flex-row w-full">
            <div class="w-6/12 sm:w-full d-flex content-center">
                <h1 class="text-2xl font-bold mb-1">Jumlah Mahasiswa</h1>
                <p class="mb-1">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Quo eveniet, tenetur saepe ullam doloremque tempora, incidunt natus eligendi id quaerat eum cumque nisi temporibus molestias quibusdam officiis! Eius, odit dicta!
                </p>
                <form>
                    <input type="radio" id="angkatan" name="jumlah" value="angkatan" checked>
                    <label for="angkatan">Angkatan</label><br>
                    <input type="radio" id="prodi" name="jumlah" value="prodi">
                    <label for="prodi">Program Studi</label><br>
                </form>
            </div>
            <div class="w-6/12 sm:w-full d-flex content-center">
                <canvas id="jlhMahasiswaAngkatan"></canvas>
                <canvas id="jlhMahasiswaProdi" style="display: none;"></canvas>
            </div>
            <div class="column"></div>
        </div>

        <div class="h-3"></div>

        <div class="flex flex-col md:flex-row w-full mb-4">
            <div class="w-6/12 sm:w-full d-flex content-center">
                <h1 class="text-2xl font-bold mb-1">Prestasi</h1>
                <p class="mb-1">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit. Quo eveniet, tenetur saepe ullam doloremque tempora, incidunt natus eligendi id quaerat eum cumque nisi temporibus molestias quibusdam officiis! Eius, odit dicta!
                </p>
                <form>
                    <input type="radio" id="tahun" name="waktu" value="tahun" checked>
                    <label for="tahun">Tahun</label><br>
                    <input type="radio" id="semester" name="waktu" value="semester">
                    <label for="semester">Semester</label><br>
                </form>
            </div>
            <div class="w-6/12 sm:w-full d-flex content-center">
                <canvas id="prestasiSemester" style="display: none;"></canvas>
                <canvas id="prestasiTahun"></canvas> <!-- Display this canvas by default -->
            </div>
            <div class="column"></div>
        </div>

        <div class="h-3"></div>

        <div class="flex flex-col md:flex-row w-full mb-5">
            <div class="w-6/12 sm:w-full d-flex content-center">
                <h1 class="text-2xl font-bold mb-1">Jumlah Mahasiswa yang Mengikuti Kegiatan di Luar Kampus</h1>
                <h5><b>1. MBKM</b></h5>
                <p class="mb-1">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore 
                </p>

                <h5 ><b>2. IISMA</b></h5>
                <p class="mb-1">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore 
                </p>

                <h5><b>3. Kerja Praktik</b></h5>
                <p class="mb-1">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore 
                </p>

                <h5><b>4. Studi Independent</b></h5>
                <p class="mb-1">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore 
                </p>

                <h5><b>5. Pertukaran Pelajar</b></h5>
                <p class="mb-1">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore 
                </p>
            </div>
            <div class="w-6/12 sm:w-full d-flex content-center">
                <canvas id="jlhMahasiswaKegiatanChart"></canvas>
            </div>
            <div class="column"></div>
        </div>
    </main>

    <script>
        // Warna Chart
        const barColors = ["#0078C4","#0078C4", "#0078C4", "#0078C4", "#0078C4", "#0078C4", "#0078C4", "#0078C4", "#0078C4"];

        // untuk chart jumlah Mahasiswa/Angkatan
        const angkatan = ["2019", "2020", "2021", "2022", "2023", "2024"];
        const jlhAngkatan = [1537, 1545, 1567, 1559, 1566, 1560];

        // untuk chart jumlah Mahasiswa/Tahun
        const prodi = ["S1 Informatika", "S1 Sistem Informasi", "S1 Teknik Elektro", "S1 Manajemen Rekayasa", "S1 Teknik Metalurgi", "S1 Teknik Bioproses", "D4 Teknologi Rekayasa Perangkat Lunak", "D3 Teknologi Informasi", "D3 Teknik Komputer"]; 
        const jlhProdi = [210, 450, 360, 210, 450, 360, 210, 450, 450];

        // PRESTASI
        const semester = ["Ganjil", "Genap"]; 
        const jlhPrestasiSemesterAkademik = [43, 45];
        const jlhPrestasiSemesterNonAkademik = [4, 5];
        const jlhPrestasiTahunAkademik = [137, 145, 137, 159, 156, 151];
        const jlhPrestasiTahunNonAkademik = [17, 15, 13, 19, 16, 21];
        
        const kegiatan = ["MBKM", "IISMA", "Kerja Praktik", "Studi Independent", "Pertukaran Pelajar"];
        const jlhMahasiswa = [137, 145, 137, 159, 156];

        const angkatanChart = new Chart("jlhMahasiswaAngkatan", {
            type: "bar",
            data: {
                labels: angkatan,
                datasets: [{
                    backgroundColor: barColors,
                    data: jlhAngkatan
                }]
            },
            options: {
                legend: {display: false},
                title: {
                    display: true,
                    text: "Jumlah Mahasiswa Aktif/Tahun"
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        const prodiChart = new Chart("jlhMahasiswaProdi", {
            type: "bar",
            data: {
                labels: prodi,
                datasets: [{
                    backgroundColor: barColors,
                    data: jlhProdi
                }]
            },
            options: {
                legend: {display: false},
                title: {
                    display: true,
                    text: "Jumlah Mahasiswa Aktif/Program Studi"
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        const prestasiSemesterChart = new Chart("prestasiSemester", {
            type: "bar",
            data: {
                labels: semester,
                datasets: [
                    {
                        label: 'Akademik',
                        backgroundColor: 'royalblue',
                        data: jlhPrestasiSemesterAkademik
                    },
                    {
                        label: 'NonAkademik',
                        backgroundColor: 'darkgray',
                        data: jlhPrestasiSemesterNonAkademik
                    }
                ]
            },
            options: {
                title: {
                    display: true,
                    text: 'Jumlah Prestasi/Semester'
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        const prestasiTahunChart = new Chart("prestasiTahun", {
            type: "bar",
            data: {
                labels: angkatan,
                datasets: [
                    {
                        label: 'Akademik',
                        backgroundColor: 'royalblue',
                        data: jlhPrestasiTahunAkademik 
                    },
                    {
                        label: 'NonAkademik',
                        backgroundColor: 'darkgray',
                        data: jlhPrestasiTahunNonAkademik 
                    }
                ]
            },
            options: {
                title: {
                    display: true,
                    text: 'Jumlah Prestasi/Tahun'
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        const jlhMahasiswaKegiatanChart = new Chart("jlhMahasiswaKegiatanChart", {
            type: "bar",
            data: {
                labels: kegiatan,
                datasets: [{
                    backgroundColor: barColors,
                    data: jlhMahasiswa
                }]
            },
            options: {
                legend: {display: false},
                title: {
                    display: true,
                    text: "Jumlah Mahasiswa yang Mengikuti Kegiatan di Luar Kampus"
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true 
                        }
                    }]
                }
            }
        });

        // Menyembunyikan dan menampilkan canvas berdasarkan radio button dengan name jumlah
        document.querySelectorAll('input[name="jumlah"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                if (radio.value === 'angkatan') {
                    document.getElementById('jlhMahasiswaAngkatan').style.display = 'block';
                    document.getElementById('jlhMahasiswaProdi').style.display = 'none';
                } else {
                    document.getElementById('jlhMahasiswaAngkatan').style.display = 'none';
                    document.getElementById('jlhMahasiswaProdi').style.display = 'block';
                }
            });
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
        document.getElementById('jlhMahasiswaProdi').style.display = 'none';
        document.getElementById('prestasiSemester').style.display = 'none';
    </script>

</body>

</html>