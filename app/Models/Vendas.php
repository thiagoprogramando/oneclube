<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\HasApiTokens;

class Vendas extends Authenticatable
{

    protected $table = 'vendas';

    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nome',
        'cpf',
        'telefone',
        'email',
        'endereco',
        'id_produto',
        'id_vendedor',
        'valor',
        'updatedat',
        'createdat'
    ];

}
