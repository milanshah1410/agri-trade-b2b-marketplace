<?php

namespace Tests\Concerns;

use App\Enums\RoleName;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

trait CreatesUsers
{
    /**
     * Roles must exist before users can be assigned to them, and the Vite manifest is
     * not required for feature tests.
     */
    protected function prepareApp(): void
    {
        $this->withoutVite();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function makeUser(RoleName $role, array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole($role->value);

        return $user;
    }
}
