<?php

namespace Tests\Feature\Auth;

use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareApp();
    }

    public function test_login_page_renders(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Auth/Login', false));
    }

    public function test_each_role_lands_on_its_own_home(): void
    {
        $expected = [
            [RoleName::Customer, '/dashboard'],
            [RoleName::Transporter, '/transporter'],
            [RoleName::Driver, '/driver'],
            [RoleName::SuperAdmin, '/admin'],
            [RoleName::Admin, '/admin'],
            [RoleName::SalesManager, '/admin'],
            [RoleName::WarehouseManager, '/admin'],
        ];

        foreach ($expected as [$role, $path]) {
            $user = $this->makeUser($role);

            $this->post('/login', ['login' => $user->email, 'password' => 'password'])
                ->assertRedirect($path);

            $this->assertAuthenticatedAs($user);

            $this->app['auth']->guard('web')->logout();
        }
    }

    public function test_user_can_log_in_with_mobile_number_in_any_format(): void
    {
        $user = $this->makeUser(RoleName::Customer, ['mobile' => '9876543210']);

        $this->post('/login', ['login' => '+91 98765-43210', 'password' => 'password'])
            ->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_updates_last_login_and_writes_audit_entry(): void
    {
        $user = $this->makeUser(RoleName::Customer);

        $this->post('/login', ['login' => $user->email, 'password' => 'password']);

        $this->assertNotNull($user->fresh()->last_login_at);
        $this->assertDatabaseHas('audit_logs', ['event' => 'login', 'user_id' => $user->id]);
    }

    public function test_wrong_password_is_rejected_and_audited(): void
    {
        $user = $this->makeUser(RoleName::Customer);

        $this->post('/login', ['login' => $user->email, 'password' => 'wrong'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
        $this->assertSame(1, AuditLog::where('event', 'login_failed')->count());
    }

    public function test_unknown_user_is_rejected_with_same_message(): void
    {
        $this->post('/login', ['login' => 'nobody@example.com', 'password' => 'password'])
            ->assertSessionHasErrors(['login' => 'These credentials do not match our records.']);
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = $this->makeUser(RoleName::Customer, ['status' => UserStatus::Inactive]);

        $this->post('/login', ['login' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_repeated_failures(): void
    {
        $user = $this->makeUser(RoleName::Customer);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['login' => $user->email, 'password' => 'wrong']);
        }

        // Even the correct password is refused while locked out.
        $this->post('/login', ['login' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_logout_ends_the_session(): void
    {
        $user = $this->makeUser(RoleName::Customer);

        $this->actingAs($user)->post('/logout')->assertRedirect('/');

        $this->assertGuest();
        $this->assertDatabaseHas('audit_logs', ['event' => 'logout', 'user_id' => $user->id]);
    }

    public function test_signed_in_users_are_redirected_away_from_login(): void
    {
        $user = $this->makeUser(RoleName::Customer);

        $this->actingAs($user)->get('/login')->assertRedirect('/portal');
    }

    public function test_portal_route_sends_each_role_home(): void
    {
        $customer = $this->makeUser(RoleName::Customer);
        $admin = $this->makeUser(RoleName::Admin);

        $this->actingAs($customer)->get('/portal')->assertRedirect('/dashboard');
        $this->actingAs($admin)->get('/portal')->assertRedirect('/admin');
    }
}
