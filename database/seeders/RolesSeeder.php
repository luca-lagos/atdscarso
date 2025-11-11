<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'admin'     => 'Administrador general',
            'profesor'  => 'Docente o profesor',
            'alumno'    => 'Estudiante/alumno',
        ];

        foreach ($roles as $name => $desc) {
            Role::findOrCreate($name, 'web');
        }

        echo "✅ Roles creados o verificados correctamente.\n";
    }
}
