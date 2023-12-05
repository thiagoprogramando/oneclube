<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {

        Schema::create('sale', function (Blueprint $table) {

            $table->id();
            $table->string('name');
            $table->string('cpfcnpj');
            $table->string('rg')->nullable();
            $table->string('address');
            $table->date('birthDate');

            $table->string('mobilePhone');
            $table->string('email')->nullable();

            $table->string('id_contrato')->nullable();
            $table->string('id_pay')->nullable();
            $table->unsignedBigInteger('id_produto');
            $table->unsignedBigInteger('id_vendedor');

            $table->decimal('value', 10, 2);
            $table->integer('comission');
            $table->string('billingType');
            $table->string('installmentCount');

            $table->string('status_pay')->nullable();
            $table->string('status_produto')->nullable();
            
            $table->text('file')->nullable();
            $table->text('sign_url')->nullable();
            $table->timestamps();

        });
    }

    public function down(): void {
        Schema::dropIfExists('vendas');
    }
};