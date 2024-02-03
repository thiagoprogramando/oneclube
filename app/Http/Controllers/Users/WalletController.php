<?php

namespace App\Http\Controllers\USers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gatway\AssasController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class WalletController extends Controller {
    
    public function wallet() {

        $assas = new AssasController();
        $balance = $assas->balance();
        if($balance == 0 || $balance > 0) {
            $statistics = $assas->statistics();
            $extracts    = $assas->extract();
            return view('dashboard.wallet.wallet', ['balance' => $balance, 'statistics' => $statistics, 'extracts' => $extracts]);
        }
        
        return view('dashboard.wallet.wallet', ['balance' => 'Falha!', 'statistics' => 'Falha!']);
    }

    public function withdraw(Request $request) {

        $request->validate([
            'key_pix'   => 'required',
            'password'  => 'required|string',
            'value'     => 'required'
        ]);

        $usuario = Auth::user();
        if (Hash::check($request->password, $usuario->password)) {
            
            $assas = new AssasController();
            $key_pix = $request->key_pix != 'EMAIL' ? str_replace(['.', '-', '_', ','], '', $request->key_pix) : $request->key_pix;
            $realizaSaque = $assas->withdraw($key_pix, $this->formatarValor($request->value), $request->type);
            
            if ($realizaSaque['success']) {
                return redirect()->back()->with('success', $realizaSaque['message']);
            }
            
            return redirect()->back()->with('error', 'Tivemos um problema ao realizar o saque: ' . $realizaSaque['error']);
        } else {
            return redirect()->back()->with('error', 'Verifique sua senha!');
        }
    }

    private function formatarValor($valor) {
        
        $valor = preg_replace('/[^0-9,.]/', '', $valor);
        $valor = str_replace(['.', ','], '', $valor);

        return number_format(floatval($valor) / 100, 2, '.', '');
    }

}
