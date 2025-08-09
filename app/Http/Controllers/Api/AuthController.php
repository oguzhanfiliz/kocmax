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
 *     description="Kullanıcı kimlik doğrulama API uç noktaları"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/auth/login",
     *      operationId="loginUser",
     *      tags={"Authentication"},
     *      summary="Kullanıcı girişi",
     *      description="Kullanıcı girişi yapın ve hız sınırlaması ile erişim belirteci alın",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com", description="E-posta adresi"),
     *              @OA\Property(property="password", type="string", example="password123", description="Şifre"),
     *              @OA\Property(property="device_name", type="string", example="mobile_app", description="Cihaz adı")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Giriş başarılı",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true, description="İşlem durumu"),
     *              @OA\Property(property="message", type="string", example="Giriş başarılı", description="Başarı mesajı"),
     *              @OA\Property(property="data", type="object", description="Giriş verileri",
     *                  @OA\Property(property="user", type="object", description="Kullanıcı bilgileri"),
     *                  @OA\Property(property="token", type="string", example="1|abc123def456...", description="Erişim belirteci"),
     *                  @OA\Property(property="refresh_token", type="string", example="2|xyz789abc123...", description="Yenileme belirteci"),
     *                  @OA\Property(property="expires_at", type="string", format="date-time", description="Belirteç bitiş tarihi")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Geçersiz giriş bilgileri",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false, description="İşlem durumu"),
     *              @OA\Property(property="message", type="string", example="Geçersiz giriş bilgileri", description="Hata mesajı")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Doğrulama hatası",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false, description="İşlem durumu"),
     *              @OA\Property(property="message", type="string", example="Doğrulama başarısız", description="Hata mesajı"),
     *              @OA\Property(property="errors", type="object", description="Doğrulama hataları")
     *          )
     *      ),
     *      @OA\Response(
     *          response=429,
     *          description="Çok fazla giriş denemesi",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false, description="İşlem durumu"),
     *              @OA\Property(property="message", type="string", example="Çok fazla giriş denemesi. Lütfen daha sonra tekrar deneyin.", description="Hata mesajı")
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
                'message' => "Çok fazla giriş denemesi. Lütfen {$seconds} saniye sonra tekrar deneyin."
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
                'message' => 'Doğrulama başarısız',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            RateLimiter::hit($throttleKey);
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz giriş bilgileri'
            ], 401);
        }

        $user = Auth::user();
        $deviceName = $request->input('device_name', 'api-token');
        
        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Hesap devre dışı bırakılmış. Lütfen destek ekibiyle iletişime geçin.'
            ], 403);
        }

        // Check email verification for B2C users (only if enabled)
        if (config('auth.email_verification_enabled') && !$user->is_approved_dealer && !$user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Giriş yapmadan önce lütfen e-posta adresinizi doğrulayın.',
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
            'message' => 'Giriş başarılı',
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
     *      summary="Kullanıcı kaydı",
     *      description="Yeni bir kullanıcı hesabı kaydedin",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","email","password","password_confirmation"},
     *              @OA\Property(property="name", type="string", example="John Doe", description="Ad soyad"),
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com", description="E-posta adresi"),
     *              @OA\Property(property="password", type="string", example="password123", description="Şifre"),
     *              @OA\Property(property="password_confirmation", type="string", example="password123", description="Şifre onayı"),
     *              @OA\Property(property="phone", type="string", example="+905551234567", description="Telefon numarası"),
     *              @OA\Property(property="customer_type", type="string", enum={"B2C","B2B"}, example="B2C", description="Müşteri tipi")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Kayıt başarılı",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true, description="İşlem durumu"),
     *              @OA\Property(property="message", type="string", example="Kayıt başarılı. Lütfen doğrulama için e-postanızı kontrol edin.", description="Başarı mesajı")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Doğrulama hatası",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false, description="İşlem durumu"),
     *              @OA\Property(property="message", type="string", example="Doğrulama başarısız", description="Hata mesajı"),
     *              @OA\Property(property="errors", type="object", description="Doğrulama hataları")
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
                'message' => 'Doğrulama başarısız',
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
            ? 'Kayıt başarılı. Lütfen doğrulama için e-postanızı kontrol edin.'
            : 'Kayıt başarılı. Şimdi giriş yapabilirsiniz.';

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
     *      summary="Erişim belirtecini yenile",
     *      description="Yenileme belirtecini kullanarak erişim belirtecini yenileyin",
     *      security={{ "sanctum": {} }},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"refresh_token"},
     *              @OA\Property(property="refresh_token", type="string", example="2|xyz789abc123...", description="Yenileme belirteci")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Belirteç başarıyla yenilendi",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true, description="İşlem durumu"),
     *              @OA\Property(property="data", type="object", description="Yenilenen belirteç verileri",
     *                  @OA\Property(property="token", type="string", description="Yeni erişim belirteci"),
     *                  @OA\Property(property="expires_at", type="string", format="date-time", description="Bitiş tarihi")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Geçersiz yenileme belirteci",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false, description="İşlem durumu"),
     *              @OA\Property(property="message", type="string", example="Geçersiz yenileme belirteci", description="Hata mesajı")
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
                'message' => 'Doğrulama başarısız',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find the refresh token
            $token = \Laravel\Sanctum\PersonalAccessToken::findToken($request->refresh_token);
            
            if (!$token || !$token->can('refresh')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Geçersiz yenileme belirteci'
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
                'message' => 'Geçersiz yenileme belirteci'
            ], 401);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/forgot-password",
     *      operationId="forgotPassword",
     *      tags={"Authentication"},
     *      summary="Şifre sıfırlama e-postası gönder",
     *      description="Kullanıcının e-postasına şifre sıfırlama bağlantısı gönderin",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com", description="E-posta adresi")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Şifre sıfırlama e-postası gönderildi",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true, description="İşlem durumu"),
     *              @OA\Property(property="message", type="string", example="Şifre sıfırlama bağlantısı e-postanıza gönderildi", description="Başarı mesajı")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Doğrulama hatası",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=false, description="İşlem durumu"),
     *              @OA\Property(property="message", type="string", example="Doğrulama başarısız", description="Hata mesajı"),
     *              @OA\Property(property="errors", type="object", description="Doğrulama hataları")
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
                'message' => 'Doğrulama başarısız',
                'errors' => $validator->errors()
            ], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Şifre sıfırlama bağlantısı e-postanıza gönderildi'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Şifre sıfırlama bağlantısı gönderilemedi'
        ], 400);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/reset-password",
     *      operationId="resetPassword",
     *      tags={"Authentication"},
     *      summary="Şifreyi sıfırla",
     *      description="E-postadaki belirteci kullanarak şifreyi sıfırlayın",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"token","email","password","password_confirmation"},
     *              @OA\Property(property="token", type="string", example="reset-token-here", description="Sıfırlama belirteci"),
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com", description="E-posta adresi"),
     *              @OA\Property(property="password", type="string", example="newpassword123", description="Yeni şifre"),
     *              @OA\Property(property="password_confirmation", type="string", example="newpassword123", description="Yeni şifre onayı")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Şifre sıfırlama başarılı",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true, description="İşlem durumu"),
     *              @OA\Property(property="message", type="string", example="Şifre sıfırlama başarılı", description="Başarı mesajı")
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
                'message' => 'Şifre sıfırlama başarılı'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Şifre sıfırlanamadı'
        ], 400);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/verify-email",
     *      operationId="verifyEmail",
     *      tags={"Authentication"},
     *      summary="E-posta adresini doğrula",
     *      description="Doğrulama belirtecini kullanarak e-posta adresini doğrulayın",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"token"},
     *              @OA\Property(property="token", type="string", example="verification-token-here", description="Doğrulama belirteci")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="E-posta başarıyla doğrulandı",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true, description="İşlem durumu"),
     *              @OA\Property(property="message", type="string", example="E-posta başarıyla doğrulandı", description="Başarı mesajı")
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
                'message' => 'Geçersiz doğrulama belirteci'
            ], 422);
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'E-posta başarıyla doğrulandı'
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/resend-verification",
     *      operationId="resendVerification",
     *      tags={"Authentication"},
     *      summary="E-posta doğrulamasını yeniden gönder",
     *      description="E-posta doğrulama bağlantısını yeniden gönderin",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email"},
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Doğrulama e-postası gönderildi",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true, description="İşlem durumu"),
     *              @OA\Property(property="message", type="string", example="Doğrulama e-postası gönderildi", description="Başarı mesajı")
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
                'message' => 'Kullanıcı bulunamadı'
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'E-posta zaten doğrulanmış'
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
            'message' => 'Doğrulama e-postası gönderildi'
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/auth/logout",
     *      operationId="logoutUser",
     *      tags={"Authentication"},
     *      summary="Kullanıcı çıkışı",
     *      description="Kullanıcı çıkışı yapın ve erişim belirtecini iptal edin",
     *      security={{ "sanctum": {} }},
     *      @OA\Response(
     *          response=200,
     *          description="Çıkış başarılı",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true, description="İşlem durumu"),
     *              @OA\Property(property="message", type="string", example="Çıkış başarılı", description="Başarı mesajı")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Kimlik doğrulaması yapılmamış",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Kimlik doğrulaması gerekli", description="Hata mesajı")
     *          )
     *      )
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Çıkış başarılı'
        ]);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/auth/user",
     *      operationId="getCurrentUser",
     *      tags={"Authentication"},
     *      summary="Mevcut kullanıcıyı al",
     *      description="Kimliği doğrulanmış kullanıcı bilgilerini alın",
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