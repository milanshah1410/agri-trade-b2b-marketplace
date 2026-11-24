<?php

namespace Tests\Feature\Audit;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_user_is_logged_without_secrets(): void
    {
        $user = User::factory()->create(['name' => 'Asha']);

        $log = AuditLog::where('event', 'created')->where('auditable_id', $user->id)->firstOrFail();

        $this->assertSame(User::class, $log->auditable_type);
        $this->assertSame('Asha', $log->new_values['name']);
        $this->assertSame('[redacted]', $log->new_values['password']);
        $this->assertArrayNotHasKey('remember_token', $log->new_values);
    }

    public function test_updates_store_old_and_new_values(): void
    {
        $user = User::factory()->create(['name' => 'Old Name']);

        $user->update(['name' => 'New Name']);

        $log = AuditLog::where('event', 'updated')->where('auditable_id', $user->id)->firstOrFail();

        $this->assertSame(['name' => 'Old Name'], $log->old_values);
        $this->assertSame(['name' => 'New Name'], $log->new_values);
    }

    public function test_password_changes_are_logged_but_redacted(): void
    {
        $user = User::factory()->create();

        $user->update(['password' => 'AnotherStr0ngPass!']);

        $log = AuditLog::where('event', 'updated')->where('auditable_id', $user->id)->firstOrFail();

        $this->assertSame('[redacted]', $log->new_values['password']);
    }

    public function test_last_login_updates_alone_do_not_create_noise(): void
    {
        $user = User::factory()->create();

        $user->forceFill(['last_login_at' => now()])->save();

        $this->assertSame(0, AuditLog::where('event', 'updated')->count());
    }

    public function test_deleting_a_user_is_logged(): void
    {
        $user = User::factory()->create();

        $user->delete();

        $this->assertTrue(AuditLog::where('event', 'deleted')->where('auditable_id', $user->id)->exists());
    }

    public function test_audit_entries_cannot_be_changed_or_deleted(): void
    {
        $log = AuditLog::record('custom_event');

        $this->assertFalse($log->update(['event' => 'tampered']));
        $this->assertFalse($log->delete());
        $this->assertSame('custom_event', $log->fresh()->event);
    }
}
