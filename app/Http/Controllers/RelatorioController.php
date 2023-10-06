<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cupom;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Vendas;
use App\Models\User;

class RelatorioController extends Controller
{
    public function index() {
        $users = auth()->user();

        return view('dashboard.relatorio.vendas', [
            'users' => User::all(),
            'vendas' => Vendas::take(50)->get(),
            'cupons' => Cupom::all(),
        ]);
    }

    public function filtro(Request $request) {

        $users = auth()->user();

        $produto = $request->input('produto');
        $usuario = $request->input('usuario');
        $status = $request->input('status');
        $cupom = $request->input('cupom');

        $vendas = Vendas::query();

        if ($produto != 'ALL') {
            $vendas = $vendas->where('id_produto', $produto);
        }

        if ($usuario != 'ALL') {
            $vendas = $vendas->where('id_vendedor', $usuario);
        }

        if ($cupom) {
            $vendas = $vendas->where('cupom', $cupom);
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
            'users'  => $users,
            'vendas' => $vendas,
            'users'  => User::all(),
            'cupons' => Cupom::all(),
        ]);
    }

    public function usuarios() {
        $users = auth()->user();

        return view('dashboard.relatorio.usuarios', [
            'users' => User::all(),
        ]);
    }

    public function upusuarios(Request $request) {
        $users = auth()->user();

        $usuario = User::where('id', $request->id_usuario)->first();
        if($usuario){
            $usuario->tipo = $request->tipo;
            $usuario->save();
            $msg = "Sucesso, Usuário atualizado!";
        } else {
            $msg = "Tivemos um pequeno problema e os dados não foram alterados!";
        }

        return view('dashboard.relatorio.usuarios', [
            'users' => User::all(),
            'msg'   => $msg
        ]);
    }
}
