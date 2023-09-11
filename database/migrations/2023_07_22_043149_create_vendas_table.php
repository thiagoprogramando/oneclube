<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->nullable();
            $table->string('cpf');
            $table->string('telefone')->nullable();
            $table->string('email')->nullable();
            $table->string('id_contrato')->nullable();
            $table->unsignedBigInteger('id_produto');
            $table->string('id_pay')->nullable();
            $table->unsignedBigInteger('id_vendedor');
            $table->decimal('valor', 10, 2);
            $table->string('parcela')->nullable();
            $table->string('forma_pagamento')->nullable();
            $table->string('status_pay')->nullable();
            $table->string('cupom')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('vendas');
    }
};
