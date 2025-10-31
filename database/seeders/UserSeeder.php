<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crea roles
        $roles = ['super-admin', 'admin', 'informatico', 'biblioteca', 'profesor', 'alumno'];
        foreach ($roles as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Asigna todos los permisos a super-admin
        $super = Role::findByName('super-admin');
        $super->givePermissionTo(Permission::all());
    }
}
