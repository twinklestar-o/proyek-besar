<!-- resources/views/layout/sidebar.blade.php -->
<aside class="bg-[#EEEEEE] w-52 h-full pt-16 fixed top-0 left-0 z-0">
  <ul class="space-y-2 px-3 pt-3">
    <li>
      <a href="#" class="block text-black-700 p-2 pl-6 rounded active:bg-[#D9D9D9] active:text-[#0078C4] home-link">
        Home
      </a>
    </li>

    <li>
      <a href="#" class="block text-black-700 hover:bg-[#D9D9D9] hover:text-[#0078C4] p-2 pl-6 rounded" id="dashboard">
        Dashboard
      </a>
    </li>

    <li>
      <a href="#" class="block text-black-700 hover:bg-[#D9D9D9] hover:text-[#0078C4] p-2 pl-6 rounded" id="log">
        Log Keluar/Masuk
      </a>
    </li>

    <li>
      <a href="#" class="block text-black-700 hover:bg-[#D9D9D9] hover:text-[#0078C4] p-2 pl-6 rounded" id="absensiKampus">
        Absensi Kampus
      </a>
    </li>

    <li>
      <a href="#" class="block text-black-700 hover:bg-[#D9D9D9] hover:text-[#0078C4] p-2 pl-6 rounded" id="absensiKelas">
        Absensi Kelas
      </a>
    </li>

    <li>
      <a href="#" class="block text-black-700 hover:bg-[#D9D9D9] hover:text-[#0078C4] p-2 pl-6 rounded" id="absensiKampus2">
        Absensi Kampus
      </a>
    </li>
  </ul>
</aside>

<script>
  // Menangani kelas aktif pada sidebar
  const sidebarLinks = document.querySelectorAll('aside a');

  // Fungsi untuk mengatur kelas aktif
  function setActiveLink(link) {
    sidebarLinks.forEach(l => {
      l.classList.remove('bg-[#D9D9D9]', 'text-[#0078C4]');
      l.classList.add('text-black-700'); // Kembalikan warna teks default
    });
    link.classList.add('bg-[#D9D9D9]', 'text-[#0078C4]');
  }

  // Set default active link to Home
  const defaultActiveLink = document.querySelector('.home-link');
  setActiveLink(defaultActiveLink);

  // Tambahkan event listener untuk setiap link
  sidebarLinks.forEach(link => {
    link.addEventListener('click', function(event) {
      event.preventDefault(); // Mencegah navigasi saat mengklik
      setActiveLink(this);
    });
  });
</script>