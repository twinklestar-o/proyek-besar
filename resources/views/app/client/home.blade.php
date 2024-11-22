<!-- resources/views/home.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Del</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
</script>
</head>

<body class="bg-gray-50">

    <!-- Include Header -->
    @include('layouts.headerbar')

    <!-- Include Sidebar -->
    @include('layouts.sidebar.sidebar_display')

    <!-- Main Content -->
    <main class="pl-64 mr-5 pt-20">
        <div class="flex flex-col md:flex-row w-full">
            <div class="w-6/12 sm:w-full">
                <h1 class="text-2xl font-bold mb-4">Jumlah Mahasiswa</h1>
                <p>
                    Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quo eveniet, tenetur saepe ullam doloremque tempora, incidunt natus eligendi id quaerat eum cumque nisi temporibus molestias quibusdam officiis! Eius, odit dicta!
                    Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quo eveniet, tenetur saepe ullam doloremque tempora, incidunt natus eligendi id quaerat eum cumque nisi temporibus molestias quibusdam officiis! Eius, odit dicta!
                </p>
                <form action="">
                  <input type="radio" id="html" name="fav_language" value="HTML">
                  <label for="html">Angkatan</label><br>
                  <input type="radio" id="css" name="fav_language" value="CSS">
                  <label for="css">Program Studi</label><br>
                </form>
            </div>
            <div class="w-6/12 sm:w-full">
                <canvas id="jlhMahasiswaAngkatan"></canvas>
                <canvas id="jlhMahasiswaProdi"></canvas>
            </div>
            <div class="column"></div>
        </div>
    </main>

    <script>
        const angkatan = ["2019", "2020", "2021", "2022", "2023", "2024"];
        const jlhAngkatan = [1537, 1545, 1567, 1559, 1566, 1560];
        const barColors = ["#0078C4","#0078C4", "#0078C4", "#0078C4", "#0078C4", "#0078C4" ];
        
        new Chart("jlhMahasiswaAngkatan", {
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
            }
          }
        });
    </script>

</body>

</html>