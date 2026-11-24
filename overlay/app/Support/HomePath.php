<?php

namespace App\Support;

use App\Enums\RoleName;
use App\Models\User;

class HomePath
{
    /**
     * Where a signed-in user belongs, based on role.
     */
    public static function for(?User $user): string
    {
        if (! $user) {
            return '/login';
        }

        return match (true) {
            $user->hasAnyRole(RoleName::staffValues()) => '/admin',
            $user->hasRole(RoleName::Transporter->value) => '/transporter',
            $user->hasRole(RoleName::Driver->value) => '/driver',
            $user->hasRole(RoleName::Customer->value) => '/dashboard',
            default => '/',
        };
    }
}
