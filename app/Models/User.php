<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    protected $table = 'users';

    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'mobilePhone',
        'address',
        'password',
        'cpfcnpj',
        'birthDate',
        'companyType',
        'type',
        'status',  // 1 - Ativo 2 - Pendente de Aprovação 3 - Pendente de Pagamento
        'walletId',
        'apiKey',
        'term'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'updatedAt' => 'datetime',
        'createdAt' => 'datetime',
    ];

    public function getAuthPassword(){
        return $this->password;
    }

    public function getUpdatedAt(){
        return $this->updatedAt;
    }

    protected $appends = ['status_user', 'type_user'];

    public function getStatusUserAttribute() {

        $statusMapping = [
            1 => 'Conta Ativa',
            2 => 'Conta Pendente de Aprovação',
            3 => 'Pendente de Pagamento',
        ];

        return $statusMapping[$this->attributes['status']];
    }

    public function getTypeUserAttribute() {

        $typeMapping = [
            1 => 'Administrador',
            2 => 'Assinante',
            3 => 'Outros',
        ];

        return $typeMapping[$this->attributes['type']];
    }

}
