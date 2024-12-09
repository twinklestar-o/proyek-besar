@extends('layouts.auth') 

@section('content')
  <style>
    form > label {
        font-size: 18px;
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
          <h1 class="text-2xl font-bold mb-1">Log Keluar/Masuk</h1>
          <p class="mb-1">
              Lorem ipsum dolor sit amet consectetur adipisicing elit. Quo eveniet, tenetur saepe ullam doloremque tempora, incidunt natus eligendi id quaerat eum cumque nisi temporibus molestias quibusdam officiis! Eius, odit dicta!
          </p>
      </div>

      <div class="mt-4 flex flex-row">
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
    const jlhLogHari = [1173, 2]; 
    const jlhLogMinggu = [3617, 2]; 
    const jlhLogBulan = [1929, 26];
    const jlhHistoryBarcode = [1621, 1929, 1229, 1939, 1922, 2029, 1629];
    const jlhHistoryTidakBarcode = [22, 13, 13, 21, 21, 12, 12];

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
    jlhAbsensiChart = createChart(jlhLogHari, "Jumlah Mahasiswa yang Hadir/Hari", "jlhAbsensiChart");

    // Setup for history chart
    historyChart = new Chart("historyChart", {
        type: "bar",
        data: {
            labels: tahun,
            datasets: [
                {
                    label: 'Barcode',
                    backgroundColor: 'royalblue',
                    data: jlhHistoryBarcode
                },
                {
                    label: 'Tidak Barcode',
                    backgroundColor: 'darkgray',
                    data: jlhHistoryTidakBarcode
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
                data = jlhLogMinggu;
                title = "Jumlah Mahasiswa yang Hadir/Minggu";
            } else if (event.target.value === "bulan") {
                data = jlhLogBulan;
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