<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;

use App\Models\Invoice;
use App\Models\Sale;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ClientController extends Controller {
    
    public function logarClient(Request $request) {

        $request->validate([
            'cpfcnpj' => 'required|numeric',
        ]);

        $sale = Sale::where('cpfcnpj', $request->input('cpfcnpj'))->first();
        if ($sale) {
            Session::put('cpfcnpj', $request->input('cpfcnpj'));
            return redirect()->route('vendasCliente');
        } else {
            return redirect()->back()->with('error', 'CPF ou CNPJ sem contratos!');
        }
    }

    public function vendasCliente() {

        $cpfcnpj = Session::get('cpfcnpj');
        $sales = Sale::where('cpfcnpj', $cpfcnpj)->get();

        return view('cliente.sales', ['sales' => $sales]);
    }

    public function faturasCliente($sale) {

        $invoices = Invoice::where('idUser', $sale)->where('type', 3)->get();

        return view('cliente.invoice', ['invoices' => $invoices]);
    }

    public function logoutClient() {

        Auth::logout();
        Session::forget('cpfcnpj');

        return view('cliente.index');
    }

}
