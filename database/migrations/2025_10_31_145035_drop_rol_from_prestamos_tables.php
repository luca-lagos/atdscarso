<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach (['prestamos_biblioteca', 'prestamos_informatica'] as $table) {
            if (Schema::hasColumn($table, 'rol')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('rol');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['prestamos_biblioteca', 'prestamos_informatica'] as $table) {
            if (!Schema::hasColumn($table, 'rol')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->string('rol')->nullable(); // solo por rollback
                });
            }
        }
    }
};
