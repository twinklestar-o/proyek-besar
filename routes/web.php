<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/home');
});

Route::get('/home', function () {
    return view('home');
});

Route::get('/dashboard', function () {
    return view('dashboard'); // Replace 'home' with your dashboard view file if different
});

Route::get('/auth/login', function () {
    return view('app/admin/login');
});
