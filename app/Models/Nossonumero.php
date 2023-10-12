<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nossonumero extends Model
{
    use HasFactory;

    protected $table = 'nossonumero';

    protected $fillable = [
        'numeroConvenio',
        'numeroControle',
        'numeroTituloCliente',
    ];

    public static function gerarNumeroTituloCliente()
    {
        $numeroConvenio = env('NUMEROCONVENIO');

        $ultimoNumeroControle = self::select('numeroControle')
            ->where('numeroConvenio', $numeroConvenio)
            ->orderBy('numeroControle', 'desc')
            ->first();

        if ($ultimoNumeroControle) {
            $novoNumeroControle = $ultimoNumeroControle->numeroControle + 1;
        } else {
            $novoNumeroControle = 1;
        }

        $numeroConvenio = str_pad($numeroConvenio, 7, '0', STR_PAD_LEFT);
        $novoNumeroControle = str_pad($novoNumeroControle, 10, '0', STR_PAD_LEFT);
        $numeroTituloCliente = "000{$numeroConvenio}{$novoNumeroControle}";

        Nossonumero::create([
            'numeroConvenio' => $numeroConvenio,
            'numeroControle' => $novoNumeroControle,
            'numeroTituloCliente' => $numeroTituloCliente,
        ]);

        return $numeroTituloCliente;
    }
}
