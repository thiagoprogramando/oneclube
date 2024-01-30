<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mkt extends Model {

    use HasFactory;

    protected $table = 'mkt';

    protected $fillable = [
        'id_product',
        'name',
        'description',
        'file',
    ];
}
