<?php

namespace App\Providers;

use App\Models\Sale;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider {

    public function register(): void {

    }

    public function boot(): void {
        Schema::defaultStringLength(191);
    
        View::composer('*', function ($view) {
            $user = Auth::user();
    
            if ($user) {
                $ranking = $this->calcularRanking($user->id);
                $view->with('ranking', $ranking);
            }
        });
    }
    
    private function calcularRanking($id) {
        $sumSale = Sale::where('id_vendedor', $id)->where('status_pay', 'PAYMENT_CONFIRMED')->sum('value');
    
        $valoresAlvo = [10000, 30000, 50000, 100000, 300000, 500000, 1000000];
    
        foreach ($valoresAlvo as $valorAlvo) {
            if ($sumSale < $valorAlvo) {
                $resultado = $this->calcularPorcentagem($valorAlvo, $sumSale);
                break;
            }
        }
    
        $ranking = [
            'alvo' => $valorAlvo,
            'porcentagem' => $resultado ?? 100,
        ];
    
        return $ranking;
    }
    
    private function calcularPorcentagem($valorAlvo, $sumSale) {
        return max(0, (1 - ($valorAlvo - $sumSale) / $valorAlvo) * 100);
    }
}    
