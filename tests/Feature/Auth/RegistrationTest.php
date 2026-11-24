<?php

namespace Tests\Feature\Auth;

use App\Enums\RoleName;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareApp();
    }

    /**
     * @return array<string, string>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Ramesh Patel',
            'company_name' => 'Patel Agro Traders',
            'email' => 'Ramesh@Example.com',
            'mobile' => '+91 98765 43210',
            'gstin' => '24abcde1234f1z5',
            'password' => 'Str0ngPassword!',
            'password_confirmation' => 'Str0ngPassword!',
        ], $overrides);
    }

    public function test_registration_page_renders(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Auth/Register', false));
    }

    public function test_visitor_can_register_as_customer(): void
    {
        $response = $this->post('/register', $this->validPayload());

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'ramesh@example.com')->firstOrFail();

        $this->assertSame('9876543210', $user->mobile);
        $this->assertTrue($user->hasRole(RoleName::Customer->value));
        $this->assertCount(1, $user->getRoleNames());
        $this->assertSame('Patel Agro Traders', $user->customerProfile->company_name);
        $this->assertSame('24ABCDE1234F1Z5', $user->customerProfile->gstin);
        $this->assertNotSame('Str0ngPassword!', $user->password);
    }

    public function test_gstin_is_optional(): void
    {
        $this->post('/register', $this->validPayload(['gstin' => '']))->assertRedirect('/dashboard');

        $this->assertNull(User::where('email', 'ramesh@example.com')->firstOrFail()->customerProfile->gstin);
    }

    public function test_client_cannot_choose_role_or_status(): void
    {
        $this->post('/register', $this->validPayload([
            'role' => RoleName::SuperAdmin->value,
            'roles' => [RoleName::SuperAdmin->value],
            'status' => UserStatus::Inactive->value,
            'permissions' => ['users.delete'],
        ]))->assertRedirect('/dashboard');

        $user = User::where('email', 'ramesh@example.com')->firstOrFail();

        $this->assertSame([RoleName::Customer->value], $user->getRoleNames()->all());
        $this->assertSame(UserStatus::Active, $user->status);
        $this->assertFalse($user->can('users.delete'));
    }

    public function test_registration_is_validated(): void
    {
        $this->post('/register', $this->validPayload([
            'password' => 'short',
            'password_confirmation' => 'different',
            'mobile' => '12345',
            'gstin' => 'NOT-A-GSTIN',
            'email' => 'not-an-email',
            'company_name' => '',
        ]))->assertSessionHasErrors(['password', 'mobile', 'gstin', 'email', 'company_name']);

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
    }

    public function test_duplicate_email_and_mobile_are_rejected(): void
    {
        $this->makeUser(RoleName::Customer, ['email' => 'ramesh@example.com', 'mobile' => '9876543210']);

        $this->post('/register', $this->validPayload())->assertSessionHasErrors(['email', 'mobile']);
    }

    public function test_signed_in_users_are_redirected_away_from_register(): void
    {
        $user = $this->makeUser(RoleName::Customer);

        $this->actingAs($user)->get('/register')->assertRedirect('/portal');
    }
}
