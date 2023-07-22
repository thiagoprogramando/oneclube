<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cpf');
            $table->string('rg');
            $table->string('endereco');
            $table->string('telefone');
            $table->string('email');
            $table->unsignedBigInteger('id_contrato');
            $table->unsignedBigInteger('id_produto');
            $table->unsignedBigInteger('id_pay');
            $table->unsignedBigInteger('id_vendedor');
            $table->decimal('valor', 10, 2);
            $table->string('status_pay');
            $table->string('status_produto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendas');
    }
};
