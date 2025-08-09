<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear rate limiter for clean test state  
        RateLimiter::clear('login:127.0.0.1');
        
        // Set email verification to false for testing
        config(['auth.email_verification_enabled' => false]);
    }

    // ========================== REGISTRATION TESTS ==========================

    public function test_valid_b2c_user_registration(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'phone' => '+905551234567',
            'customer_type' => 'B2C'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);
        
        // Debug response if error
        if ($response->status() !== 201) {
            dump($response->json());
            dump($response->status());
        }

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => ['user_id', 'email']
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Registration successful. You can now login.',
                    'data' => ['email' => 'john@example.com']
                ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
    }

    public function test_valid_b2b_user_registration(): void
    {
        $userData = [
            'name' => 'Business User',
            'email' => 'business@example.com',
            'password' => 'SecurePassword123!',
            'password_confirmation' => 'SecurePassword123!',
            'phone' => '+905559876543',
            'customer_type' => 'B2B'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(201)
                ->assertJson(['success' => true]);

        $user = User::where('email', 'business@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('B2B', $user->customer_type_override);
    }

    public function test_duplicate_email_prevention(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => ['email']
                ])
                ->assertJson(['success' => false]);
    }

    public function test_password_confirmation_validation(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password'
        ];

        $response = $this->postJson('/api/v1/auth/register', $userData);

        $response->assertStatus(422)
                ->assertJsonPath('errors.password', function ($errors) {
                    return in_array('The password field confirmation does not match.', $errors);
                });
    }

    public function test_registration_input_validation(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => '', // Required field empty
            'email' => 'invalid-email', // Invalid email format
            'password' => '123', // Too short password
        ]);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => ['name', 'email', 'password']
                ]);
    }

    // ========================== LOGIN TESTS ==========================

    public function test_valid_credentials_authentication(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
            'device_name' => 'test-device'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user' => ['id', 'name', 'email', 'is_approved_dealer', 'customer_type'],
                        'token',
                        'refresh_token',
                        'expires_at',
                        'refresh_expires_at'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Login successful'
                ]);

        // Verify tokens were created
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'test-device'
        ]);
    }

    public function test_invalid_credentials_rejection(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('correct_password')
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'wrong_password'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ]);
    }

    public function test_nonexistent_user_login(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ]);
    }

    public function test_rate_limiting_enforcement(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('correct_password')
        ]);

        // Make 5 failed login attempts (rate limit)
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => 'user@example.com',
                'password' => 'wrong_password'
            ])->assertStatus(401);
        }

        // 6th attempt should be rate limited
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'correct_password'
        ]);

        $response->assertStatus(429)
                ->assertJsonPath('success', false)
                ->assertJsonPath('message', function ($message) {
                    return str_contains($message, 'Too many login attempts');
                });
    }

    public function test_email_verification_requirement_when_enabled(): void
    {
        // Enable email verification
        config(['auth.email_verification_enabled' => true]);
        
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => null, // Not verified
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(403)
                ->assertJson([
                    'success' => false,
                    'message' => 'Please verify your email address before logging in.',
                    'data' => [
                        'email_verification_required' => true,
                        'email' => 'user@example.com'
                    ]
                ]);
    }

    public function test_device_specific_token_management(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123')
        ]);

        // Login from first device
        $response1 = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
            'device_name' => 'mobile-app'
        ]);

        $token1 = $response1->json('data.token');

        // Login from second device with same device name (should replace token)
        $response2 = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
            'device_name' => 'mobile-app'
        ]);

        $token2 = $response2->json('data.token');

        $this->assertNotEquals($token1, $token2);

        // Old token should no longer work
        $response = $this->withToken($token1)->getJson('/api/v1/auth/user');
        $response->assertStatus(401);

        // New token should work
        $response = $this->withToken($token2)->getJson('/api/v1/auth/user');
        $response->assertStatus(200);
    }

    // ========================== TOKEN MANAGEMENT TESTS ==========================

    public function test_access_token_refresh_workflow(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123')
        ]);

        // Login to get tokens
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'password123',
            'device_name' => 'test-device'
        ]);

        $refreshToken = $loginResponse->json('data.refresh_token');

        // Use refresh token to get new access token
        $refreshResponse = $this->postJson('/api/v1/auth/refresh', [
            'refresh_token' => $refreshToken
        ]);

        $refreshResponse->assertStatus(200)
                       ->assertJsonStructure([
                           'success',
                           'data' => ['token', 'expires_at']
                       ])
                       ->assertJson(['success' => true]);

        $newToken = $refreshResponse->json('data.token');
        $this->assertNotNull($newToken);

        // New token should work for authenticated requests
        $response = $this->withToken($newToken)->getJson('/api/v1/auth/user');
        $response->assertStatus(200);
    }

    public function test_invalid_refresh_token_scenario(): void
    {
        $response = $this->postJson('/api/v1/auth/refresh', [
            'refresh_token' => 'invalid_refresh_token'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid refresh token'
                ]);
    }

    public function test_token_revocation_on_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123')
        ]);

        // Login to get token
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'password123'
        ]);

        $token = $loginResponse->json('data.token');

        // Logout
        $logoutResponse = $this->withToken($token)->postJson('/api/v1/auth/logout');
        $logoutResponse->assertStatus(200)
                      ->assertJson([
                          'success' => true,
                          'message' => 'Logout successful'
                      ]);

        // Token should no longer work
        $response = $this->withToken($token)->getJson('/api/v1/auth/user');
        $response->assertStatus(401);
    }

    // ========================== PASSWORD MANAGEMENT TESTS ==========================

    public function test_password_reset_email_sending(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'user@example.com'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Password reset link sent to your email'
                ]);
    }

    public function test_password_reset_with_invalid_email(): void
    {
        $response = $this->postJson('/api/v1/auth/forgot-password', [
            'email' => 'nonexistent@example.com'
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'success' => false,
                    'message' => 'Unable to send password reset link'
                ]);
    }

    public function test_token_based_password_reset(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('old_password')
        ]);

        // Create a password reset token manually for testing
        $token = Str::random(64);
        \DB::table('password_reset_tokens')->insert([
            'email' => 'user@example.com',
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        $response = $this->postJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'email' => 'user@example.com',
            'password' => 'new_secure_password',
            'password_confirmation' => 'new_secure_password'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Password reset successful'
                ]);

        // Verify password was changed
        $user->refresh();
        $this->assertTrue(Hash::check('new_secure_password', $user->password));
    }

    // ========================== EMAIL VERIFICATION TESTS ==========================

    public function test_email_verification_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'email_verified_at' => null,
            'email_verification_token' => 'valid_token_123'
        ]);

        $response = $this->postJson('/api/v1/auth/verify-email', [
            'token' => 'valid_token_123'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Email verified successfully'
                ]);

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->email_verification_token);
    }

    public function test_email_verification_with_invalid_token(): void
    {
        $response = $this->postJson('/api/v1/auth/verify-email', [
            'token' => 'invalid_token'
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid verification token'
                ]);
    }

    public function test_resend_verification_email(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'email_verified_at' => null
        ]);

        $response = $this->postJson('/api/v1/auth/resend-verification', [
            'email' => 'user@example.com'
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Verification email sent'
                ]);

        // Verify token was generated
        $user->refresh();
        $this->assertNotNull($user->email_verification_token);
    }

    public function test_resend_verification_for_already_verified_email(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'email_verified_at' => now()
        ]);

        $response = $this->postJson('/api/v1/auth/resend-verification', [
            'email' => 'user@example.com'
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'success' => false,
                    'message' => 'Email is already verified'
                ]);
    }

    // ========================== SECURITY TESTS ==========================

    public function test_login_validation_prevents_sql_injection(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => "admin@example.com' OR '1'='1",
            'password' => "anything' OR '1'='1"
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'success' => false,
                    'message' => 'Invalid credentials'
                ]);
    }

    public function test_registration_sanitizes_xss_attempts(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => '<script>alert("xss")</script>',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        if ($response->status() === 201) {
            $user = User::where('email', 'test@example.com')->first();
            // Verify that the name was properly escaped/sanitized
            $this->assertStringNotContainsString('<script>', $user->name);
        } else {
            // If validation failed, that's also acceptable
            $response->assertStatus(422);
        }
    }

    public function test_get_current_user_with_valid_token(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);

        $token = $user->createToken('test-device')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/v1/auth/user');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'user' => [
                            'id', 'name', 'email', 'is_approved_dealer', 
                            'customer_type', 'email_verified_at', 'created_at', 'updated_at'
                        ]
                    ]
                ])
                ->assertJsonPath('data.user.email', 'test@example.com');
    }

    public function test_get_current_user_without_token(): void
    {
        $response = $this->getJson('/api/v1/auth/user');
        $response->assertStatus(401);
    }

    public function test_login_updates_last_login_timestamp(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
            'last_login_at' => null
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'user@example.com',
            'password' => 'password123'
        ]);

        $user->refresh();
        $this->assertNotNull($user->last_login_at);
        $this->assertTrue($user->last_login_at->isToday());
    }
}