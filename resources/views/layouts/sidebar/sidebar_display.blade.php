@php
$currentRoute = request()->route()->getName();
@endphp

<aside class="bg-[#EEEEEE] w-52 h-full pt-16 fixed top-0 left-0 z-0 sidebar">
  <ul class="space-y-2 px-3 pt-3">
    <li>
      <a href="{{ route('client.home.index') }}" 
        class="block text-black-700 rounded {{ $currentRoute == 'client.home.index' ? 'active' : '' }} home-link">
        Home
      </a>
    </li>

    <li>
      <a href="{{ route('client.log.index') }}" 
        class="block text-black-700 rounded {{ $currentRoute == 'client.log.index' ? 'active' : '' }}" id="log">
        Log Keluar/Masuk
      </a>
    </li>

    <li>
      <a href="#" 
        class="block text-black-700 rounded {{ Request::is('absensiKampus') ? 'active' : '' }}" id="absensiKampus">
        Absensi Kampus
      </a>
    </li>

    <li>
      <a href="{{ route('client.absensi-kelas.index') }}" 
        class="block text-black-700 rounded {{ $currentRoute == 'client.absensi-kelas.index' ? 'active' : '' }}" id="absensiKelas">
        Absensi Kelas
      </a>
    </li>

    <li>
      <a href="{{ route('client.pelanggaran.index') }}" 
        class="block text-black-700 rounded {{ $currentRoute == 'client.pelanggaran.index' ? 'active' : '' }}" id="pelanggaran">
        Pelanggaran
      </a>
    </li>
  </ul>
</aside>

<style>
  aside a {
    text-decoration: none;
    display: block; /* Pastikan tautan mengambil seluruh ruang */
    padding: 0.5rem 1.5rem; /* Padding default */
  }
  
  /* Gaya untuk tautan aktif dan hover */
  aside a.active,
  aside a:hover {
    background-color: #D9D9D9; /* Warna latar belakang aktif dan hover */
    color: #0078C4; /* Warna teks aktif dan hover */
    border-radius: 0.375rem; /* Radius sudut */
    padding: 0.5rem 1.5rem; /* Padding yang lebih besar untuk efek aktif */
  }

  /* Pastikan gaya sidebar tidak terpengaruh oleh gaya lain */
  .sidebar a {
    font-size: 1rem;
    margin: 0; 
  }
</style>

<script>
  const sidebarLinks = document.querySelectorAll('aside a');

  // Set default active link
  sidebarLinks.forEach(link => {
    link.addEventListener('click', function(event) {
      const href = this.getAttribute('href');
      if (href === '#' || href === '') {
        event.preventDefault(); // Mencegah navigasi hanya jika href kosong atau "#"
      } else {
        window.location.href = href; // Lakukan navigasi ke href
      }
    });
  });
</script>