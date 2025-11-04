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
        return $authUser->can('view_any_turnos_sala');
    }

    public function view(AuthUser $authUser, Turnos_sala $turnosSala): bool
    {
        return $authUser->can('view_turnos_sala');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_turnos_sala');
    }

    public function update(AuthUser $authUser, Turnos_sala $turnosSala): bool
    {
        return $authUser->can('update_turnos_sala');
    }

    public function delete(AuthUser $authUser, Turnos_sala $turnosSala): bool
    {
        return $authUser->can('delete_turnos_sala');
    }

    public function restore(AuthUser $authUser, Turnos_sala $turnosSala): bool
    {
        return $authUser->can('restore_turnos_sala');
    }

    public function forceDelete(AuthUser $authUser, Turnos_sala $turnosSala): bool
    {
        return $authUser->can('force_delete_turnos_sala');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_turnos_sala');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_turnos_sala');
    }

    public function replicate(AuthUser $authUser, Turnos_sala $turnosSala): bool
    {
        return $authUser->can('replicate_turnos_sala');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_turnos_sala');
    }

}