<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LimpaNomeController extends Controller
{
    public function index($id) {
        return view('franquias.limpanome', ['id' => $id]);
    }
}
