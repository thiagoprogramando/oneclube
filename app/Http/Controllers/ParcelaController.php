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
        
        // Busque a VendaParcela pelo ID
        $parcela = VendaParcela::find($parcelaId);
        
        if (!$parcela) {
            // Lide com o caso em que a parcela não é encontrada, se necessário.
            Session::flash('erro', 'Parcela não encontrada.');
        } else {
            // Verifique o campo 'status' e atualize conforme as condições
            if ($parcela->status === 'PAYMENT_CONFIRMED') {
                $parcela->status = 'PENDING_PAY';
                Session::flash('sucesso', 'Status atualizado para PENDING_PAY.');
            } elseif ($parcela->status === 'PENDING_PAY') {
                $parcela->status = 'PAYMENT_CONFIRMED';
                Session::flash('sucesso', 'Status atualizado para PAYMENT_CONFIRMED.');
            }
            
            // Salve as alterações no banco de dados
            $parcela->save();
        }
    
        // Redirecione para a página anterior com as mensagens
        return redirect()->back();
    }
}
