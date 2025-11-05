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
            // Ajusta el nombre de la tabla si difiere
            $table->string('estante', 50)->nullable()->after('coleccion');
            $table->string('columna', 50)->nullable()->after('estante');

            // Índices opcionales si vas a filtrar/ordenar por estas columnas
            $table->index('estante');
            $table->index('columna');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventario_biblioteca', function (Blueprint $table) {
            $table->dropIndex(['estante']);
            $table->dropIndex(['columna']);
            $table->dropColumn(['estante', 'columna']);
        });
    }
};
