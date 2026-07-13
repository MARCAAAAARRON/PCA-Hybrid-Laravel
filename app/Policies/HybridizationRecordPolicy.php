<?php

namespace App\Policies;

use App\Models\User;
use App\Models\HybridizationRecord;
use Illuminate\Auth\Access\HandlesAuthorization;

class HybridizationRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_hybridization::record');
    }

    public function view(User $user, HybridizationRecord $hybridizationRecord): bool
    {
        return $user->can('view_hybridization::record');
    }

    public function create(User $user): bool
    {
        return $user->can('create_hybridization::record');
    }

    public function update(User $user, HybridizationRecord $hybridizationRecord): bool
    {
        if (!$user->can('update_hybridization::record')) {
            return false;
        }

        return $this->canEditRecord($user, $hybridizationRecord);
    }

    public function delete(User $user, HybridizationRecord $hybridizationRecord): bool
    {
        if (!$user->can('delete_hybridization::record')) {
            return false;
        }

        return $this->canEditRecord($user, $hybridizationRecord);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_hybridization::record');
    }

    public function forceDelete(User $user, HybridizationRecord $hybridizationRecord): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, HybridizationRecord $hybridizationRecord): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, HybridizationRecord $hybridizationRecord): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }

    /**
     * Stricter than the other 4 field-data policies: this table has
     * an individual created_by column, so ownership is per-user,
     * not per-field-site.
     */
    protected function canEditRecord(User $user, HybridizationRecord $record): bool
    {
        if ($user->role === 'superadmin') {
            return true;
        }

        if (!$record->isDraft()) {
            return false;
        }

        if ($user->role === 'supervisor') {
            return $user->id === $record->created_by;
        }

        return false;
    }
}