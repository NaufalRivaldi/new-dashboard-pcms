<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Summary;
use App\Models\User;

class SummaryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any Summary');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Summary $summary): bool
    {
        return $user->checkPermissionTo('view Summary');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create Summary');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Summary $summary): bool
    {
        return $user->checkPermissionTo('update Summary');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Summary $summary): bool
    {
        return $user->checkPermissionTo('delete Summary');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any Summary');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Summary $summary): bool
    {
        return $user->checkPermissionTo('restore Summary');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any Summary');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, Summary $summary): bool
    {
        return $user->checkPermissionTo('replicate Summary');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder Summary');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Summary $summary): bool
    {
        return $user->checkPermissionTo('force-delete Summary');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any Summary');
    }
}
