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
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('app/admin/dashboard');
        })->name('admin.dashboard');

        Route::get('/absensi-kampus', function () {
            return view('app/admin/absensi_kampus');
        })->name('admin.absensi_kampus');

        Route::get('/absensi-kelas', function () {
            return view('app/admin/absensi_kelas');
        })->name('admin.absensi_kelas');

        Route::get('/log', function () {
            return view('app/admin/log');
        })->name('admin.log');

        Route::get('/pelanggaran', function () {
            return view('app/admin/pelanggaran');
        })->name('admin.pelanggaran');
    });
});