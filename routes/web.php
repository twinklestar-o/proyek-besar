<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app/client/home');
});


Route::get('/auth/login', function () {
    return view('app/admin/login');
});
