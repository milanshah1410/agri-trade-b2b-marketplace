<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Creates the first Super Admin from SUPER_ADMIN_* settings. There is no default password:
     * if the settings are missing or weak, nothing is created.
     */
    public function run(): void
    {
        $email = config('agri.super_admin.email');
        $password = config('agri.super_admin.password');

        if (! $email || ! $password) {
            $this->command?->warn('SUPER_ADMIN_EMAIL / SUPER_ADMIN_PASSWORD not set; no Super Admin created.');

            return;
        }

        if (strlen($password) < 12) {
            $this->command?->error('SUPER_ADMIN_PASSWORD must be at least 12 characters; no Super Admin created.');

            return;
        }

        $user = User::firstOrCreate(
            ['email' => strtolower($email)],
            [
                'name' => config('agri.super_admin.name', 'Super Admin'),
                'password' => $password,
                'status' => UserStatus::Active,
            ],
        );

        $user->forceFill(['email_verified_at' => $user->email_verified_at ?? now()])->save();
        $user->assignRole(RoleName::SuperAdmin->value);
    }
}
