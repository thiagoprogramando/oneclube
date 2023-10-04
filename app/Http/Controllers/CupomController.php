<?php

namespace App\Http\Controllers;

use App\Models\Cupom;
use Illuminate\Http\Request;

class CupomController extends Controller
{
    public function cupom() {
        $cupons = Cupom::all();

        return view('dashboard.relatorio.cupom', [ 'cupons' => $cupons ]);
    }

    public function cadastraCupom(Request $request) {
        $cupom = new Cupom();
        $cupom->titulo = $request->titulo;
        $cupom->codigo = $request->codigo;
        $cupom->save();

        return redirect()->back()->with('msg', 'Cupom cadastrado com sucesso!');
    }

    public function excluiCupom(Request $request) {
        $cupom = Cupom::where('id', $request->id)->first();
        $cupom->delete();

        return redirect()->back()->with('msg', 'Cupom excluído com sucesso!');
    }
}
