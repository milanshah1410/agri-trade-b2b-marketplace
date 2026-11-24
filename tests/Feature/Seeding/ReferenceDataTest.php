<?php

namespace Tests\Feature\Seeding;

use App\Enums\RoleName;
use App\Models\State;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\StateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReferenceDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_seven_roles_are_seeded(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        foreach (RoleName::cases() as $role) {
            $this->assertTrue(Role::where('name', $role->value)->exists(), $role->value);
        }

        $this->assertSame(7, Role::count());
    }

    public function test_seeding_twice_changes_nothing(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $roles = Role::count();
        $permissions = \Spatie\Permission\Models\Permission::count();

        $this->seed(RolesAndPermissionsSeeder::class);

        $this->assertSame($roles, Role::count());
        $this->assertSame($permissions, \Spatie\Permission\Models\Permission::count());
    }

    public function test_states_and_union_territories_are_seeded_once(): void
    {
        $this->seed(StateSeeder::class);
        $this->seed(StateSeeder::class);

        $this->assertSame(36, State::count());
        $this->assertSame(28, State::where('type', 'state')->count());
        $this->assertSame(8, State::where('type', 'ut')->count());
        $this->assertSame('24', State::where('code', 'GJ')->firstOrFail()->gst_code);
        $this->assertSame(36, State::distinct()->count('gst_code'));
    }

    public function test_super_admin_is_created_from_settings(): void
    {
        config([
            'agri.super_admin.email' => 'Owner@Example.com',
            'agri.super_admin.password' => 'a-long-enough-pass-123',
        ]);

        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'owner@example.com')->firstOrFail();

        $this->assertTrue($admin->hasRole(RoleName::SuperAdmin->value));
    }

    public function test_no_super_admin_without_settings_or_with_weak_password(): void
    {
        config(['agri.super_admin.email' => null, 'agri.super_admin.password' => null]);
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(0, User::count());

        config(['agri.super_admin.email' => 'owner@example.com', 'agri.super_admin.password' => 'short']);
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(0, User::count());
    }
}
