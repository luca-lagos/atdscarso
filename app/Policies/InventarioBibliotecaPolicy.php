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
        return $authUser->can('ViewAny:InventarioBiblioteca');
    }

    public function view(AuthUser $authUser, InventarioBiblioteca $inventarioBiblioteca): bool
    {
        return $authUser->can('View:InventarioBiblioteca');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:InventarioBiblioteca');
    }

    public function update(AuthUser $authUser, InventarioBiblioteca $inventarioBiblioteca): bool
    {
        return $authUser->can('Update:InventarioBiblioteca');
    }

    public function delete(AuthUser $authUser, InventarioBiblioteca $inventarioBiblioteca): bool
    {
        return $authUser->can('Delete:InventarioBiblioteca');
    }

    public function restore(AuthUser $authUser, InventarioBiblioteca $inventarioBiblioteca): bool
    {
        return $authUser->can('Restore:InventarioBiblioteca');
    }

    public function forceDelete(AuthUser $authUser, InventarioBiblioteca $inventarioBiblioteca): bool
    {
        return $authUser->can('ForceDelete:InventarioBiblioteca');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:InventarioBiblioteca');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:InventarioBiblioteca');
    }

    public function replicate(AuthUser $authUser, InventarioBiblioteca $inventarioBiblioteca): bool
    {
        return $authUser->can('Replicate:InventarioBiblioteca');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:InventarioBiblioteca');
    }

}