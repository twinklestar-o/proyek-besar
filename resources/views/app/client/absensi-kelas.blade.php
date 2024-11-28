@extends('layouts.auth') 

@section('content')
  <style>
    .dropbtn {
        background-color: #BFBFBF;
        color: black;
        font-size: 16px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        width: 138px;
        height: 47px;
    }

    .dropbtn:hover, .dropbtn:focus {
        background-color: #D9D9D9;
        color: #0078C4;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown1 {
        border-right: 2px solid black;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f1f1f1;
        min-width: 160px;
        max-height: 200px; /* Menentukan tinggi maksimum untuk dropdown */
        overflow-y: auto; /* Menambahkan scroll jika konten melebihi tinggi maksimum */
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
        font-size: 14px;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    form > label {
        font-size: 18px;
    }

    .dropdown a:hover {
        background-color: #ddd;
    }

    .show {
        display: block;
    }

    .box {
        border-bottom: 2px solid #D9D9D9;
    }
  </style>

  <!-- Main Content -->
  <main class="pl-56 mr-5 pt-10">
      <div>
          <h1 class="text-2xl font-bold mb-1">Pelanggaran</h1>
          <p class="mb-1">
              Lorem ipsum dolor sit amet consectetur adipisicing elit. Quo eveniet, tenetur saepe ullam doloremque tempora, incidunt natus eligendi id quaerat eum cumque nisi temporibus molestias quibusdam officiis! Eius, odit dicta!
          </p>
      </div>

      <div class="mt-4 flex flex-row">
          <div class="dropdown dropdown1 pr-4">
              <button id="mainDropdownButton" onclick="toggleMainDropdown()" class="dropbtn flex items-center flex justify-center p-2">
                  Mata Kuliah
                  <svg class="h-4 w-4 text-gray-900 ml-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" />
                      <polyline points="6 9 12 15 18 9" />
                  </svg>
              </button>
              <div id="mainDropdown" class="dropdown-content">
                  <a href="#" onclick="selectMainCourse('Dasar Pemograman')">Dasar Pemograman</a>
                  <a href="#" onclick="selectMainCourse('Matematika Dasar')">Matematika Dasar</a>
                  <a href="#" onclick="selectMainCourse('Matematika Teknik')">Matematika Teknik</a>
                  <a href="#" onclick="selectMainCourse('Pemrograman Berorientasi Objek')">Pemrograman Berorientasi Objek</a>
                  <a href="#" onclick="selectMainCourse('Sistem Digital')">Sistem Digital</a>
                  <a href="#" onclick="selectMainCourse('Algoritma dan Struktur Data')">Algoritma dan Struktur Data</a>
                  <a href="#" onclick="selectMainCourse('Manajemen Proyek Teknologi Informasi')">Manajemen Proyek Teknologi Informasi</a>
                  <a href="#" onclick="selectMainCourse('Probabilitas dan Statistika')">Probabilitas dan Statistika</a>
                  <a href="#" onclick="selectMainCourse('Rekayasa Perangkat Lunak')">Rekayasa Perangkat Lunak</a>
                  <a href="#" onclick="selectMain Course('Basis Data')">Basis Data</a>
                  <a href="#" onclick="selectMainCourse('Pemograman Visual')">Pemograman Visual</a>
                  <a href="#" onclick="selectMainCourse('Inovasi Digital')">Inovasi Digital</a>
              </div>
          </div>

          <div class="grid content-center ml-3 ">
              <form class="h-fit flex flex-row">
                  <input type="radio" id="hari" name="waktu" value="hari" checked>
                  <label class="mr-5" for="hari">Hari</label>
                  <input type="radio" id="minggu" name="waktu" value="minggu">
                  <label class="mr-5" for="minggu">Minggu </label>
                  <input type="radio" id="bulan" name="waktu" value="bulan">
                  <label for="bulan">Bulan </label>
              </form>
          </div>
      </div>

      <div class="box mt-10 flex flex-col items-center justify-around md:flex-row">
          <div class="mr-2" id="absensi-info">
              <p>
                  Hadir : 43 <br>
                  Tidak Hadir : 45 <br>
              </p>
          </div>

          <div class="self-start">
              <p><b>Hari ini: </b></p>
              <div class="w-6/12 sm:w-full d-flex content-center">
                  <canvas id="jlhAbsensiChart"></canvas>
              </div>
          </div>
      </div>

      <div class="mt-2">
        <p>History : </p>
        <div class="dropdown pr-4 mt-2">
            <button id="historyDropdownButton" onclick="toggleHistoryDropdown()" class="dropbtn flex items-center flex justify-center p-2">
                Mata Kuliah
                <svg class="h-4 w-4 text-gray-900 ml-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" />
                    <polyline points="6 9 12 15 18 9" />
                </svg>
            </button>
            <div id="historyDropdown" class="dropdown-content">
                <a href="#" onclick="selectHistoryCourse('Dasar Pemograman')">Dasar Pemograman</a>
                <a href="#" onclick="selectHistoryCourse('Matematika Dasar')">Matematika Dasar</a>
                <a href="#" onclick="selectHistoryCourse('Matematika Teknik')">Matematika Teknik</a>
                <a href="#" onclick="selectHistoryCourse('Pemrograman Berorientasi Objek')">Pemrograman Berorientasi Objek</a>
                <a href="#" onclick="selectHistoryCourse('Sistem Digital')">Sistem Digital</a>
                <a href="#" onclick="selectHistoryCourse('Algoritma dan Struktur Data')">Algoritma dan Struktur Data</a>
                <a href="#" onclick="selectHistoryCourse('Manajemen Proyek Teknologi Informasi')">Manajemen Proyek Teknologi Informasi</a>
                <a href="#" onclick="selectHistoryCourse('Probabilitas dan Statistika')">Probabilitas dan Statistika</a>
                <a href="#" onclick="selectHistoryCourse('Rekayasa Perangkat Lunak')">Rekayasa Perangkat Lunak</a>
                <a href="#" onclick="selectHistoryCourse('Basis Data')">Basis Data</a>
                <a href="#" onclick="selectHistoryCourse('Pemograman Visual')">Pemograman Visual</a>
                <a href="#" onclick="selectHistoryCourse('Inovasi Digital')">Inovasi Digital</a>
            </div>
        </div>
        <div class="box mt-10 flex flex-col items-center justify-around md:flex-row">
            <p><b>History Chart: </b></p>
            <div class="w-6/12 sm:w-full d-flex content-center">
                <canvas id="historyChart"></canvas>
            </div>
        </div>
      </div>
  </main>
  <script>
    const barColors = ["#0078C4", "#0078C4"];
    const absensi = ["Hadir", "Tidak Hadir"];
    const tahun = ["Jan", "Feb", "Mar", "Apr", "Mei", " Jun"];
    
    // Sample data for absensi
    const jlhAbsensiHari = [43, 2]; 
    const jlhAbsensiMinggu = [88, 2]; 
    const jlhAbsensiBulan = [162, 26];
    const jlhAbsensiHistoryHadir = [162, 123, 123, 211, 123, 112, 112];
    const jlhAbsensiHistoryTidakHadir = [22, 13, 13, 21, 21, 12, 12];

    let jlhAbsensiChart;
    let historyChart;

    function createChart(data, title, canvasId) {
        return new Chart(canvasId, {
            type: "pie",
            data: {
                labels: absensi,
                datasets: [{
                    backgroundColor: barColors,
                    data: data
                }]
            },
            options: {
                legend: { display: false },
                title: {
                    display: true,
                    text: title
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
    }

    // Initial chart setup for daily data
    jlhAbsensiChart = createChart(jlhAbsensiHari, "Jumlah Mahasiswa yang Hadir/Hari", "jlhAbsensiChart");

    // Setup for history chart
    historyChart = new Chart("historyChart", {
        type: "bar",
        data: {
            labels: tahun,
            datasets: [
                {
                    label: 'Hadir',
                    backgroundColor: 'royalblue',
                    data: jlhAbsensiHistoryHadir
                },
                {
                    label: 'Tidak Hadir',
                    backgroundColor: 'darkgray',
                    data: jlhAbsensiHistoryTidakHadir
                }
            ]
        },
        options: {
            legend: { display: true },
            title: {
                display: true,
                text: "Jumlah Mahasiswa yang Hadir/Tahun"
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

    document.querySelectorAll('input[name="waktu"]').forEach((elem) => {
        elem.addEventListener("change", function (event) {
            let data, title;
            if (event.target.value === "hari") {
                data = jlhAbsensiHari;
                title = "Jumlah Mahasiswa yang Hadir/Hari";
            } else if (event.target.value === "minggu") {
                data = jlhAbsensiMinggu;
                title = "Jumlah Mahasiswa yang Hadir/Minggu";
            } else if (event.target.value === "bulan") {
                data = jlhAbsensiBulan;
                title = "Jumlah Mahasiswa yang Hadir/Bulan";
            }
            jlhAbsensiChart.data.datasets[0].data = data;
            jlhAbsensiChart.options.title.text = title;
            document.getElementById("absensi-info").innerHTML = `
                <p>
                    Hadir : ${data[0]} <br>
                    Tidak Hadir : ${data[1]} <br>
                </p>
            `;
            jlhAbsensiChart.update();
        });
    });

    function toggleMainDropdown() {
        document.getElementById("mainDropdown").classList.toggle("show");
    }

    function selectMainCourse(course) {
        document.getElementById("mainDropdownButton").innerText = course;
        toggleMainDropdown(); // Close the dropdown after selection
    }

    function toggleHistoryDropdown() {
        document.getElementById("historyDropdown").classList.toggle("show");
    }

    function selectHistoryCourse(course) {
        document.getElementById("historyDropdownButton").innerText = course;
        toggleHistoryDropdown(); // Close the dropdown after selection
    }

    // Close the dropdown if the user clicks outside of it
    window.onclick = function (event) {
        if (!event.target.matches('.dropbtn')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
  </script>
@endsection