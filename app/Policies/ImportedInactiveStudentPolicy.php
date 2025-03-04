<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\ImportedInactiveStudent;
use App\Models\User;

class ImportedInactiveStudentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any ImportedInactiveStudent');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ImportedInactiveStudent $importedinactivestudent): bool
    {
        return $user->checkPermissionTo('view ImportedInactiveStudent');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create ImportedInactiveStudent');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ImportedInactiveStudent $importedinactivestudent): bool
    {
        return $user->checkPermissionTo('update ImportedInactiveStudent');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ImportedInactiveStudent $importedinactivestudent): bool
    {
        return $user->checkPermissionTo('delete ImportedInactiveStudent');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any ImportedInactiveStudent');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ImportedInactiveStudent $importedinactivestudent): bool
    {
        return $user->checkPermissionTo('restore ImportedInactiveStudent');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any ImportedInactiveStudent');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, ImportedInactiveStudent $importedinactivestudent): bool
    {
        return $user->checkPermissionTo('replicate ImportedInactiveStudent');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder ImportedInactiveStudent');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ImportedInactiveStudent $importedinactivestudent): bool
    {
        return $user->checkPermissionTo('force-delete ImportedInactiveStudent');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any ImportedInactiveStudent');
    }
}
