<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\ImportedActiveStudentEducationDetail;
use App\Models\User;

class ImportedActiveStudentEducationDetailPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any ImportedActiveStudentEducationDetail');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ImportedActiveStudentEducationDetail $importedactivestudenteducationdetail): bool
    {
        return $user->checkPermissionTo('view ImportedActiveStudentEducationDetail');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create ImportedActiveStudentEducationDetail');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ImportedActiveStudentEducationDetail $importedactivestudenteducationdetail): bool
    {
        return $user->checkPermissionTo('update ImportedActiveStudentEducationDetail');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ImportedActiveStudentEducationDetail $importedactivestudenteducationdetail): bool
    {
        return $user->checkPermissionTo('delete ImportedActiveStudentEducationDetail');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any ImportedActiveStudentEducationDetail');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ImportedActiveStudentEducationDetail $importedactivestudenteducationdetail): bool
    {
        return $user->checkPermissionTo('restore ImportedActiveStudentEducationDetail');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any ImportedActiveStudentEducationDetail');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, ImportedActiveStudentEducationDetail $importedactivestudenteducationdetail): bool
    {
        return $user->checkPermissionTo('replicate ImportedActiveStudentEducationDetail');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder ImportedActiveStudentEducationDetail');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ImportedActiveStudentEducationDetail $importedactivestudenteducationdetail): bool
    {
        return $user->checkPermissionTo('force-delete ImportedActiveStudentEducationDetail');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any ImportedActiveStudentEducationDetail');
    }
}
