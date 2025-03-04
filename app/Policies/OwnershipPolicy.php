<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Ownership;
use App\Models\User;

class OwnershipPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Ownership');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ownership $ownership): bool
    {
        return $user->checkPermissionTo('view Ownership');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Ownership');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ownership $ownership): bool
    {
        return $user->checkPermissionTo('update Ownership');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ownership $ownership): bool
    {
        return $user->checkPermissionTo('delete Ownership');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Ownership');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ownership $ownership): bool
    {
        return $user->checkPermissionTo('restore Ownership');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Ownership');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Ownership $ownership): bool
    {
        return $user->checkPermissionTo('replicate Ownership');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Ownership');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ownership $ownership): bool
    {
        return $user->checkPermissionTo('force-delete Ownership');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Ownership');
    }
}
