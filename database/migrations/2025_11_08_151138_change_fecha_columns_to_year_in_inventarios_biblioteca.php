<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Si ya hay datos, conviene crear columnas nuevas, migrar, y dropear las viejas.
        Schema::table('inventario_biblioteca', function (Blueprint $table) {
            // crea columnas nuevas como integer
            $table->unsignedSmallInteger('anio_edicion')->nullable()->after('cantidad');
            $table->unsignedSmallInteger('anio_entrada')->nullable()->after('anio_edicion');
        });

        // migrar datos existentes (si las columnas viejas existen y son date)
        if (Schema::hasColumn('inventario_biblioteca', 'fecha_edicion')) {
            DB::statement("UPDATE inventario_biblioteca SET anio_edicion = EXTRACT(YEAR FROM fecha_edicion)::int WHERE fecha_edicion IS NOT NULL");
        }
        if (Schema::hasColumn('inventario_biblioteca', 'fecha_entrada')) {
            DB::statement("UPDATE inventario_biblioteca SET anio_entrada = EXTRACT(YEAR FROM fecha_entrada)::int WHERE fecha_entrada IS NOT NULL");
        }

        Schema::table('inventario_biblioteca', function (Blueprint $table) {
            // eliminar viejas y renombrar nuevas a los nombres anteriores (si quieres conservar mismos nombres)
            if (Schema::hasColumn('inventario_biblioteca', 'fecha_edicion')) {
                $table->dropColumn('fecha_edicion');
            }
            if (Schema::hasColumn('inventario_biblioteca', 'fecha_entrada')) {
                $table->dropColumn('fecha_entrada');
            }

            $table->renameColumn('anio_edicion', 'fecha_edicion');
            $table->renameColumn('anio_entrada', 'fecha_entrada');
        });
    }

    public function down(): void
    {
        Schema::table('inventario_biblioteca', function (Blueprint $table) {
            // revertir a date (sin migración de datos inversa por simplicidad)
            $table->date('fecha_edicion')->nullable()->change();
            $table->date('fecha_entrada')->nullable()->change();
        });
    }
};
