<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendas', function (Blueprint $table) {
            $table->string('status_contrato')->nullable();
            $table->string('cep')->nullable();
            $table->string('endereco')->nullable();
        });
    }

    public function down(): void
    {

    }
};
