<?php

namespace App\Http\Controllers;

use App\Models\Vendas;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function portalCliente() {
        return view('cliente.login');
    }

    public function consultaCliente(Request $request) {
        $cliente = Vendas::where('cpf', $request->cpfcnpj)->first();
        if($cliente) {
            $vendas = Vendas::where('cpf', $cliente->cpf)->get();

            return view('cliente.vendas', ['cliente' => $cliente, 'vendas' => $vendas]);
        } else {
            return redirect()->back()->withErrors(['error' => 'Não encontramos nenhuma informação para seu CPF ou CNPJ, verifique suas informações!']);
        }

    }
}
