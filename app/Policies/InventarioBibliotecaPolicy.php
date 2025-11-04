<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\InventarioBiblioteca;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventarioBibliotecaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_inventario_biblioteca');
    }

    public function view(AuthUser $authUser, InventarioBiblioteca $inventarioBiblioteca): bool
    {
        return $authUser->can('view_inventario_biblioteca');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_inventario_biblioteca');
    }

    public function update(AuthUser $authUser, InventarioBiblioteca $inventarioBiblioteca): bool
    {
        return $authUser->can('update_inventario_biblioteca');
    }

    public function delete(AuthUser $authUser, InventarioBiblioteca $inventarioBiblioteca): bool
    {
        return $authUser->can('delete_inventario_biblioteca');
    }

    public function restore(AuthUser $authUser, InventarioBiblioteca $inventarioBiblioteca): bool
    {
        return $authUser->can('restore_inventario_biblioteca');
    }

    public function forceDelete(AuthUser $authUser, InventarioBiblioteca $inventarioBiblioteca): bool
    {
        return $authUser->can('force_delete_inventario_biblioteca');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_inventario_biblioteca');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_inventario_biblioteca');
    }

    public function replicate(AuthUser $authUser, InventarioBiblioteca $inventarioBiblioteca): bool
    {
        return $authUser->can('replicate_inventario_biblioteca');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_inventario_biblioteca');
    }

}