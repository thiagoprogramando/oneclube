<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('material', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_curso');
            $table->foreign('id_curso')->references('id')->on('curso')->onDelete('cascade');
            $table->string('title');
            $table->longText('description');
            $table->string('file');
            $table->integer('type'); // 1 - Vídeo 2 - PDF 3 - Imagem
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('material');
    }

};
