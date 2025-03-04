<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\ImportedFeeDetail;
use App\Models\User;

class ImportedFeeDetailPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any ImportedFeeDetail');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ImportedFeeDetail $importedfeedetail): bool
    {
        return $user->checkPermissionTo('view ImportedFeeDetail');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create ImportedFeeDetail');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ImportedFeeDetail $importedfeedetail): bool
    {
        return $user->checkPermissionTo('update ImportedFeeDetail');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ImportedFeeDetail $importedfeedetail): bool
    {
        return $user->checkPermissionTo('delete ImportedFeeDetail');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any ImportedFeeDetail');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ImportedFeeDetail $importedfeedetail): bool
    {
        return $user->checkPermissionTo('restore ImportedFeeDetail');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any ImportedFeeDetail');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, ImportedFeeDetail $importedfeedetail): bool
    {
        return $user->checkPermissionTo('replicate ImportedFeeDetail');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder ImportedFeeDetail');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ImportedFeeDetail $importedfeedetail): bool
    {
        return $user->checkPermissionTo('force-delete ImportedFeeDetail');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any ImportedFeeDetail');
    }
}
