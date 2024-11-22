<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return redirect('app/client/home');
// });

//Static pages
Route::get('/', fn() => view('app/client/home'))->name('home');

Route::get('/home', function () {
    return view('app/client/home');
});

Route::get('/auth/login', function () {
    return view('app/admin/login');
});
