<?php

namespace App\Policies;

use App\Models\User;
use App\Models\HybridDistribution;
use Illuminate\Auth\Access\HandlesAuthorization;

class HybridDistributionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_hybrid::distribution');
    }

    public function view(User $user, HybridDistribution $hybridDistribution): bool
    {
        return $user->can('view_hybrid::distribution');
    }

    public function create(User $user): bool
    {
        return $user->can('create_hybrid::distribution');
    }

    public function update(User $user, HybridDistribution $hybridDistribution): bool
    {
        if (!$user->can('update_hybrid::distribution')) {
            return false;
        }

        return $this->canEditRecord($user, $hybridDistribution);
    }

    public function delete(User $user, HybridDistribution $hybridDistribution): bool
    {
        if (!$user->can('delete_hybrid::distribution')) {
            return false;
        }

        return $this->canEditRecord($user, $hybridDistribution);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_hybrid::distribution');
    }

    public function forceDelete(User $user, HybridDistribution $hybridDistribution): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, HybridDistribution $hybridDistribution): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, HybridDistribution $hybridDistribution): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }

    /**
     * Editing/deleting is only allowed while the record is a draft,
     * and only by a supervisor assigned to the same field site
     * (or a superadmin, who bypasses ownership restrictions).
     */
    protected function canEditRecord(User $user, HybridDistribution $record): bool
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

        // Managers/admins can view and review, but not directly edit/delete drafts
        return false;
    }
}