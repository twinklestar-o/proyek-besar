<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Home route (protected by 'auth' middleware)
Route::middleware('auth')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Admin routes
    Route::prefix('admin')->group(function () {

        Route::get('/absensi-kampus', function () {
            return view('app/absensi_kampus');
        })->name('admin.absensi_kampus');

        Route::get('/absensi-kelas', function () {
            return view('app/absensi_kelas');
        })->name('admin.absensi_kelas');

        Route::get('/log', function () {
            return view('app/log');
        })->name('admin.log');

        Route::get('/pelanggaran', function () {
            return view('app/pelanggaran');
        })->name('admin.pelanggaran');
    });
});