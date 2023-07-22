<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OneServicosController extends Controller
{
    public function index($id) {
        return view('franquias.oneservicos', ['id' => $id]);
    }
}
