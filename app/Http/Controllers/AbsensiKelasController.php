<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class AbsensiKelasController extends Controller
{
  public function index()
  {
    return view('app.client.absensi-kelas');
  }
}
