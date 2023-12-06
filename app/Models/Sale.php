<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sale';

    protected $fillable = [
        'name',
        'cpfcnpj',
        'birthDate',
        'rg',
        'address',

        'mobilePhone',
        'email',
        
        'id_contrato',
        'id_ficha',
        'id_pay',
        'id_produto',
        'id_vendedor',

        'value',
        'comission',
        'billingType',
        'installmentCount',

        'status_pay',
        'status_produto',

        'file_contrato',
        'sign_url_contrato',
        'file_ficha',
        'sign_url_ficha'
    ];
}
