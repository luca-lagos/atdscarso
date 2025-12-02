<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Turnos Sala: agregar 'finalizado' al CHECK
        DB::statement('ALTER TABLE turnos_sala DROP CONSTRAINT IF EXISTS turnos_sala_estado_check;');
        DB::statement("
            ALTER TABLE turnos_sala
            ADD CONSTRAINT turnos_sala_estado_check
            CHECK (estado IN (
                'activo',
                'pendiente',
                'cancelado',
                'finalizado'
            ));
        ");

        // Turnos TV: agregar 'finalizado' al CHECK
        DB::statement('ALTER TABLE turnos_tv DROP CONSTRAINT IF EXISTS turnos_tv_estado_check;');
        DB::statement("
            ALTER TABLE turnos_tv
            ADD CONSTRAINT turnos_tv_estado_check
            CHECK (estado IN (
                'activo',
                'pendiente',
                'cancelado',
                'finalizado'
            ));
        ");
    }

    public function down(): void
    {
        // Revertir: quitar 'finalizado'
        DB::statement('ALTER TABLE turnos_sala DROP CONSTRAINT IF EXISTS turnos_sala_estado_check;');
        DB::statement("
            ALTER TABLE turnos_sala
            ADD CONSTRAINT turnos_sala_estado_check
            CHECK (estado IN (
                'activo',
                'pendiente',
                'cancelado'
            ));
        ");

        DB::statement('ALTER TABLE turnos_tv DROP CONSTRAINT IF EXISTS turnos_tv_estado_check;');
        DB::statement("
            ALTER TABLE turnos_tv
            ADD CONSTRAINT turnos_tv_estado_check
            CHECK (estado IN (
                'activo',
                'pendiente',
                'cancelado'
            ));
        ");
    }
};
