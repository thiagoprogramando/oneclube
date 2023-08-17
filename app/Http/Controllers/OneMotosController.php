<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OneMotosController extends Controller
{
    public function index($id) {
        return view('franquias.onemotos', ['id' => $id]);
    }

    public function associado($id) {
        return view('associado.onemotos', ['id' => $id]);
    }
}
