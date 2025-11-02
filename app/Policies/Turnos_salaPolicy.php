<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Turnos_sala;
use Illuminate\Auth\Access\HandlesAuthorization;

class Turnos_salaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TurnosSala');
    }

    public function view(AuthUser $authUser, Turnos_sala $turnosSala): bool
    {
        return $authUser->can('View:TurnosSala');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TurnosSala');
    }

    public function update(AuthUser $authUser, Turnos_sala $turnosSala): bool
    {
        return $authUser->can('Update:TurnosSala');
    }

    public function delete(AuthUser $authUser, Turnos_sala $turnosSala): bool
    {
        return $authUser->can('Delete:TurnosSala');
    }

    public function restore(AuthUser $authUser, Turnos_sala $turnosSala): bool
    {
        return $authUser->can('Restore:TurnosSala');
    }

    public function forceDelete(AuthUser $authUser, Turnos_sala $turnosSala): bool
    {
        return $authUser->can('ForceDelete:TurnosSala');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TurnosSala');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TurnosSala');
    }

    public function replicate(AuthUser $authUser, Turnos_sala $turnosSala): bool
    {
        return $authUser->can('Replicate:TurnosSala');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TurnosSala');
    }

}