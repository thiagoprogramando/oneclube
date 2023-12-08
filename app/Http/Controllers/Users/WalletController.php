<?php

namespace App\Http\Controllers\USers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gatway\AssasController;
use Illuminate\Http\Request;

class WalletController extends Controller {
    
    public function wallet() {

        $assas = new AssasController();
        $balance = $assas->balance();
        if($balance == 0 || $balance > 0) {
            $statistics = $assas->statistics();
            return view('dashboard.wallet.wallet', ['balance' => $balance, 'statistics' => $statistics]);
        }
        
        return view('dashboard.wallet.wallet', ['balance' => 'Erro!', 'statistics' => 'Erro!']);
    }

}
