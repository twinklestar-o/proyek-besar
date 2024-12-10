<aside class="bg-gray-100 w-64 border-r border-gray-200 shadow-lg hidden md:block">
  <div class="sticky top-16 h-[calc(100vh)] overflow-y-hidden">
    <nav class="p-4">
      <ul class="space-y-4">
        <li>
          <a href="/home"
            class="flex items-center py-2 px-4 rounded transition 
             {{ Request::is('home') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-600 hover:text-white' }}">
            <i class="bi bi-house-door-fill mr-2"></i>
            Home
          </a>
        </li>
        <li>
          <a href="/log" class="flex items-center py-2 px-4 rounded transition 
             {{ Request::is('log') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-600 hover:text-white' }}">
            <i class="bi bi-box-arrow-in-right mr-2"></i>
            Log Keluar/Masuk
          </a>
        </li>
        <li>
          <a href="/absensi-asrama"
            class="flex items-center py-2 px-4 rounded transition 
             {{ Request::is('absensi-asrama') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-600 hover:text-white' }}">
            <i class="bi bi-building mr-2"></i>
            Absensi Asrama
          </a>
        </li>
        <li>
          <a href="/absensi-kelas"
            class="flex items-center py-2 px-4 rounded transition 
             {{ Request::is('absensi-kelas') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-600 hover:text-white' }}">
            <i class="bi bi-journal-text mr-2"></i>
            Absensi Kelas
          </a>
        </li>
        <li>
          <a href="/pelanggaran"
            class="flex items-center py-2 px-4 rounded transition 
             {{ Request::is('pelanggaran') ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-blue-600 hover:text-white' }}">
            <i class="bi bi-exclamation-triangle-fill mr-2"></i>
            Pelanggaran
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>