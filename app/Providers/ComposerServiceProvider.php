<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

use App\Models\Vendas;
use App\VendaParcelas;


class ComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // $vendasPremiadas = Vendas::select(['vendas.id', 'vendas.nome', 'vendas.updated_at', DB::raw('COUNT(parcelas.id) as parcelas_pagas_count')])
        // ->leftJoin('venda_parcelas as parcelas', function ($join) {
        //     $join->on('vendas.id', '=', 'parcelas.venda_id')
        //         ->where('parcelas.status', '=', 'PAYMENT_CONFIRMED');
        // })
        // ->leftJoin('venda_lances', 'vendas.id', '=', 'venda_lances.venda_id')
        // ->where('vendas.status_produto', 'PREMIADO')
        // ->where('vendas.id_produto', 3)
        // ->groupBy('vendas.id', 'vendas.nome', 'vendas.updated_at')
        // ->selectRaw('SUM(venda_lances.oferta) as soma_ofertas')
        // ->get();

        $vendasPremiadas = Vendas::select([
            'vendas.id',
            'vendas.nome',
            'vendas.updated_at',
            DB::raw('COUNT(parcelas.id) as parcelas_pagas_count'),
            DB::raw('SUM(venda_lances.oferta) as soma_ofertas')
        ])
        ->leftJoin('venda_parcelas as parcelas', function ($join) {
            $join->on('vendas.id', '=', 'parcelas.venda_id')
                ->where('parcelas.status', '=', 'PAYMENT_CONFIRMED');
        })
        ->leftJoin('venda_lances', 'vendas.id', '=', 'venda_lances.venda_id')
        ->where('vendas.status_produto', 'PREMIADO')
        ->where('vendas.id_produto', 11)
        ->groupBy('vendas.id', 'vendas.nome', 'vendas.updated_at')
        ->get();


        View::share('vencedores', $vendasPremiadas);

    }

}
