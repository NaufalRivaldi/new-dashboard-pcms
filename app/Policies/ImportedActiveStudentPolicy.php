<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ImportedActiveStudent;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImportedActiveStudentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_imported::active::student');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ImportedActiveStudent $importedActiveStudent): bool
    {
        return $user->can('view_imported::active::student');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_imported::active::student');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ImportedActiveStudent $importedActiveStudent): bool
    {
        return $user->can('update_imported::active::student');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ImportedActiveStudent $importedActiveStudent): bool
    {
        return $user->can('delete_imported::active::student');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_imported::active::student');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ImportedActiveStudent $importedActiveStudent): bool
    {
        return $user->can('force_delete_imported::active::student');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_imported::active::student');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ImportedActiveStudent $importedActiveStudent): bool
    {
        return $user->can('{{ Restore }}');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('{{ RestoreAny }}');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, ImportedActiveStudent $importedActiveStudent): bool
    {
        return $user->can('replicate_imported::active::student');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_imported::active::student');
    }
}
