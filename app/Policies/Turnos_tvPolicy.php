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
        return $authUser->can('ViewAny:TurnosTv');
    }

    public function view(AuthUser $authUser, Turnos_tv $turnosTv): bool
    {
        return $authUser->can('View:TurnosTv');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TurnosTv');
    }

    public function update(AuthUser $authUser, Turnos_tv $turnosTv): bool
    {
        return $authUser->can('Update:TurnosTv');
    }

    public function delete(AuthUser $authUser, Turnos_tv $turnosTv): bool
    {
        return $authUser->can('Delete:TurnosTv');
    }

    public function restore(AuthUser $authUser, Turnos_tv $turnosTv): bool
    {
        return $authUser->can('Restore:TurnosTv');
    }

    public function forceDelete(AuthUser $authUser, Turnos_tv $turnosTv): bool
    {
        return $authUser->can('ForceDelete:TurnosTv');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TurnosTv');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TurnosTv');
    }

    public function replicate(AuthUser $authUser, Turnos_tv $turnosTv): bool
    {
        return $authUser->can('Replicate:TurnosTv');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TurnosTv');
    }

}