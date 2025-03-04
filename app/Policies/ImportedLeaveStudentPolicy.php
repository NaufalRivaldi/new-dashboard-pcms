<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\ImportedLeaveStudent;
use App\Models\User;

class ImportedLeaveStudentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any ImportedLeaveStudent');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ImportedLeaveStudent $importedleavestudent): bool
    {
        return $user->checkPermissionTo('view ImportedLeaveStudent');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create ImportedLeaveStudent');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ImportedLeaveStudent $importedleavestudent): bool
    {
        return $user->checkPermissionTo('update ImportedLeaveStudent');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ImportedLeaveStudent $importedleavestudent): bool
    {
        return $user->checkPermissionTo('delete ImportedLeaveStudent');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any ImportedLeaveStudent');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ImportedLeaveStudent $importedleavestudent): bool
    {
        return $user->checkPermissionTo('restore ImportedLeaveStudent');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any ImportedLeaveStudent');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, ImportedLeaveStudent $importedleavestudent): bool
    {
        return $user->checkPermissionTo('replicate ImportedLeaveStudent');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder ImportedLeaveStudent');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ImportedLeaveStudent $importedleavestudent): bool
    {
        return $user->checkPermissionTo('force-delete ImportedLeaveStudent');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any ImportedLeaveStudent');
    }
}
