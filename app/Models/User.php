<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    protected $table = 'users';

    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nome',
        'email',
        'password',
        'created_at',
        'updated_at',
        'cpf',
        'tipo',
        'status',
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

}
