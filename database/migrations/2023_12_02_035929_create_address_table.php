<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('address', function (Blueprint $table) {
            $table->id();
            $table->integer('idUser');
            $table->string('postalCode');
            $table->string('address');
            $table->string('addressNumber');
            $table->string('complement')->nullable();
            $table->string('province');
            $table->string('city');
            $table->string('state');
            $table->timestamps();
        });
    }

    public function down(): void {

        Schema::dropIfExists('address');
    }
};
