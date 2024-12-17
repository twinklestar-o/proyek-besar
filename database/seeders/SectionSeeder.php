<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SectionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Cek apakah section sudah ada
    $section = DB::table('sections')->where('section', 'log_keluar_masuk_mahasiswa')->first();

    if (!$section) {
      DB::table('sections')->insert([
        'section' => 'log_keluar_masuk_mahasiswa',
        'title' => 'Log Keluar/Masuk Mahasiswa',
        'description' => 'Log Keluar/Masuk Mahasiswa mencatat semua aktivitas mahasiswa saat memasuki atau meninggalkan kampus. Informasi ini memungkinkan pemantauan kehadiran mahasiswa secara real-time, serta memberikan gambaran mengenai pola mobilitas mahasiswa di lingkungan kampus.',
        'chart_type' => 'bar', // Default chart type
        'created_at' => now(),
        'updated_at' => now(),
      ]);

      DB::table('sections')->insertOrIgnore([
        'section' => 'absensi_asrama',
        'title' => 'Absensi Asrama',
        'description' => 'Absensi Asrama mencatat kehadiran mahasiswa di asrama selama periode tertentu. Data ini penting untuk memastikan bahwa mahasiswa mematuhi aturan tinggal di asrama dan untuk menjaga keamanan. Informasi ini juga dapat digunakan untuk mengidentifikasi mahasiswa yang sering tidak hadir dan memberikan dukungan yang diperlukan.',
        'created_at' => now(),
        'updated_at' => now(),
      ]);

      DB::table('sections')->insertOrIgnore([
        'section' => 'pelanggaran_asrama',
        'title' => 'Data Pelanggaran Asrama',
        'description' => 'Data Pelanggaran Asrama mencatat semua pelanggaran yang dilakukan oleh mahasiswa selama tinggal di asrama. Ini termasuk pelanggaran terhadap aturan asrama, seperti kebisingan atau membawa tamu tanpa izin. Informasi ini penting untuk menjaga disiplin dan menciptakan lingkungan yang aman bagi semua penghuni asrama.',
        'created_at' => now(),
        'updated_at' => now(),
      ]);

      DB::table('sections')->insertOrIgnore([
        'section' => 'absensi_kelas',
        'title' => 'Absensi Kelas',
        'description' => 'Absensi Kelas mencatat kehadiran mahasiswa dalam setiap sesi perkuliahan. Data ini memberikan gambaran mengenai partisipasi mahasiswa dalam proses belajar mengajar, serta membantu dalam evaluasi keterlibatan akademik mereka.',
        'created_at' => now(),
        'updated_at' => now(),
      ]);


    }
  }
}
