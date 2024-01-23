<?php

namespace App\Imports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SalesImport implements ToModel, WithHeadingRow
{
    protected $status;
    protected $tag;

    public function __construct($status, $tag)
    {
        $this->status = $status;
        $this->tag = $tag;
    }

    public function model(array $row)
    {
        // Atualiza o modelo Sale com base no ID
        Sale::where('id', $row['id'])
            ->update([
                'status_pay' => $this->status,
                'tag' => $this->tag,
            ]);

        // Pode retornar null, pois não estamos criando novos modelos
        return null;
    }
}
