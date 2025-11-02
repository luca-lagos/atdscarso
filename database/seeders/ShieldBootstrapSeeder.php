<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ShieldBootstrapSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Generar permisos de todos los Resources/Pages/Widgets
        // Nota: si falla en entornos sin consola, puedes envolver en try/catch.
        Artisan::call('shield:generate', ['--ignore-existing-policies' => true]);

        // 2) Asegurar roles base
        $roles = [
            'super-admin',
            'admin',
            'informatico',
            'biblioteca',
            'profesor',
            'alumno',
        ];

        foreach ($roles as $name) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // 3) Dar todos los permisos al super-admin
        $super = Role::where('name', 'super-admin')->first();
        if ($super) {
            $super->givePermissionTo(Permission::all());
        }

        // 4) Usuario super-admin
        $email = 'super@escuela.test';
        $password = 'secret123';

        $user = User::updateOrCreate(
            ['email' => $email],
            ['name' => 'Super Admin', 'password' => Hash::make($password)]
        );

        if ($user && !$user->hasRole('super-admin')) {
            $user->assignRole('super-admin');
        }

        // 5) (Opcional) Usuarios demo para probar flujo de préstamos
        //    Comenta si no quieres crearlos.
        $demoUsers = [
            ['email' => 'profesor@escuela.test', 'name' => 'Profesor Demo', 'role' => 'profesor'],
            ['email' => 'alumno@escuela.test',   'name' => 'Alumno Demo',   'role' => 'alumno'],
        ];

        foreach ($demoUsers as $du) {
            $u = \App\Models\User::firstOrCreate(
                ['email' => $du['email']],
                ['name' => $du['name'], 'password' => Hash::make('secret123')]
            );
            if (!$u->hasRole($du['role'])) {
                $u->syncRoles([$du['role']]); // asegura solo ese rol demo
            }
        }

        $this->command?->info('Shield: permisos generados, roles creados, super-admin listo.');
        $this->command?->warn("Login super-admin: $email / $password");
    }
}
