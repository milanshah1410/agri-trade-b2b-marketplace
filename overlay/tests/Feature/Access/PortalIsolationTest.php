<?php

namespace Tests\Feature\Access;

use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class PortalIsolationTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareApp();
    }

    /**
     * Each portal URL is open to exactly one role.
     *
     * @return array<int, array{0: string, 1: RoleName, 2: string}>
     */
    private function portals(): array
    {
        return [
            ['/dashboard', RoleName::Customer, 'Customer/Dashboard'],
            ['/transporter', RoleName::Transporter, 'Transporter/Dashboard'],
            ['/driver', RoleName::Driver, 'Driver/Dashboard'],
        ];
    }

    public function test_guests_are_sent_to_login(): void
    {
        foreach ($this->portals() as [$url]) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_only_the_matching_role_can_open_each_portal(): void
    {
        foreach ($this->portals() as [$url, $allowedRole, $component]) {
            foreach (RoleName::cases() as $role) {
                $user = $this->makeUser($role);

                $response = $this->actingAs($user)->get($url);

                if ($role === $allowedRole) {
                    $response->assertOk()->assertInertia(fn (Assert $page) => $page->component($component, false));
                } else {
                    $this->assertSame(
                        403,
                        $response->getStatusCode(),
                        "{$role->value} must be refused at {$url}",
                    );
                }
            }
        }
    }

    public function test_user_without_role_is_refused_everywhere(): void
    {
        $user = User::factory()->create();

        foreach ($this->portals() as [$url]) {
            $this->actingAs($user)->get($url)->assertForbidden();
        }
    }

    public function test_inactive_user_is_logged_out_and_redirected(): void
    {
        $user = $this->makeUser(RoleName::Customer, ['status' => UserStatus::Inactive]);

        $this->actingAs($user)->get('/dashboard')->assertRedirect('/login');

        $this->assertGuest();
    }
}
