<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\Vendas;
use App\Models\VendaParcela;
use App\Models\User;

class RelatorioController extends Controller
{
    public function index() {
        return view('dashboard.relatorio.vendas', [
            'users' => User::all(),
            'vendas' => Vendas::take(50)->get(),
        ]);
    }

    public function filtro(Request $request) {

        $users = auth()->user();

        $produto = $request->input('produto');
        $usuario = $request->input('usuario');
        $status = $request->input('status');

        $vendas = Vendas::query();

        if ($produto != 'ALL') {
            $vendas = $vendas->where('id_produto', $produto);
        }

        if ($usuario != 'ALL') {
            $vendas = $vendas->where('id_vendedor', $usuario);
        }

        if ($status != 'ALL') {
            $vendas = $vendas->where('status_pay', $status);
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
        ]);
    }

    public function usuarios() {
        return view('dashboard.relatorio.usuarios', [
            'users' => User::all(),
        ]);
    }

    public function upusuarios(Request $request) {
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

    public function premiados() {
        $vendasPremiadas = Vendas::where('status_produto', 'PREMIADO')->get();
        return view('dashboard.relatorio.premiados', [
            'vendasPremiadas' => $vendasPremiadas,
            'vendas'          => Vendas::all()
        ]);
    }

    public function cria_premiados(Request $request) {
        $venda = Vendas::where('id', $request->id)->first();
        if($venda){
            $venda->status_produto = 'PREMIADO';
            $venda->save();
        }

        $vendasPremiadas = Vendas::where('status_produto', 'PREMIADO')->get();
        return view('dashboard.relatorio.premiados', [
            'vendasPremiadas' => $vendasPremiadas,
            'vendas'          => Vendas::all()
        ]);
    }

    public function atualiza_premiados(Request $request) {
        $venda = Vendas::where('id', $request->id)->first();
        if($venda){
            $venda->status_produto = '';
            $venda->save();
        }

        $vendasPremiadas = Vendas::where('status_produto', 'PREMIADO')->get();
        return view('dashboard.relatorio.premiados', [
            'vendasPremiadas' => $vendasPremiadas,
            'vendas'          => Vendas::all()
        ]);
    }

    public function relatorioParcelas($id = null) {
        $users = auth()->user();

        if($id) {
            $parcelas = VendaParcela::where('venda_id', $id)->get();
        } else {
            $parcelas = VendaParcela::where('cpf', $users->cpf)->get();
        }

        return view('dashboard.relatorio.parcelas', [
            'parcelas' => $parcelas,
        ]);
    }

    public function relatorioContratos() {
        $users = auth()->user();

        $vendas = Vendas::where('cpf', $users->cpf)->get();

        return view('dashboard.relatorio.contratos', [
            'vendas' => $vendas,
        ]);
    }
}
