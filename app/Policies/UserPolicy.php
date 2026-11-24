<?php

namespace App\Policies;

use App\Enums\RoleName;
use App\Models\User;

/**
 * Super Admins bypass this via Gate::before (see AppServiceProvider).
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('users.view');
    }

    public function view(User $user, User $model): bool
    {
        return $user->can('users.view');
    }

    public function create(User $user): bool
    {
        return $user->can('users.create');
    }

    public function update(User $user, User $model): bool
    {
        return $user->can('users.update') && $this->mayManage($user, $model);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->can('users.delete') && $user->isNot($model) && $this->mayManage($user, $model);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('users.delete');
    }

    public function restore(User $user, User $model): bool
    {
        return $user->can('users.delete') && $this->mayManage($user, $model);
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('users.delete');
    }

    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    /**
     * Only a Super Admin may touch a Super Admin account.
     */
    private function mayManage(User $actor, User $target): bool
    {
        return ! $target->hasRole(RoleName::SuperAdmin->value)
            || $actor->hasRole(RoleName::SuperAdmin->value);
    }
}
