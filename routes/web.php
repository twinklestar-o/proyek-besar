<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;

// Public routes
Route::get('/', function () {
    return view('app/client/home');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Admin routes (protected by 'auth' middleware)
Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('app/admin/dashboard');
    });

    Route::get('/admin/absensi-kampus', function () {
        return view('app/admin/absensi_kampus');
    });

    Route::get('/admin/absensi-kelas', function () {
        return view('app/admin/absensi_kelas');
    });

    Route::get('/admin/log', function () {
        return view('app/admin/log');
    });

    Route::get('/admin/pelanggaran', function () {
        return view('app/admin/pelanggaran');
    });

    Route::get('/home', function () {
        return view('app/admin/dashboard');
    });
});


