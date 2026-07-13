<?php

namespace App\Policies;

use App\Models\User;
use App\Models\NurseryOperation;
use Illuminate\Auth\Access\HandlesAuthorization;

class NurseryOperationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_nursery::operation');
    }

    public function view(User $user, NurseryOperation $nurseryOperation): bool
    {
        return $user->can('view_nursery::operation');
    }

    public function create(User $user): bool
    {
        return $user->can('create_nursery::operation');
    }

    public function update(User $user, NurseryOperation $nurseryOperation): bool
    {
        if (!$user->can('update_nursery::operation')) {
            return false;
        }

        return $this->canEditRecord($user, $nurseryOperation);
    }

    public function delete(User $user, NurseryOperation $nurseryOperation): bool
    {
        if (!$user->can('delete_nursery::operation')) {
            return false;
        }

        return $this->canEditRecord($user, $nurseryOperation);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_nursery::operation');
    }

    public function forceDelete(User $user, NurseryOperation $nurseryOperation): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, NurseryOperation $nurseryOperation): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, NurseryOperation $nurseryOperation): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }

    protected function canEditRecord(User $user, NurseryOperation $record): bool
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