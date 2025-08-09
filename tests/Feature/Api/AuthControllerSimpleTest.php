<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerSimpleTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_input_validation_works(): void
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

    public function test_login_with_invalid_credentials_returns_401(): void
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

    public function test_login_validation_prevents_sql_injection(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => "admin@example.com' OR '1'='1",
            'password' => "anything' OR '1'='1"
        ]);

        // Should return validation error or invalid credentials, not 500
        $this->assertContains($response->status(), [401, 422]);
        
        if ($response->status() === 422) {
            $response->assertJsonStructure(['success', 'errors']);
        } else {
            $response->assertJson([
                'success' => false,
                'message' => 'Invalid credentials'
            ]);
        }
    }

    public function test_get_current_user_without_token_returns_401(): void
    {
        $response = $this->getJson('/api/v1/auth/user');
        $response->assertStatus(401);
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

    public function test_invalid_refresh_token_returns_401(): void
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

    // Test that should work with valid data - simplified
    public function test_valid_user_can_login(): void
    {
        // Create user directly in database (bypassing registration API)
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'device_name' => 'test-device'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'user',
                        'token',
                        'refresh_token'
                    ]
                ])
                ->assertJson([
                    'success' => true,
                    'message' => 'Login successful'
                ]);
    }

    public function test_user_can_logout_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        // Login first to get a token
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        $token = $loginResponse->json('data.token');

        // Now logout
        $logoutResponse = $this->withToken($token)
                              ->postJson('/api/v1/auth/logout');

        $logoutResponse->assertStatus(200)
                      ->assertJson([
                          'success' => true,
                          'message' => 'Logout successful'
                      ]);

        // Token should no longer work
        $response = $this->withToken($token)->getJson('/api/v1/auth/user');
        $response->assertStatus(401);
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
                            'customer_type'
                        ]
                    ]
                ])
                ->assertJsonPath('data.user.email', 'test@example.com');
    }
}