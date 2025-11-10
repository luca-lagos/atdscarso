<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventario_biblioteca', function (Blueprint $table) {
            $table->dropColumn('cantidad');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventario_biblioteca', function (Blueprint $table) {
            $table->unsignedSmallInteger('cantidad')->default(1);
        });
    }
};
