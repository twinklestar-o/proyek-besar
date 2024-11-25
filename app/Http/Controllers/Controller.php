<?php

namespace App\Http\Controllers;

abstract class Controller {
    public function pelanggaran() {
        return view('app.client.pelanggaran'); 
    }
}
