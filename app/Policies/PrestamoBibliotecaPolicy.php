<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PrestamoBiblioteca;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrestamoBibliotecaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_prestamo_biblioteca');
    }

    public function view(AuthUser $authUser, PrestamoBiblioteca $prestamoBiblioteca): bool
    {
        return $authUser->can('view_prestamo_biblioteca');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create_prestamo_biblioteca');
    }

    public function update(AuthUser $authUser, PrestamoBiblioteca $prestamoBiblioteca): bool
    {
        return $authUser->can('update_prestamo_biblioteca');
    }

    public function delete(AuthUser $authUser, PrestamoBiblioteca $prestamoBiblioteca): bool
    {
        return $authUser->can('delete_prestamo_biblioteca');
    }

    public function restore(AuthUser $authUser, PrestamoBiblioteca $prestamoBiblioteca): bool
    {
        return $authUser->can('restore_prestamo_biblioteca');
    }

    public function forceDelete(AuthUser $authUser, PrestamoBiblioteca $prestamoBiblioteca): bool
    {
        return $authUser->can('force_delete_prestamo_biblioteca');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('force_delete_any_prestamo_biblioteca');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('restore_any_prestamo_biblioteca');
    }

    public function replicate(AuthUser $authUser, PrestamoBiblioteca $prestamoBiblioteca): bool
    {
        return $authUser->can('replicate_prestamo_biblioteca');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('reorder_prestamo_biblioteca');
    }

}