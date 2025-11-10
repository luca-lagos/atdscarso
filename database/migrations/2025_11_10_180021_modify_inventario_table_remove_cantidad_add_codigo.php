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
        Schema::table('inventario', function (Blueprint $table) {
            // Agregar código de identificación único (S/N)
            $table->string('codigo_identificacion', 100)
                ->nullable()
                ->after('nombre_equipo')
                ->comment('Número de serie o código único del equipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventario', function (Blueprint $table) {
            $table->dropColumn('codigo_identificacion');
        });
    }
};
