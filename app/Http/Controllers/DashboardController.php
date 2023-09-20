<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Vendas;
use App\Models\VendaLance;

class DashboardController extends Controller
{
    public function dashboard (){
        $user = auth()->user();

        if($user->tipo == 4){
            $vendas = Vendas::where('cpf', $user->cpf)->get();
            $vendas->each(function ($venda) {
                $venda->total_parcelas_confirmadas = $venda->vendaParcelas()->where('status', 'PAYMENT_CONFIRMED')->count();
                $venda->total_parcelas_confirmadas_valor = $venda->vendaParcelas()->where('status', 'PAYMENT_CONFIRMED')->sum('valor');
                $venda->soma_ofertas = VendaLance::where('venda_id', $venda->id)->sum('oferta');
            });
        } else {
            $vendas = Vendas::where('id_vendedor', $user->id)->limit(15)->get();
        }

        $mes = Carbon::now()->month;
        $lances = VendaLance::where('user_id', $user->id)->where('mes', $mes)->get();

        return view('dashboard.index', [
            'user' => $user,
            'vendas' => $vendas,
            'lances' => $lances
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
