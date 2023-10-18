<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function index($id, $cupom = null) {
        return view('franquias.score', ['id' => $id, 'cupom' => $cupom]);
    }
}
