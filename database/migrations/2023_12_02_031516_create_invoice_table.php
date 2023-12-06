<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {

        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
            $table->integer('idUser');
            $table->string('name');
            $table->string('description');
            $table->string('token')->nullable();
            $table->string('qrcode')->nullable();
            $table->string('url')->nullable();
            $table->decimal('value', 10, 2);
            $table->integer('type'); // 1 - Entrada 2 - Mensalidade 3 - Serviços Extras
            $table->string('status');
            $table->string('dueDate')->nullable();
            $table->timestamps();
        });

    }

    public function down(): void {

        Schema::dropIfExists('invoice');
    }
};
