<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Inventario;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventarioPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_inventario');
    }

    public function view(AuthUser $authUser, Inventario $inventario): bool
    {
        return $authUser->can('view_inventario');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_inventario');
    }

    public function update(AuthUser $authUser, Inventario $inventario): bool
    {
        return $authUser->can('update_inventario');
    }

    public function delete(AuthUser $authUser, Inventario $inventario): bool
    {
        return $authUser->can('delete_inventario');
    }

    public function restore(AuthUser $authUser, Inventario $inventario): bool
    {
        return $authUser->can('restore_inventario');
    }

    public function forceDelete(AuthUser $authUser, Inventario $inventario): bool
    {
        return $authUser->can('force_delete_inventario');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_inventario');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_inventario');
    }

    public function replicate(AuthUser $authUser, Inventario $inventario): bool
    {
        return $authUser->can('replicate_inventario');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_inventario');
    }

}