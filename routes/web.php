<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AbsensiKelasController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\AbsensiAsramaController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\ContentController;

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Redirect root to home
Route::get('/', function () {
    return redirect('/home');
});

// Route for all users
Route::get('/home', [HomeController::class, 'index'])->name('home.public');
Route::get('/absensi-kelas', [AbsensiKelasController::class, 'showAbsensiKelas'])->name('absensi.kelas.public');
Route::get('/log', [LogController::class, 'getLogMahasiswa'])->name('log.mahasiswa.public');
Route::get('/absensi-asrama', [AbsensiAsramaController::class, 'getAbsensiAsrama'])->name('absensi.asrama.public');
Route::get('/pelanggaran', [PelanggaranController::class, 'getPelanggaranByAsrama'])->name('pelanggaran.public');

// AJAX Routes for Absensi Kelas
Route::get('/absensi-kelas/matkul', [AbsensiKelasController::class, 'getMatkulAjax'])->name('absensi.kelas.matkul');
Route::get('/absensi-kelas/absensi', [AbsensiKelasController::class, 'fetchTotalKehadiranAjax'])->name('absensi.kelas.absensi');

// Routes for authenticated users
Route::middleware('auth')->group(function () {
    // Home route
    Route::get('/admin/home', [HomeController::class, 'index'])->name('home.auth');
    // Absensi Kelas
    Route::get('/admin/absensi-kelas', [AbsensiKelasController::class, 'showAbsensiKelas'])->name('absensi.kelas.auth');
    // Log Mahasiswa
    Route::get('/admin/log', [LogController::class, 'getLogMahasiswa'])->name('log.mahasiswa.auth');
    // Absensi Asrama
    Route::get('/admin/absensi-asrama', [AbsensiAsramaController::class, 'getAbsensiAsrama'])->name('absensi.asrama.auth');
    // Pelanggaran
    Route::get('/admin/pelanggaran', [PelanggaranController::class, 'getPelanggaranByAsrama'])->name('pelanggaran.auth');
});
