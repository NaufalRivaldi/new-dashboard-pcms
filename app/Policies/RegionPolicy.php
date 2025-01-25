<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Region;
use App\Models\User;

class RegionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Region');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Region $region): bool
    {
        return $user->checkPermissionTo('view Region');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Region');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Region $region): bool
    {
        return $user->checkPermissionTo('update Region');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Region $region): bool
    {
        return $user->checkPermissionTo('delete Region');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Region');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Region $region): bool
    {
        return $user->checkPermissionTo('restore Region');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Region');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Region $region): bool
    {
        return $user->checkPermissionTo('replicate Region');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Region');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Region $region): bool
    {
        return $user->checkPermissionTo('force-delete Region');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Region');
    }
}
