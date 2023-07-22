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
        'status_pay',
        'status_produto',
        'updatedat',
        'createdat'
    ];

    protected $hidden = [
        'passwordHash',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'updatedAt' => 'datetime',
        'createdAt' => 'datetime',
    ];

    public function getAuthPassword(){
        return $this->passwordHash;
    }

    public function getUpdatedAt(){
        return $this->updatedAt;
    }

}
