<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\PasswordResetMail;
use App\Mail\EmailVerificationMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="User authentication endpoints"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/auth/login",
     *      operationId="loginUser",
     *      tags={"Authentication"},
     *      summary="User login",
     *      description="Login user and get access token with rate limiting",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *              @OA\Property(property="password", type="string", example="password123"),
     *              @OA\Property(property="device_name", type="string", example="mobile_app")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Login successful",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Login successful"),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="user", type="object"),
     *                  @OA\Property(property="token", type="string", example="1|abc123def456..."),
     *                  @OA\Property(property="refresh_token", type="string", example="2|xyz789abc123..."),
     *                  @OA\Property(property="expires_at", type="string", format="date-time")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid credentials",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Invalid credentials")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Validation failed"),
     *              @OA\Property(property="errors", type="object")
     *          )
     *      ),
     *      @OA\Response(
     *          response=429,
     *          description="Too many login attempts",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Too many login attempts. Please try again later.")
     *          )
     *      )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        // Rate limiting
        $throttleKey = 'login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'message' => "Too many login attempts. Please try again in {$seconds} seconds."
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'device_name' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($throttleKey);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            RateLimiter::hit($throttleKey);
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        $deviceName = $request->input('device_name', 'api-token');
        
        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Account is deactivated. Please contact support.'
            ], 403);
        }

        // Check email verification for B2C users (only if enabled)
        if (config('auth.email_verification_enabled') && !$user->is_approved_dealer && !$user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Please verify your email address before logging in.',
                'data' => [
                    'email_verification_required' => true,
                    'email' => $user->email
                ]
            ], 403);
        }
        
        // Clear old tokens for this device
        $user->tokens()->where('name', $deviceName)->delete();
        
        // Create access token
        $accessToken = $user->createToken($deviceName, ['*'], now()->addHours(2));
        
        // Create refresh token
        $refreshToken = $user->createToken($deviceName . '_refresh', ['refresh'], now()->addDays(30));

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Clear rate limiter on successful login
        RateLimiter::clear($throttleKey);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_approved_dealer' => $user->is_approved_dealer ?? false,
                    'customer_type' => $user->is_approved_dealer ? 'B2B' : 'B2C',
                    'email_verified_at' => $user->email_verified_at,
                    'last_login_at' => $user->last_login_at
                ],
                'token' => $accessToken->plainTextToken,
                'refresh_token' => $refreshToken->plainTextToken,
                'expires_at' => $accessToken->accessToken->expires_at,
                'refresh_expires_at' => $refreshToken->accessToken->expires_at
            ]
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/register",
     *      operationId="registerUser",
     *      tags={"Authentication"},
     *      summary="User registration",
     *      description="Register a new user account",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password","password_confirmation"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *              @OA\Property(property="password", type="string", example="password123"),
     *              @OA\Property(property="password_confirmation", type="string", example="password123"),
     *              @OA\Property(property="phone", type="string", example="+905551234567"),
     *              @OA\Property(property="customer_type", type="string", enum={"B2C","B2B"}, example="B2C")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Registration successful",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Registration successful. Please check your email for verification.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Validation failed"),
     *              @OA\Property(property="errors", type="object")
     *          )
     *      )
     * )
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'customer_type' => 'nullable|in:B2C,B2B'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'is_active' => true,
            'customer_type_override' => $request->customer_type ?? 'B2C'
        ]);

        // Send email verification (only if enabled)
        if (config('auth.email_verification_enabled')) {
            event(new Registered($user));
        }

        $message = config('auth.email_verification_enabled') 
            ? 'Registration successful. Please check your email for verification.'
            : 'Registration successful. You can now login.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'user_id' => $user->id,
                'email' => $user->email
            ]
        ], 201);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/refresh",
     *      operationId="refreshToken",
     *      tags={"Authentication"},
     *      summary="Refresh access token",
     *      description="Refresh access token using refresh token",
     *      security={{ "sanctum": {} }},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"refresh_token"},
     *              @OA\Property(property="refresh_token", type="string", example="2|xyz789abc123...")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Token refreshed successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="token", type="string"),
     *                  @OA\Property(property="expires_at", type="string", format="date-time")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Invalid refresh token",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Invalid refresh token")
     *          )
     *      )
     * )
     */
    public function refresh(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find the refresh token
            $token = \Laravel\Sanctum\PersonalAccessToken::findToken($request->refresh_token);
            
            if (!$token || !$token->can('refresh')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid refresh token'
                ], 401);
            }

            $user = $token->tokenable;
            
            // Revoke old access tokens for this device
            $deviceName = $token->name;
            $user->tokens()->where('name', $deviceName)->where('id', '!=', $token->id)->delete();
            
            // Create new access token
            $newToken = $user->createToken($deviceName, ['*'], now()->addHours(2));

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $newToken->plainTextToken,
                    'expires_at' => $newToken->accessToken->expires_at
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid refresh token'
            ], 401);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/forgot-password",
     *      operationId="forgotPassword",
     *      tags={"Authentication"},
     *      summary="Send password reset email",
     *      description="Send password reset link to user's email",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Password reset email sent",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Password reset link sent to your email")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Validation failed"),
     *              @OA\Property(property="errors", type="object")
     *          )
     *      )
     * )
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to send password reset link'
        ], 400);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/reset-password",
     *      operationId="resetPassword",
     *      tags={"Authentication"},
     *      summary="Reset password",
     *      description="Reset password using token from email",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"token","email","password","password_confirmation"},
     *              @OA\Property(property="token", type="string", example="reset-token-here"),
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *              @OA\Property(property="password", type="string", example="newpassword123"),
     *              @OA\Property(property="password_confirmation", type="string", example="newpassword123")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Password reset successful",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Password reset successful")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Validation failed"),
     *              @OA\Property(property="errors", type="object")
     *          )
     *      )
     * )
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successful'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to reset password'
        ], 400);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/verify-email",
     *      operationId="verifyEmail",
     *      tags={"Authentication"},
     *      summary="Verify email address",
     *      description="Verify email address using verification token",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"token"},
     *              @OA\Property(property="token", type="string", example="verification-token-here")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Email verified successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Email verified successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Invalid verification token")
     *          )
     *      )
     * )
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Find user by verification token
        $user = User::where('email_verification_token', $request->token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification token'
            ], 422);
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully'
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/resend-verification",
     *      operationId="resendVerification",
     *      tags={"Authentication"},
     *      summary="Resend email verification",
     *      description="Resend email verification link",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Verification email sent",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Verification email sent")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false),
     *              @OA\Property(property="message", type="string", example="Validation failed"),
     *              @OA\Property(property="errors", type="object")
     *          )
     *      )
     * )
     */
    public function resendVerification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email is already verified'
            ], 422);
        }

        // Generate new verification token
        $user->update([
            'email_verification_token' => Str::random(64)
        ]);

        // Send verification email
        Mail::to($user->email)->send(new EmailVerificationMail($user));

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent'
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/logout",
     *      operationId="logoutUser",
     *      tags={"Authentication"},
     *      summary="User logout",
     *      description="Logout user and revoke access token",
     *      security={{ "sanctum": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Logout successful",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Logout successful")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *      )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/auth/user",
     *      operationId="getCurrentUser",
     *      tags={"Authentication"},
     *      summary="Get current user",
     *      description="Get authenticated user information",
     *      security={{ "sanctum": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="User information retrieved",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      )
     * )
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'is_approved_dealer' => $request->user()->is_approved_dealer ?? false,
                    'customer_type' => $request->user()->is_approved_dealer ? 'B2B' : 'B2C',
                    'email_verified_at' => $request->user()->email_verified_at,
                    'last_login_at' => $request->user()->last_login_at,
                    'created_at' => $request->user()->created_at,
                    'updated_at' => $request->user()->updated_at
                ]
            ]
        ]);
    }
}