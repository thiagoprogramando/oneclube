<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcela extends Model
{
    use HasFactory;

    protected $table = 'parcela';

    protected $fillable = [
        'id_venda',
        'n_parcela',
        'vencimento',
        'valor',
        'status',
        'codigocliente',
        'txid',
        'numerocontratocobranca',
        'linhadigitavel',
        'numero',
    ];
}
