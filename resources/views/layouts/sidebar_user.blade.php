@php
  $currentRoute = request()->route()->getName();
@endphp

<aside class="bg-[#EEEEEE] w-52 h-full pt-16 fixed top-0 left-0 z-0 sidebar">
  <ul class="space-y-2 px-3 pt-3">
    <li>
      <a href="{{ route('client.home.index') }}"
        class="block text-black-700 p-2 pl-6 rounded {{ $currentRoute == 'client.home.index' ? 'active' : '' }} home-link">
        Home
      </a>
    </li>

    <li>
      <a href="{{ route('client.log.index') }}"
        class="block text-black-700 p-2 pl-6 rounded {{ $currentRoute == 'client.log.index' ? 'active' : '' }}"
        id="log">
        Log Keluar/Masuk
      </a>
    </li>

    <li>
      <a href="{{ route('client.absensi-kelas.index') }}"
        class="block text-black-700 p-2 pl-6 rounded {{ $currentRoute == 'client.absensi-kelas.index' ? 'active' : '' }}"
        id="absensiKelas">
        Absensi Kelas
      </a>
    </li>

    <li>
      <a href="#" class="block text-black-700 p-2 pl-6 rounded {{ Request::is('absensiKampus') ? 'active' : '' }}"
        id="absensiKampus">
        Absensi Kampus
      </a>
    </li>

    <li>
      <a href="{{ route('client.pelanggaran.index') }}"
        class="block text-black-700 p-2 pl-6 rounded {{ $currentRoute == 'client.pelanggaran.index' ? 'active' : '' }}"
        id="pelanggaran">
        Pelanggaran
      </a>
    </li>
  </ul>
</aside>

<style>
  aside a {
    text-decoration: none;
  }

  /* Menambahkan gaya hover */
  aside a:hover {
    text-decoration: none;
    /* Pastikan tidak ada garis bawah saat hover */
  }

  /* Gaya untuk tautan aktif */
  aside a.active {
    background-color: #D9D9D9;
    /* Warna latar belakang aktif */
    color: #0078C4;
    /* Warna teks aktif */
    padding: 0.5rem 1.5rem;
    /* Padding yang konsisten */
    border-radius: 0.375rem;
    /* Radius sudut */
  }

  /* Pastikan gaya sidebar tidak terpengaruh oleh gaya lain */
  .sidebar a {
    font-size: 1rem;
    margin: 0;
    padding: 0.5rem 1.5rem;
  }
</style>

<script>
  const sidebarLinks = document.querySelectorAll('aside a');

  // Fungsi untuk mengatur kelas aktif
  function setActiveLink(link) {
    sidebarLinks.forEach(l => {
      l.classList.remove('active'); // Hapus kelas aktif dari semua tautan
    });
    link.classList.add('active'); // Tambahkan kelas aktif ke tautan yang diklik
  }

  // Set default active link
  sidebarLinks.forEach(link => {
    link.addEventListener('click', function (event) {
      const href = this.getAttribute('href');
      if (href === '#' || href === '') {
        event.preventDefault(); // Mencegah navigasi hanya jika href kosong atau "#"
      } else {
        window.location.href = href; // Lakukan navigasi ke href
      }
      setActiveLink(this);
    });
  });

  // Set active link based on current route
  const currentActiveLink = Array.from(sidebarLinks).find(link => link.classList.contains('active'));
  if (currentActiveLink) {
    setActiveLink(currentActiveLink);
  }
</script>