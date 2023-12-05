<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProdutoController extends Controller {
    
    public function limpanome($id, $valor) {
        return view('franquias.limpanome', ['id' => $id, 'valor' => $valor]);
    }
}
