@php
  $currentRoute = request()->route()->getName();
@endphp

<aside class="bg-gray-200 w-52 h-screen fixed top-0 left-0 z-10">
  <ul class="space-y-2 px-3 pt-3">
    <li><a href="{{ route('home') }}" class="block text-black p-2 hover:bg-gray-300">Home</a></li>
    <li><a href="{{ route('log.mahasiswa') }}" class="block text-black p-2 hover:bg-gray-300">Log Keluar/Masuk</a></li>
    <li><a href="{{ route('absensi.kelas') }}" class="block text-black p-2 hover:bg-gray-300">Absensi Kelas</a></li>
    <li><a href="{{ route('absensi.asrama') }}" class="block text-black p-2 hover:bg-gray-300">Absensi Asrama</a></li>
    <li><a href="{{ route('admin.pelanggaran') }}" class="block text-black p-2 hover:bg-gray-300">Pelanggaran</a></li>
  </ul>
</aside>


<style>
  aside a {
    text-decoration: none;
  }

  /* Menambahkan gaya hover */
  aside a:hover {
    text-decoration: none;
  }

  /* Gaya untuk tautan aktif */
  aside a.active {
    background-color: #D9D9D9;
    color: #0078C4;
    padding: 0.5rem 1.5rem;
    border-radius: 0.375rem;
  }

  .sidebar a {
    font-size: 1rem;
    margin: 0;
    padding: 0.5rem 1.5rem;
  }
</style>

<script>
  const sidebarLinks = document.querySelectorAll('aside a');

  function setActiveLink(link) {
    sidebarLinks.forEach(l => {
      l.classList.remove('active'); // Hapus kelas aktif dari semua tautan
    });
    link.classList.add('active'); // Tambahkan kelas aktif ke tautan yang diklik
  }

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

  const currentActiveLink = Array.from(sidebarLinks).find(link => link.classList.contains('active'));
  if (currentActiveLink) {
    setActiveLink(currentActiveLink);
  }
</script>