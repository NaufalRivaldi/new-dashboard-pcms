<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\ImportedFee;
use App\Models\User;

class ImportedFeePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any ImportedFee');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ImportedFee $importedfee): bool
    {
        return $user->checkPermissionTo('view ImportedFee');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create ImportedFee');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ImportedFee $importedfee): bool
    {
        return $user->checkPermissionTo('update ImportedFee');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ImportedFee $importedfee): bool
    {
        return $user->checkPermissionTo('delete ImportedFee');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any ImportedFee');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ImportedFee $importedfee): bool
    {
        return $user->checkPermissionTo('restore ImportedFee');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any ImportedFee');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, ImportedFee $importedfee): bool
    {
        return $user->checkPermissionTo('replicate ImportedFee');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder ImportedFee');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ImportedFee $importedfee): bool
    {
        return $user->checkPermissionTo('force-delete ImportedFee');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any ImportedFee');
    }
}
