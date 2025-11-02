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
        return $authUser->can('ViewAny:PrestamoBiblioteca');
    }

    public function view(AuthUser $authUser, PrestamoBiblioteca $prestamoBiblioteca): bool
    {
        return $authUser->can('View:PrestamoBiblioteca');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PrestamoBiblioteca');
    }

    public function update(AuthUser $authUser, PrestamoBiblioteca $prestamoBiblioteca): bool
    {
        return $authUser->can('Update:PrestamoBiblioteca');
    }

    public function delete(AuthUser $authUser, PrestamoBiblioteca $prestamoBiblioteca): bool
    {
        return $authUser->can('Delete:PrestamoBiblioteca');
    }

    public function restore(AuthUser $authUser, PrestamoBiblioteca $prestamoBiblioteca): bool
    {
        return $authUser->can('Restore:PrestamoBiblioteca');
    }

    public function forceDelete(AuthUser $authUser, PrestamoBiblioteca $prestamoBiblioteca): bool
    {
        return $authUser->can('ForceDelete:PrestamoBiblioteca');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PrestamoBiblioteca');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PrestamoBiblioteca');
    }

    public function replicate(AuthUser $authUser, PrestamoBiblioteca $prestamoBiblioteca): bool
    {
        return $authUser->can('Replicate:PrestamoBiblioteca');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PrestamoBiblioteca');
    }

}