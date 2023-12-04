<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'address';

    protected $fillable = [
        'idUser',
        'postalCode',
        'address',
        'addressNumber',
        'complement',
        'province',
        'city',
        'state',
    ];
}
