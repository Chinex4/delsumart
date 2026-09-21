<?php

namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use App\Notifications\MfaCodeNotification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthenticationAndCsrfRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::create([
            'name' => 'Demo Student',
            'matric_no' => 'DELSU/TEST/001',
            'email' => 'demo@example.test',
            'programme' => 'Computing',
            'level' => '200',
            'password' => 'Password123!',
            'account_status' => 'active',
        ]);
    }

    public function test_registration_preserves_fields_and_redirects_to_verification(): void
    {
        $this->post('/register', [
            'name' => 'New Student',
            'matric_no' => 'DELSU/NEW/001',
            'email' => 'new@example.test',
            'programme' => 'Computing',
            'level' => '200',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertRedirect('/verification');
        $this->assertAuthenticated();
        $this->get('/verification')
            ->assertOk()
            ->assertSee('Upload your documents');
    }

    public function test_password_login_requires_correct_one_time_code(): void
    {
        Notification::fake();
        $user = $this->user();
        $this->post('/login', [
            'login' => $user->email,
            'password' => 'Password123!',
        ])->assertRedirect('/mfa');
        $this->assertGuest();
        $notification = Notification::sent(
            $user,
            MfaCodeNotification::class,
        )->first();
        $this->post('/mfa', ['code' => 'wrong'])->assertSessionHasErrors(
            'code',
        );
        $this->assertGuest();
        $code = collect($notification->toMail($user)->introLines)->first(
            fn ($line) => preg_match('/^\d{6}$/', $line),
        );
        $this->post('/mfa', ['code' => $code])->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_admin_can_disable_email_otp_and_login_skips_mfa(): void
    {
        Notification::fake();
        $admin = User::create([
            'name' => 'Admin User',
            'matric_no' => 'ADMIN/001',
            'email' => 'admin@example.test',
            'programme' => 'Administration',
            'level' => 'Staff',
            'password' => 'Password123!',
            'role' => 'admin',
            'account_status' => 'active',
        ]);

        $this->actingAs($admin)->patch('/admin/system/settings', [
            'email_login_otp' => '0',
        ])->assertRedirect();

        $this->post('/logout');
        $this->post('/login', [
            'login' => $admin->email,
            'password' => 'Password123!',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin);
        Notification::assertNothingSent();
        $this->assertFalse(SystemSetting::boolean('email_login_otp', true));
    }

    public function test_non_admin_cannot_change_system_settings_or_clear_caches(): void
    {
        $user = $this->user();

        $this->actingAs($user)
            ->patch('/admin/system/settings', ['email_login_otp' => '0'])
            ->assertForbidden();

        $this->actingAs($user)
            ->post('/admin/system/clear-caches')
            ->assertForbidden();
    }

    public function test_password_reset_uses_existing_reset_broker(): void
    {
        Notification::fake();
        $user = $this->user();
        $this->post('/forgot-password', [
            'email' => $user->email,
        ])->assertSessionHas('success');
        $reset = Notification::sent($user, ResetPassword::class)->first();
        $this->assertNotNull($reset);
        $this->post('/reset-password', [
            'email' => $user->email,
            'token' => $reset->token,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertRedirect('/login');
        $this->assertTrue(
            Hash::check('NewPassword123!', $user->fresh()->password),
        );
    }

    public function test_signed_webhook_is_not_blocked_by_browser_csrf_but_forms_are_protected(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        config(['services.paystack.secret_key' => 'test-secret']);
        $payload = json_encode([
            'event' => 'charge.success',
            'data' => ['reference' => 'DM-CSRF'],
        ]);
        $this->call(
            'POST',
            '/paystack/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_PAYSTACK_SIGNATURE' => hash_hmac(
                    'sha512',
                    $payload,
                    'test-secret',
                ),
            ],
            $payload,
        )->assertOk();
        $this->post('/register', [])->assertStatus(419);
    }
}
