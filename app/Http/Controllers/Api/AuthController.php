<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
     *      description="Login user and get access token",
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
     *      )
     * )
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'device_name' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();
        $deviceName = $request->input('device_name', 'api-token');
        
        // Eski token'ları temizle (isteğe bağlı)
        $user->tokens()->where('name', $deviceName)->delete();
        
        // Yeni token oluştur
        $token = $user->createToken($deviceName);

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_approved_dealer' => $user->is_approved_dealer ?? false,
                    'customer_type' => $user->is_approved_dealer ? 'B2B' : 'B2C'
                ],
                'token' => $token->plainTextToken,
                'expires_at' => $token->accessToken->expires_at
            ]
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
                    'created_at' => $request->user()->created_at,
                    'updated_at' => $request->user()->updated_at
                ]
            ]
        ]);
    }
}