<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Prestamo;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrestamoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_prestamo');
    }

    public function view(AuthUser $authUser, Prestamo $prestamo): bool
    {
        return $authUser->can('view_prestamo');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_prestamo');
    }

    public function update(AuthUser $authUser, Prestamo $prestamo): bool
    {
        return $authUser->can('update_prestamo');
    }

    public function delete(AuthUser $authUser, Prestamo $prestamo): bool
    {
        return $authUser->can('delete_prestamo');
    }

    public function restore(AuthUser $authUser, Prestamo $prestamo): bool
    {
        return $authUser->can('restore_prestamo');
    }

    public function forceDelete(AuthUser $authUser, Prestamo $prestamo): bool
    {
        return $authUser->can('force_delete_prestamo');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_prestamo');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_prestamo');
    }

    public function replicate(AuthUser $authUser, Prestamo $prestamo): bool
    {
        return $authUser->can('replicate_prestamo');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_prestamo');
    }

}