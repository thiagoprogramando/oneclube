<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::table('invoice', function (Blueprint $table) {
            $table->decimal('commission', 10, 2)->after('value');
        });
    }

    public function down(): void {
        Schema::table('invoice', function (Blueprint $table) {
            //
        });
    }
};