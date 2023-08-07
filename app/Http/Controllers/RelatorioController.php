<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
            'vendas' => Vendas::where('status_pay', 'PAYMENT_CONFIRMED')->take(50)->get(),
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

        $vendas = Vendas::where('status_pay', 'PAYMENT_CONFIRMED');

        if ($produto != 'ALL') {
            $vendas = $vendas->where('id_produto', $produto);
        }

        if ($usuario != 'ALL') {
            $vendas = $vendas->where('id_vendedor', $usuario);
        }

        $dataInicio = $request->input('data_inicio');
        $dataFim = $request->input('data_fim');

        if ($dataInicio && $dataFim) {
            $dataInicio = Carbon::parse($dataInicio);
            $dataFim = Carbon::parse($dataFim);
    
            $vendas->whereBetween('updated_at', [$dataInicio, $dataFim]);
        }

        $vendas = $vendas->get();
        
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

    public function upusuarios(Request $request) {
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

        $usuario = User::where('id', $request->id_usuario)->first();
        if($usuario){
            $usuario->tipo = $request->tipo;
            $usuario->save();
            $msg = "Sucesso, Usuário atualizado!";
        } else {
            $msg = "Tivemos um pequeno problema e os dados não foram alterados!";
        }

        return view('dashboard.relatorio.usuarios', [
            'notfic' => $notfic,
            'users' => User::all(),
            'msg'   => $msg
        ]);
    }
}
