<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobilePhone');
            $table->string('address');
            $table->string('password');
            $table->string('cpfcnpj')->unique();
            $table->date('birthDate');
            $table->string('companyType');
            $table->integer('type');
            $table->integer('status');
            $table->string('walletId')->nullable();
            $table->string('apiKey')->nullable();
            $table->string('customer')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};
