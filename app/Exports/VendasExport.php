<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class VendasExport implements FromView
{
    protected $vendas;

    public function __construct($vendas)
    {
        $this->vendas = $vendas;
    }

    public function view(): View
    {
        return view('exports.vendas', [
            'vendas' => $this->vendas,
        ]);
    }
}

