<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NavController;

//Static pages
Route::get('/pelanggaran', [NavController::class, 'pelanggaran'])->name('pelanggaran');
Route::get('/', [NavController::class, ' home'])->name('home');

Route::get('/home', function () {
    return view('app/client/home');
});

Route::get('/dashboard', function () {
    return view('dashboard'); // Replace 'home' with your dashboard view file if different
});

Route::get('/auth/login', function () {
    return view('app/admin/login');
});
