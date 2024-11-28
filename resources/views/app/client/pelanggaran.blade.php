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
        border-right: 2px solid black;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f1f1f1;
        min-width: 160px;
        overflow: auto;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
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
          <div class="dropdown pr-4">
              <button onclick="myFunction()" class="dropbtn flex items-center flex justify-center">
                  Asrama
                  <svg class="h-4 w-4 text-gray-900 ml-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                      <path stroke="none" d="M0 0h24v24H0z" />
                      <polyline points="6 9 12 15 18 9" />
                  </svg>
              </button>
              <div id="myDropdown" class="dropdown-content">
                  <a href="">Asrama Silo</a>
                  <a href="">Asrama Kapernaum</a>
                  <a href="">Asrama Pniel</a>
                  <a href="">Asrama Jati</a>
                  <a href="">Asrama Rusun 1</a>
                  <a href="">Asrama Rusun 2</a>
                  <a href="">Asrama Rusun 3</a>
                  <a href="">Asrama Rusun 4</a>
              </div>
          </div>

          <div class="grid content-center ml-3 ">
              <form class="h-fit flex flex-row">
                  <input type="radio" id="asrama" name="lokasi" value="asrama" checked>
                  <label class="mr-5" for="asrama">Asrama</label>
                  <input type="radio" id="kampus" name="lokasi" value="kampus">
                  <label for="kampus">Kampus </label>
              </form>
          </div>
      </div>

      <div class="box mt-10 flex flex-col items-center justify-around ```html
      md:flex-row">
          <div class="mr-2" id="pelanggaran-info">
              <p>
                  Ringan : 43 <br>
                  Sedang : 45 <br>
                  Berat : 47 <br>
              </p>
          </div>

          <div class="self-start">
              <p><b>Hari ini: </b></p>
              <div class="w-6/12 sm:w-full d-flex content-center">
                  <canvas id="jumlahPelanggaran"></canvas>
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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
      const barColors = ["royalblue", "darkgray", "red"];
      const tingkatan = ["Ringan", "Sedang", "Berat"];
      const tahun = ["2019", "2020", "2021", "2022", "2023", "2024"];

      const jlhPelanggaranAsrama = [43, 45, 47];
      const jlhPelanggaranKampus = [3, 4, 1];
      const jlhHistoryPelanggaranRingan = [16, 13, 23, 21, 23, 11, 11];
      const jlhHistoryPelanggaranSedang = [22, 13, 13, 21, 21, 12, 12];
      const jlhHistoryPelanggaranBerat = [2, 3, 3, 2, 4, 3, 5];

      let historyChart;
      historyChart = new Chart("historyChart", {
        type: "bar",
        data: {
            labels: tahun,
            datasets: [
                {
                    label: 'Ringan',
                    backgroundColor: 'royalblue',
                    data: jlhHistoryPelanggaranRingan
                },
                {
                    label: 'Sedang',
                    backgroundColor: 'darkgray',
                    data: jlhHistoryPelanggaranSedang
                },
                {
                    label: 'Berat',
                    backgroundColor: 'red',
                    data: jlhHistoryPelanggaranBerat
                }
            ]
        },
        options: {
            legend: { display: true },
            title: {
                display: true,
                text: "Jumlah Pelanggaran per Tahun"
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

      const pelanggaranChart = new Chart("jumlahPelanggaran", {
          type: "bar",
          data: {
              labels: tingkatan,
              datasets: [{
                  backgroundColor: barColors,
                  data: jlhPelanggaranAsrama
              }]
          },
          options: {
              legend: { display: false },
              title: {
                  display: true,
                  text: "Jumlah Mahasiswa yang Melanggar Peraturan"
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

      document.querySelectorAll('input[name="lokasi"]').forEach((elem) => {
          elem.addEventListener("change", function (event) {
              if (event.target.value === "kampus") {
                  pelanggaranChart.data.datasets[0].data = jlhPelanggaranKampus;
                  document.getElementById("pelanggaran-info").innerHTML = `
                      <p>
                          Ringan : 3 <br>
                          Sedang : 4 <br>
                          Berat : 1 <br>
                      </p>
                  `;
              } else {
                  pelanggaranChart.data.datasets[0].data = jlhPelanggaranAsrama;
                  document.getElementById("pelanggaran-info").innerHTML = `
                      <p>
                          Ringan : 43 <br>
                          Sedang : 45 <br>
                          Berat : 47 <br>
                      </p>
                  `;
              }
              pelanggaranChart.update();
          });
      });

      function myFunction() {
          document.getElementById("myDropdown").classList.toggle("show");
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