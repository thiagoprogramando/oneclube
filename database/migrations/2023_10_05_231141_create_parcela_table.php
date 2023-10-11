<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void {
        Schema::create('parcela', function (Blueprint $table) {
            $table->id();
            $table->integer('id_venda');
            $table->integer('n_parcela');
            $table->date('vencimento');
            $table->decimal('valor', 10, 2);
            $table->string('status');
            $table->string('codigocliente')->nullable();
            $table->string('txid')->nullable();
            $table->string('numerocontratocobranca')->nullable();
            $table->longText('linhadigitavel')->nullable();
            $table->longText('url')->nullable();
            $table->longText('numero')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('parcela');
    }
};
