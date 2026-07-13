<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Report;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_report');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Report $report): bool
    {
        return $user->can('view_report');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_report');
    }

    /**
     * Determine whether the user can update the model.
     * Supervisors can only update reports they personally generated.
     */
    public function update(User $user, Report $report): bool
    {
        if (!$user->can('update_report')) {
            return false;
        }

        return $this->ownsReport($user, $report);
    }

    /**
     * Determine whether the user can delete the model.
     * Supervisors can only delete reports they personally generated.
     */
    public function delete(User $user, Report $report): bool
    {
        if (!$user->can('delete_report')) {
            return false;
        }

        return $this->ownsReport($user, $report);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_report');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Report $report): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Report $report): bool
    {
        return $user->can('restore_report');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_report');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Report $report): bool
    {
        return false;
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return false;
    }

    /**
     * Superadmin bypasses ownership entirely.
     * Supervisors can only act on reports they personally generated.
     * Admin/manager never reach this check since they don't get
     * update/delete permissions on reports in the first place.
     */
    protected function ownsReport(User $user, Report $report): bool
    {
        if ($user->role === 'superadmin') {
            return true;
        }

        if ($user->role === 'supervisor') {
            return $user->id === $report->generated_by;
        }

        return false;
    }
}