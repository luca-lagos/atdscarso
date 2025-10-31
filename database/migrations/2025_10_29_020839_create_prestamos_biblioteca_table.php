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
        Schema::create('prestamos_biblioteca', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventario_biblioteca_id')->constrained('inventario_biblioteca')->cascadeOnDelete();
            if (!Schema::hasColumn('prestamos_biblioteca', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->after('inventario_biblioteca_id');
            }
            $table->enum('estado', ['pendiente', 'activo', 'vencido', 'devuelto', 'perdido'])->default('pendiente')->change();
            $table->date('fecha_prestamo');
            $table->date('fecha_vencimiento')->index();
            $table->date('fecha_devolucion')->nullable();
            $table->enum('estado', ['activo', 'vencido', 'devuelto', 'perdido'])->default('activo')->index();
            $table->unsignedTinyInteger('renovaciones')->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestamos_biblioteca');
    }
};
