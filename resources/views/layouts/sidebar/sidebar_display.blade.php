@php
$currentRoute = request()->route()->getName();
@endphp

<aside class="bg-[#EEEEEE] w-52 h-full pt-16 fixed top-0 left-0 z-0">
  <ul class="space-y-2 px-3 pt-3">
    <li>
      <a href="{{route ('home')}}" class="block text-black-700 p-2 pl-6 rounded {{ $currentRoute == 'home' ? 'bg-[#D9D9D9] text-[#0078C4]' : '' }} home-link">
        Home
      </a>
    </li>

    <li>
      <a href="" class="block text-black-700 hover:bg-[#D9D9D9] hover:text-[#0078C4] p-2 pl-6 rounded {{ $currentRoute == '' ? 'bg-[#D9D9D9] text-[#0078C4]' : '' }}" id="dashboard">
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
      <a href="{{ route('pelanggaran') }}" class="block text-black-700 hover:bg-[#D9D9D9] hover:text-[#0078C4] p-2 pl-6 rounded {{ $currentRoute == 'pelanggaran' ? 'bg-[#D9D9D9] text-[#0078C4]' : '' }}" id="absensiKampus2">
        Pelanggaran
      </a>
    </li>
  </ul>
</aside>