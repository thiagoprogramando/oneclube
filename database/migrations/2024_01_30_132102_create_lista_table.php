<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('lista', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('dateEnd');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('lista');
    }
};
