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
        Schema::create('inventario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_equipo');
            $table->string('categoria'); // notebook, tv portátil, proyector...
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('nro_serie')->unique();
            $table->enum('estado', ['disponible','prestado','en_reparacion','baja'])->default('disponible');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario');
    }
};
