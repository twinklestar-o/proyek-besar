<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class PelanggaranController extends Controller
{
  public function index()
  {
    return view('app.client.pelanggaran');
  }
}
