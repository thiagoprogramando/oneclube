<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendaParcela extends Model
{
    use HasFactory;
    protected $table = 'venda_parcelas';

    protected $fillable = [
        'venda_id', 'numero_parcela', 'valor', 'vencimento', 'id_assas', 'status'
    ];

    public function venda()
    {
        return $this->belongsTo(Venda::class, 'venda_id');
    }
}
