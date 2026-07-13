<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PollenProduction;
use Illuminate\Auth\Access\HandlesAuthorization;

class PollenProductionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_pollen::production');
    }

    public function view(User $user, PollenProduction $pollenProduction): bool
    {
        return $user->can('view_pollen::production');
    }

    public function create(User $user): bool
    {
        return $user->can('create_pollen::production');
    }

    public function update(User $user, PollenProduction $pollenProduction): bool
    {
        if (!$user->can('update_pollen::production')) {
            return false;
        }

        return $this->canEditRecord($user, $pollenProduction);
    }

    public function delete(User $user, PollenProduction $pollenProduction): bool
    {
        if (!$user->can('delete_pollen::production')) {
            return false;
        }

        return $this->canEditRecord($user, $pollenProduction);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_pollen::production');
    }

    public function forceDelete(User $user, PollenProduction $pollenProduction): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function restore(User $user, PollenProduction $pollenProduction): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, PollenProduction $pollenProduction): bool
    {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }

    protected function canEditRecord(User $user, PollenProduction $record): bool
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