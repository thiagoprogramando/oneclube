<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['cpf']);
            $table->string('cpf')->nullable()->change();
        });
    }

    public function down() //reverter a migration
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cpf')->unique()->change();
        });
    }
};