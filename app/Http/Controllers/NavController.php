<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NavController extends Controller
{
    public function home() {
        return view('app.client.home'); 
    }

    public function pelanggaran() {
        return view('app.client.pelanggaran'); 
    }
}
