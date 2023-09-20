<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendaLance extends Model
{
    use HasFactory;

    protected $fillable = [
        'venda_id',
        'user_id',
        'pago',
        'oferta',
        'mes',
    ];
}
