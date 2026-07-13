<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MonthlyHarvest;
use Illuminate\Auth\Access\HandlesAuthorization;

class MonthlyHarvestPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_monthly::harvest');
    }

    public function view(User $user, MonthlyHarvest $monthlyHarvest): bool
    {
        return $user->can('view_monthly::harvest');
    }

    public function create(User $user): bool
    {
        return $user->can('create_monthly::harvest');
    }

    public function update(User $user, MonthlyHarvest $monthlyHarvest): bool
    {
        if (!$user->can('update_monthly::harvest')) {
            return false;
        }

        return $this->canEditRecord($user, $monthlyHarvest);
    }

    public function delete(User $user, MonthlyHarvest $monthlyHarvest): bool
    {
        if (!$user->can('delete_monthly::harvest')) {
            return false;
        }

        return $this->canEditRecord($user, $monthlyHarvest);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_monthly::harvest');
    }

    public function forceDelete(User $user, MonthlyHarvest $monthlyHarvest): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, MonthlyHarvest $monthlyHarvest): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, MonthlyHarvest $monthlyHarvest): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }

    protected function canEditRecord(User $user, MonthlyHarvest $record): bool
    {
        if ($user->role === 'superadmin') {
            return true;
        }

        if (!$record->isDraft()) {
            return false;
        }

        if ($user->role === 'supervisor') {
            return $user->field_site_id === $record->field_site_id;
        }

        return false;
    }
}