<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Kullanıcılar", description="Kullanıcı profili ve hesap yönetimi API uç noktaları")
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/users/profile",
     *     summary="Mevcut kullanıcı profilini al",
     *     description="Kimliği doğrulanmış kullanıcının profil bilgilerini alın",
     *     operationId="getUserProfile",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource"),
     *             @OA\Property(property="message", type="string", example="Profile retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'data' => new UserResource($user),
            'message' => 'Profile retrieved successfully'
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/users/profile",
     *     summary="Kullanıcı profilini güncelle",
     *     description="Kimliği doğrulanmış kullanıcının profil bilgilerini güncelleyin",
     *     operationId="updateUserProfile",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string", maxLength=255, example="John"),
     *             @OA\Property(property="last_name", type="string", maxLength=255, example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="phone", type="string", maxLength=20, example="+90 555 123 4567"),
     *             @OA\Property(property="date_of_birth", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="gender", type="string", enum={"male", "female", "other"}, example="male"),
     *             @OA\Property(property="company_name", type="string", maxLength=255, example="ABC Company"),
     *             @OA\Property(property="tax_number", type="string", maxLength=50, example="1234567890"),
     *             @OA\Property(property="notification_preferences", type="object",
     *                 @OA\Property(property="email_notifications", type="boolean", example=true),
     *                 @OA\Property(property="sms_notifications", type="boolean", example=false),
     *                 @OA\Property(property="marketing_emails", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/UserResource"),
     *             @OA\Property(property="message", type="string", example="Profile updated successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'date_of_birth' => ['sometimes', 'nullable', 'date'],
            'gender' => ['sometimes', 'nullable', 'in:male,female,other'],
            'company_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'tax_number' => ['sometimes', 'nullable', 'string', 'max:50'],
            'notification_preferences' => ['sometimes', 'array'],
            'notification_preferences.email_notifications' => ['sometimes', 'boolean'],
            'notification_preferences.sms_notifications' => ['sometimes', 'boolean'],
            'notification_preferences.marketing_emails' => ['sometimes', 'boolean'],
        ]);

        // Handle notification preferences JSON update
        if (isset($validated['notification_preferences'])) {
            $currentPrefs = $user->notification_preferences ?? [];
            $validated['notification_preferences'] = array_merge($currentPrefs, $validated['notification_preferences']);
        }

        $user->update($validated);

        return response()->json([
            'data' => new UserResource($user->fresh()),
            'message' => 'Profile updated successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users/change-password",
     *     summary="Kullanıcı şifresini değiştir",
     *     description="Kimliği doğrulanmış kullanıcının şifresini değiştirin",
     *     operationId="changePassword",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "password", "password_confirmation"},
     *             @OA\Property(property="current_password", type="string", format="password", example="oldpassword123"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password changed successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'The provided current password is incorrect',
                'errors' => [
                    'current_password' => ['The current password is incorrect']
                ]
            ], 422);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users/upload-avatar",
     *     summary="Kullanıcı avatarı yükle",
     *     description="Kimliği doğrulanmış kullanıcı için yeni bir profil resmi yükleyin",
     *     operationId="uploadAvatar",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="avatar",
     *                     type="string",
     *                     format="binary",
     *                     description="Avatar image file (max 2MB, formats: jpg, jpeg, png, gif)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Avatar uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="avatar_url", type="string", example="https://example.com/storage/avatars/user123.jpg")
     *             ),
     *             @OA\Property(property="message", type="string", example="Avatar uploaded successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function uploadAvatar(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'avatar' => ['required', 'image', 'max:2048'], // Max 2MB
        ]);

        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store new avatar
        $path = $validated['avatar']->store('avatars', 'public');
        
        $user->update(['avatar' => $path]);

        return response()->json([
            'data' => [
                'avatar_url' => Storage::disk('public')->url($path)
            ],
            'message' => 'Avatar uploaded successfully'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/users/avatar",
     *     summary="Kullanıcı avatarını sil",
     *     description="Kimliği doğrulanmış kullanıcının profil resmini silin",
     *     operationId="deleteAvatar",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Avatar deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Avatar deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=404, description="Avatar not found")
     * )
     */
    public function deleteAvatar(Request $request)
    {
        $user = $request->user();

        if (!$user->avatar) {
            return response()->json([
                'message' => 'No avatar found to delete'
            ], 404);
        }

        // Delete avatar file
        Storage::disk('public')->delete($user->avatar);
        
        // Update user record
        $user->update(['avatar' => null]);

        return response()->json([
            'message' => 'Avatar deleted successfully'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/dealer-status",
     *     summary="Bayi başvuru durumunu al",
     *     description="Kimliği doğrulanmış kullanıcının bayi başvurusunun mevcut durumunu alın",
     *     operationId="getDealerStatus",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Dealer status retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="is_dealer", type="boolean", example=false),
     *                 @OA\Property(property="is_approved_dealer", type="boolean", example=false),
     *                 @OA\Property(property="dealer_application_status", type="string", enum={"none", "pending", "approved", "rejected"}, example="pending"),
     *                 @OA\Property(property="dealer_application_date", type="string", format="datetime", nullable=true),
     *                 @OA\Property(property="dealer_approval_date", type="string", format="datetime", nullable=true),
     *                 @OA\Property(property="pricing_tier", type="object", nullable=true,
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="discount_percentage", type="number", format="float")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Dealer status retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function dealerStatus(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'data' => [
                'is_dealer' => $user->is_dealer,
                'is_approved_dealer' => $user->is_approved_dealer,
                'dealer_application_status' => $this->getDealerApplicationStatus($user),
                'dealer_application_date' => $user->dealer_application_date,
                'dealer_approval_date' => $user->dealer_approval_date,
                'pricing_tier' => $user->pricingTier ? [
                    'id' => $user->pricingTier->id,
                    'name' => $user->pricingTier->name,
                    'discount_percentage' => $user->pricingTier->discount_percentage
                ] : null
            ],
            'message' => 'Dealer status retrieved successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users/dealer-application",
     *     summary="Bayi başvurusunda bulunun",
     *     description="B2B bayisi olmak için başvuruda bulunun",
     *     operationId="submitDealerApplication",
     *     tags={"Users"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"company_name", "tax_number", "business_type", "annual_volume"},
     *             @OA\Property(property="company_name", type="string", maxLength=255, example="ABC Safety Equipment Ltd."),
     *             @OA\Property(property="tax_number", type="string", maxLength=50, example="1234567890"),
     *             @OA\Property(property="business_type", type="string", maxLength=100, example="Safety Equipment Retailer"),
     *             @OA\Property(property="annual_volume", type="number", format="float", example=50000.00, description="Expected annual purchase volume"),
     *             @OA\Property(property="business_address", type="string", example="123 Business St, Istanbul, Turkey"),
     *             @OA\Property(property="website", type="string", format="url", nullable=true, example="https://abcsafety.com"),
     *             @OA\Property(property="reference_customers", type="string", nullable=true, example="Customer A, Customer B"),
     *             @OA\Property(property="additional_notes", type="string", nullable=true, example="We specialize in construction safety equipment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dealer application submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Dealer application submitted successfully. We will review your application within 3-5 business days.")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError"),
     *     @OA\Response(
     *         response=409,
     *         description="Application already submitted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You have already submitted a dealer application")
     *         )
     *     )
     * )
     */
    public function submitDealerApplication(Request $request)
    {
        $user = $request->user();

        // Check if user already has a pending or approved application
        if ($user->is_dealer || $user->dealer_application_date) {
            return response()->json([
                'message' => 'You have already submitted a dealer application'
            ], 409);
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'tax_number' => ['required', 'string', 'max:50'],
            'business_type' => ['required', 'string', 'max:100'],
            'annual_volume' => ['required', 'numeric', 'min:0'],
            'business_address' => ['required', 'string'],
            'website' => ['nullable', 'url'],
            'reference_customers' => ['nullable', 'string'],
            'additional_notes' => ['nullable', 'string'],
        ]);

        // Update user with application data
        $user->update([
            'is_dealer' => true,
            'dealer_application_date' => now(),
            'company_name' => $validated['company_name'],
            'tax_number' => $validated['tax_number'],
            'business_type' => $validated['business_type'],
            'annual_volume' => $validated['annual_volume'],
            'business_address' => $validated['business_address'],
            'website' => $validated['website'] ?? null,
            'reference_customers' => $validated['reference_customers'] ?? null,
            'additional_notes' => $validated['additional_notes'] ?? null,
        ]);

        return response()->json([
            'message' => 'Dealer application submitted successfully. We will review your application within 3-5 business days.'
        ]);
    }

    /**
     * Get dealer application status based on user fields
     */
    private function getDealerApplicationStatus(User $user): string
    {
        if (!$user->is_dealer) {
            return 'none';
        }

        if ($user->is_approved_dealer) {
            return 'approved';
        }

        if ($user->dealer_application_date && !$user->is_approved_dealer) {
            // Check if explicitly rejected (you might have a rejection field)
            // For now, assume pending if application exists but not approved
            return 'pending';
        }

        return 'none';
    }
}