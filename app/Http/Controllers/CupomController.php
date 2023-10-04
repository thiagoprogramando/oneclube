<?php

namespace App\Http\Controllers;

use App\Models\Cupom;
use Illuminate\Http\Request;

class CupomController extends Controller
{
    public function cupom ()
    {

            $dadosFiltrados = Cupom::all();

        return view('dashboard.cupom', compact('dadosFiltrados'));
    }

    public function cupomaction(Request $request)
    {
        // Verificar se a solicitação é uma solicitação POST
        if (!$request->isMethod('post')) {
            return redirect()->route('cupom');
        }
    
        // Recuperar os dados do formulário
        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');
        $cupom = $request->input('cupom');
    
        // Filtrar os dados com base nos valores enviados
        $dadosFiltrados = Cupom::where(function ($query) use ($dataInicio, $dataFim, $cupom) {
            if ($dataInicio) {
                $query->where('created_at', '>=', $dataInicio);
            }
            if ($dataFim) {
                $query->where('created_at', '<=', $dataFim);
            }
            if ($cupom) {
                $query->where('codigo', '=', $cupom);
            }
        })->get();
    
        // Retorne a view com os dados filtrados
        return view('dashboard.filtro', compact('dadosFiltrados'));
    }
    
    
}
