<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('curso', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->string('title');
            $table->longText('description');
            $table->decimal('value', 10,2);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('curso');
    }

};
