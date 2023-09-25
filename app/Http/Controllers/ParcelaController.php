<?php

namespace App\Http\Controllers;

use App\Models\VendaParcela;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ParcelaController extends Controller
{
    public function index()
    {
        $parcela = VendaParcela::all();

        return view('dashboard.relatorio.parcela',['parcela'=> $parcela]);
    }

    public function relatorioAction(Request $request)
    {
        $parcelaId = $request->input('parcela_id');

        $parcela = VendaParcela::find($parcelaId);

        if (!$parcela) {
            Session::flash('erro', 'Parcela não encontrada.');
        } else {
            if ($parcela->status === 'PAYMENT_CONFIRMED') {
                $parcela->status = 'PENDING_PAY';
                Session::flash('sucesso', 'Status atualizado para PENDING_PAY.');
            } elseif ($parcela->status === 'PENDING_PAY') {
                $parcela->status = 'PAYMENT_CONFIRMED';
                Session::flash('sucesso', 'Status atualizado para PAYMENT_CONFIRMED.');
            }

            $parcela->save();
        }

        return redirect()->back();
    }
}
