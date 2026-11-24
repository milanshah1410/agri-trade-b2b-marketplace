<?php

namespace Tests\Feature\Access;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareApp();
    }

    public function test_super_admin_can_do_everything(): void
    {
        $super = $this->makeUser(RoleName::SuperAdmin);
        $other = $this->makeUser(RoleName::Admin);

        $this->assertTrue($super->can('viewAny', User::class));
        $this->assertTrue($super->can('update', $other));
        $this->assertTrue($super->can('delete', $other));
        $this->assertTrue($super->can('roles.create'));
    }

    public function test_admin_can_manage_users_but_not_roles(): void
    {
        $admin = $this->makeUser(RoleName::Admin);
        $customer = $this->makeUser(RoleName::Customer);

        $this->assertTrue($admin->can('viewAny', User::class));
        $this->assertTrue($admin->can('create', User::class));
        $this->assertTrue($admin->can('update', $customer));
        $this->assertFalse($admin->can('roles.create'));
        $this->assertFalse($admin->can('roles.update'));
    }

    public function test_admin_cannot_touch_a_super_admin(): void
    {
        $admin = $this->makeUser(RoleName::Admin);
        $super = $this->makeUser(RoleName::SuperAdmin);

        $this->assertFalse($admin->can('update', $super));
        $this->assertFalse($admin->can('delete', $super));
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->makeUser(RoleName::Admin);
        $admin->givePermissionTo('users.delete');

        $this->assertFalse($admin->can('delete', $admin));
    }

    public function test_admin_cannot_see_transporter_bank_details_by_default(): void
    {
        $admin = $this->makeUser(RoleName::Admin);
        $super = $this->makeUser(RoleName::SuperAdmin);

        $this->assertFalse($admin->can('transporters.view_bank_details'));
        $this->assertTrue($super->can('transporters.view_bank_details'));
    }

    public function test_sales_and_warehouse_managers_have_limited_scope(): void
    {
        $sales = $this->makeUser(RoleName::SalesManager);
        $warehouse = $this->makeUser(RoleName::WarehouseManager);

        $this->assertFalse($sales->can('viewAny', User::class));
        $this->assertTrue($sales->can('pricing.update'));
        $this->assertFalse($sales->can('inventory.adjust'));

        $this->assertFalse($warehouse->can('viewAny', User::class));
        $this->assertTrue($warehouse->can('inventory.adjust'));
        $this->assertFalse($warehouse->can('pricing.update'));
    }

    public function test_customers_transporters_and_drivers_have_no_admin_permissions(): void
    {
        foreach ([RoleName::Customer, RoleName::Transporter, RoleName::Driver] as $role) {
            $user = $this->makeUser($role);

            $this->assertFalse($user->can('viewAny', User::class), $role->value);
            $this->assertFalse($user->can('orders.view'), $role->value);
            $this->assertFalse($user->can('audit_logs.view'), $role->value);
        }
    }
}
