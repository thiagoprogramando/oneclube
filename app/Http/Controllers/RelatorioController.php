<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VendasExport;

use App\Models\Vendas;
use App\Models\User;
use App\Models\Notificacao;

class RelatorioController extends Controller
{
    public function index() {
        $users = auth()->user();

        $notfic = Notificacao::where(function ($query) use ($users) {
            if ($users->profile === 'admin') {
                $query->where(function ($query) {
                    $query->where('tipo', '!=', '')
                        ->orWhere('tipo', 0);
                });
            } else {
                $query->where(function ($query) use ($users) {
                    $query->where('tipo', 0)
                        ->orWhere('tipo', $users->id);
                });
            }
        })->get();

        return view('dashboard.relatorio.vendas', [
            'notfic' => $notfic,
            'users' => User::all(),
            'vendas' => Vendas::latest()->take(50)->get(),
        ]);
    }

    public function filtro(Request $request) {

        $users = auth()->user();

        $notfic = Notificacao::where(function ($query) use ($users) {
            if ($users->profile === 'admin') {
                $query->where(function ($query) {
                    $query->where('tipo', '!=', '')
                        ->orWhere('tipo', 0);
                });
            } else {
                $query->where(function ($query) use ($users) {
                    $query->where('tipo', 0)
                        ->orWhere('tipo', $users->id);
                });
            }
        })->get();

        $produto = $request->input('produto');
        $usuario = $request->input('usuario');

        $vendas = Vendas::query();

        if ($produto !== 'ALL') {
            $vendas->where('id_produto', $produto);
        }

        if ($usuario !== 'ALL') {
            $vendas->where('id_vendedor', $produto);
        }

        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        if ($dataInicio && $dataFim) {
            $dataInicio = Carbon::parse($dataInicio);
            $dataFim = Carbon::parse($dataFim);
    
            $vendas->whereBetween('updated_at', [$dataInicio, $dataFim]);
        }

        $excel = $request->input('excel');
        if ($excel === 'S') {
            $filename = 'relatorio_vendas.xlsx';
            $export = new VendasExport($vendas);
            
            return Excel::download($export, $filename);
        }
        
        return view('dashboard.relatorio.vendas', [
            'notfic' => $notfic,
            'users'  => $users,
            'vendas' => $vendas,
            'users'  => User::all(),
        ]);
    }

    public function usuarios() {
        $users = auth()->user();

        $notfic = Notificacao::where(function ($query) use ($users) {
            if ($users->profile === 'admin') {
                $query->where(function ($query) {
                    $query->where('tipo', '!=', '')
                        ->orWhere('tipo', 0);
                });
            } else {
                $query->where(function ($query) use ($users) {
                    $query->where('tipo', 0)
                        ->orWhere('tipo', $users->id);
                });
            }
        })->get();

        return view('dashboard.relatorio.usuarios', [
            'notfic' => $notfic,
            'users' => User::all(),
        ]);
    }
}
