<?php

namespace Tests\Unit;

use App\Enums\RoleName;
use App\Support\PermissionCatalog;
use App\Support\Phone;
use PHPUnit\Framework\TestCase;

class PermissionCatalogTest extends TestCase
{
    public function test_every_role_has_a_permission_list(): void
    {
        $map = PermissionCatalog::rolePermissions();

        foreach (RoleName::cases() as $role) {
            $this->assertArrayHasKey($role->value, $map);
            $this->assertNotEmpty($map[$role->value], $role->value);
        }
    }

    public function test_role_permissions_only_reference_defined_permissions(): void
    {
        $all = PermissionCatalog::all();

        foreach (PermissionCatalog::rolePermissions() as $role => $permissions) {
            $unknown = array_diff($permissions, $all);

            $this->assertSame([], array_values($unknown), "{$role} references unknown permissions");
        }
    }

    public function test_permission_names_are_unique(): void
    {
        $all = PermissionCatalog::all();

        $this->assertSame(count($all), count(array_unique($all)));
    }

    public function test_audit_logs_are_read_only(): void
    {
        $all = PermissionCatalog::all();

        $this->assertContains('audit_logs.view', $all);
        $this->assertNotContains('audit_logs.update', $all);
        $this->assertNotContains('audit_logs.delete', $all);
    }

    public function test_external_roles_have_no_admin_module_permissions(): void
    {
        $map = PermissionCatalog::rolePermissions();

        foreach ([RoleName::Customer, RoleName::Transporter, RoleName::Driver] as $role) {
            foreach ($map[$role->value] as $permission) {
                $module = explode('.', $permission)[0];

                $this->assertNotContains(
                    $permission,
                    ['users.view', 'orders.view', 'audit_logs.view', 'settings.view', 'reports.view'],
                    "{$role->value} must not hold {$permission}",
                );
                $this->assertNotSame('roles', $module);
            }
        }
    }

    public function test_phone_numbers_are_normalised(): void
    {
        $this->assertSame('9876543210', Phone::normalize('9876543210'));
        $this->assertSame('9876543210', Phone::normalize('+91 98765-43210'));
        $this->assertSame('9876543210', Phone::normalize('098765 43210'));
        $this->assertSame('', Phone::normalize(null));
    }
}
