<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupom extends Model
{
    use HasFactory;

    protected $table = 'cupom'; 

    protected $fillable = [
        'id',
        'titulo', 
        'codigo', 
        'created_at',
    ];

}
