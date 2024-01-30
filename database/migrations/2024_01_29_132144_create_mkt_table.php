<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('mkt', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_product');
            $table->string('title');
            $table->string('description');
            $table->string('file');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('mkt');
    }
};
