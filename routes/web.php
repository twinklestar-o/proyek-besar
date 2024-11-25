<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app/client/home');
});

Route::get('/auth/login', function () {
    return view('app/admin/login');
});

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

Route::get('/admin/login', function () {
    return view('app/admin/login');
});

Route::get('/admin/pelanggaran', function () {
    return view('app/admin/pelanggaran');
});
