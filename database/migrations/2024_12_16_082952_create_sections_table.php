<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('section')->unique(); // Identifier unik untuk setiap section
            $table->string('title');
            $table->text('description');
            $table->string('chart_type')->nullable();
            $table->timestamps();
        });

        // Insert default sections
        DB::table('sections')->insert([
            [
                'section' => 'total_mahasiswa_aktif',
                'title' => 'Total Mahasiswa Aktif',
                'description' => 'Total Mahasiswa Aktif memberikan informasi mengenai jumlah total mahasiswa aktif yang terdaftar di institusi. Dari dashboard ini, dapat dilihat grafik persebaran mahasiswa setiap angkatan dan setiap prodi. Data spesifik untuk prodi dan angkatan tertentu juga dapat diakses untuk analisis lebih mendalam.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section' => 'prestasi',
                'title' => 'Prestasi',
                'description' => 'Prestasi akademik dan non-akademik mahasiswa mencerminkan dedikasi dan usaha para mahasiswa dalam mencapai tujuan pendidikan. Melalui berbagai kompetisi dan kegiatan, mahasiswa berkesempatan untuk menunjukkan kemampuan dan keterampilan yang telah mereka pelajari.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section' => 'kegiatan_luar_kampus',
                'title' => 'Jumlah Mahasiswa yang Mengikuti Kegiatan di Luar Kampus',
                'description' => 'Program MBKM (Merdeka Belajar Kampus Merdeka) memberikan mahasiswa kesempatan untuk belajar di luar kelas, mengembangkan keterampilan praktis, dan berkontribusi pada masyarakat. Melalui program ini, mahasiswa dapat mengikuti magang, proyek sosial, dan kegiatan lainnya yang mendukung pembelajaran holistik.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tambahkan sections lainnya sesuai kebutuhan
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
}
