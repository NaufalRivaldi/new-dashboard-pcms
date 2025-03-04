<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\ImportedNewStudent;
use App\Models\User;

class ImportedNewStudentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any ImportedNewStudent');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ImportedNewStudent $importednewstudent): bool
    {
        return $user->checkPermissionTo('view ImportedNewStudent');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create ImportedNewStudent');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ImportedNewStudent $importednewstudent): bool
    {
        return $user->checkPermissionTo('update ImportedNewStudent');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ImportedNewStudent $importednewstudent): bool
    {
        return $user->checkPermissionTo('delete ImportedNewStudent');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any ImportedNewStudent');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ImportedNewStudent $importednewstudent): bool
    {
        return $user->checkPermissionTo('restore ImportedNewStudent');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any ImportedNewStudent');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, ImportedNewStudent $importednewstudent): bool
    {
        return $user->checkPermissionTo('replicate ImportedNewStudent');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder ImportedNewStudent');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ImportedNewStudent $importednewstudent): bool
    {
        return $user->checkPermissionTo('force-delete ImportedNewStudent');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any ImportedNewStudent');
    }
}
