<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model {

    use HasFactory;

    protected static function boot() {
        parent::boot();

        static::saving(function ($sale) {
            $vendedor = User::find($sale->id_vendedor);

            if ($vendedor && $vendedor->walletId) {
                $sale->wallet = $vendedor->walletId;
            }
        });
    }

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
        'id_pay',
        'id_produto',
        'id_vendedor',

        'value',
        'comission',
        'wallet',
        'billingType',
        'installmentCount',

        'status_pay',
        'status_produto',
        'tag',

        'sign_url_contrato',
    ];

    public function vendedor() {
        return $this->belongsTo(User::class, 'id_vendedor');
    }
}
