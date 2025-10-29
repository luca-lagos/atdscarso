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
        Schema::create('inventario_biblioteca', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('isbn')->nullable()->index();
            $table->string('autor')->index();
            $table->string('editorial')->nullable();
            $table->string('categoria')->nullable();
            $table->string('idioma')->nullable();
            $table->date('fecha_edicion');
            $table->date('fecha_entrada');
            $table->string('procedencia')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('portada_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_biblioteca');
    }
};
