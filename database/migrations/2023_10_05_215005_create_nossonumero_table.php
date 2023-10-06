<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('nossonumero', function (Blueprint $table) {
            $table->id();
            $table->string('numeroConvenio', 7);
            $table->string('numeroControle', 10);
            $table->string('numeroTituloCliente', 20)->unique();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('nossonumero');
    }
};
