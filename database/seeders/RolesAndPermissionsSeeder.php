<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Resetear caché de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ========================================
        // 1. CREAR ROLES
        // ========================================
        $adminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $profesorRole = Role::firstOrCreate(['name' => 'profesor', 'guard_name' => 'web']);
        $alumnoRole = Role::firstOrCreate(['name' => 'alumno', 'guard_name' => 'web']);

        $this->command->info('✅ Roles creados: super_admin, profesor, alumno');

        // ========================================
        // 2. PERMISOS PARA SELF-PANEL (Alumnos)
        // ========================================
        $alumnoPermisos = [
            // Ver sus propios préstamos
            'view_prestamo',
            'view_prestamo_biblioteca',

            // Solicitar préstamos (quedan pendientes)
            'create_prestamo',
            'create_prestamo_biblioteca',

            // Ver turnos disponibles
            'view_any_turnos_sala',
            'view_any_turnos_tv',
        ];

        foreach ($alumnoPermisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        $alumnoRole->syncPermissions($alumnoPermisos);
        $this->command->info('✅ Permisos asignados al rol "alumno"');

        // ========================================
        // 3. PERMISOS PARA SELF-PANEL (Profesores)
        // ========================================
        $profesorPermisos = [
            // Ver sus propios préstamos
            'view_prestamo',
            'view_prestamo_biblioteca',

            // Crear préstamos (quedan activos automáticamente)
            'create_prestamo',
            'create_prestamo_biblioteca',

            // Ver y gestionar turnos
            'view_any_turnos_sala',
            'view_any_turnos_tv',
            'create_turnos_sala',
            'create_turnos_tv',
            'update_turnos_sala',
            'update_turnos_tv',
            'delet_turnos_sala',
            'delete_turnos_tv',

            // Ver inventario disponible (solo lectura)
            'view_any_inventario',
            'view_any_inventario_biblioteca',
        ];

        foreach ($profesorPermisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        $profesorRole->syncPermissions($profesorPermisos);
        $this->command->info('✅ Permisos asignados al rol "profesor"');

        // ========================================
        // 4. PERMISOS ADMINISTRATIVOS (Super Admin)
        // ========================================
        // El super_admin tiene TODOS los permisos
        // Filament Shield ya genera automáticamente los permisos de recursos
        // Aquí solo agregamos los personalizados si es necesario

        $adminPermisos = Permission::all();
        $adminRole->syncPermissions($adminPermisos);
        $this->command->info('✅ Todos los permisos asignados al rol "super-admin"');

        // ========================================
        // 5. PERMISOS GENERADOS POR FILAMENT SHIELD
        // ========================================
        $this->command->warn('⚠️  Recordá ejecutar: php artisan shield:generate --all');
        $this->command->warn('    Esto generará los permisos de recursos automáticamente.');

        $this->command->info('');
        $this->command->info('🎉 Seeder completado exitosamente');
    }
}
