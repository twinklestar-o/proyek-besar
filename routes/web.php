<?php

use App\Http\Controllers\AbsensiKelasController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PelanggaranController;

//Static pages
Route::get('/', fn() => view('app/client/home'))->name('home');


Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/auth/login', function () {
    return view('app/admin/login');
});

Route::get('/client/log', [LogController::class, 'index'])->name('client.log.index');
Route::get('/client/absensi-kelas', [AbsensiKelasController::class, 'index'])->name('client.absensi-kelas.index');
Route::get('/client/home', [HomeController::class, 'index'])->name('client.home.index');
Route::get('/client/pelanggaran', [PelanggaranController::class, 'index'])->name('client.pelanggaran.index');
