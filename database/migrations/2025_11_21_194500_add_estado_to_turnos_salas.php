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
        Schema::table('turnos_sala', function (Blueprint $table) {
            $table->enum('estado', ['activo', 'confirmado', 'cancelado', 'finalizado'])->default('activo')->after('observaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turnos_salas', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};
