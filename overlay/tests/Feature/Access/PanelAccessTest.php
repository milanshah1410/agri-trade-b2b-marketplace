<?php

namespace Tests\Feature\Access;

use App\Enums\RoleName;
use App\Enums\UserStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareApp();
    }

    public function test_guests_are_sent_to_the_admin_login(): void
    {
        $this->get('/admin')->assertRedirectContains('/admin/login');
    }

    public function test_staff_roles_can_open_the_admin_panel(): void
    {
        foreach (RoleName::staff() as $role) {
            $user = $this->makeUser($role);

            $status = $this->actingAs($user)->get('/admin')->getStatusCode();

            $this->assertSame(200, $status, "{$role->value} should reach /admin");
        }
    }

    public function test_customers_transporters_and_drivers_can_never_open_the_admin_panel(): void
    {
        foreach ([RoleName::Customer, RoleName::Transporter, RoleName::Driver] as $role) {
            $user = $this->makeUser($role);

            $status = $this->actingAs($user)->get('/admin')->getStatusCode();

            $this->assertSame(403, $status, "{$role->value} must be refused at /admin");
        }
    }

    public function test_user_without_any_role_is_refused(): void
    {
        $user = \App\Models\User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_inactive_staff_are_refused(): void
    {
        $user = $this->makeUser(RoleName::Admin, ['status' => UserStatus::Inactive]);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }
}
