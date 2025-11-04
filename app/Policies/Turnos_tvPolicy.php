<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Turnos_tv;
use Illuminate\Auth\Access\HandlesAuthorization;

class Turnos_tvPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_turnos_tv');
    }

    public function view(AuthUser $authUser, Turnos_tv $turnosTv): bool
    {
        return $authUser->can('view_turnos_tv');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_turnos_tv');
    }

    public function update(AuthUser $authUser, Turnos_tv $turnosTv): bool
    {
        return $authUser->can('update_turnos_tv');
    }

    public function delete(AuthUser $authUser, Turnos_tv $turnosTv): bool
    {
        return $authUser->can('delete_turnos_tv');
    }

    public function restore(AuthUser $authUser, Turnos_tv $turnosTv): bool
    {
        return $authUser->can('restore_turnos_tv');
    }

    public function forceDelete(AuthUser $authUser, Turnos_tv $turnosTv): bool
    {
        return $authUser->can('force_delete_turnos_tv');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_turnos_tv');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_turnos_tv');
    }

    public function replicate(AuthUser $authUser, Turnos_tv $turnosTv): bool
    {
        return $authUser->can('replicate_turnos_tv');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_turnos_tv');
    }

}