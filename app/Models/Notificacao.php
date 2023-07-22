<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Notificacao extends Model
{
    use  HasFactory, Notifiable;
    protected $table = 'crm_notificacao';

    const CREATED_AT = 'data';
    
    protected $fillable = [
        'mensagem',
        'data',
        'tipo',

    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'createdAt' => 'data',
    ];
}
