<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     title="User Resource",
 *     description="User resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="+90 555 123 4567"),
 *     @OA\Property(property="date_of_birth", type="string", format="date", nullable=true, example="1990-01-01"),
 *     @OA\Property(property="gender", type="string", nullable=true, enum={"male", "female", "other"}, example="male"),
 *     @OA\Property(property="avatar_url", type="string", nullable=true, example="https://example.com/storage/avatars/user123.jpg"),
 *     @OA\Property(property="is_dealer", type="boolean", example=false),
 *     @OA\Property(property="is_approved_dealer", type="boolean", example=false),
 *     @OA\Property(property="company_name", type="string", nullable=true, example="ABC Company"),
 *     @OA\Property(property="tax_number", type="string", nullable=true, example="1234567890"),
 *     @OA\Property(property="business_type", type="string", nullable=true, example="Safety Equipment Retailer"),
 *     @OA\Property(property="customer_type", type="string", enum={"b2b", "b2c", "guest", "wholesale", "retail"}, example="b2b", description="Müşteri tipi"),
 *     @OA\Property(property="customer_type_label", type="string", example="Business to Business", description="Müşteri tipi etiketi"),
 *     @OA\Property(property="pricing_tier", type="object", nullable=true,
 *         @OA\Property(property="id", type="integer"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="discount_percentage", type="number", format="float")
 *     ),
 *     @OA\Property(property="notification_preferences", type="object",
 *         @OA\Property(property="email_notifications", type="boolean", example=true),
 *         @OA\Property(property="sms_notifications", type="boolean", example=false),
 *         @OA\Property(property="marketing_emails", type="boolean", example=true)
 *     ),
 *     @OA\Property(property="email_verified_at", type="string", format="datetime", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="datetime"),
 *     @OA\Property(property="updated_at", type="string", format="datetime")
 * )
 */
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender' => $this->gender,
            'avatar_url' => $this->avatar ? Storage::disk('public')->url($this->avatar) : null,
            
            // Business/Dealer information
            'is_dealer' => $this->is_dealer,
            'is_approved_dealer' => $this->is_approved_dealer,
            'company_name' => $this->company_name,
            'tax_number' => $this->tax_number,
            'business_type' => $this->business_type,
            
            // Customer type information
            'customer_type' => $this->getCustomerType()->value,
            'customer_type_label' => $this->getCustomerType()->getLabel(),
            
            // Pricing tier information
            'pricing_tier' => $this->whenLoaded('pricingTier', function () {
                return $this->pricingTier ? [
                    'id' => $this->pricingTier->id,
                    'name' => $this->pricingTier->name,
                    'discount_percentage' => $this->pricingTier->discount_percentage,
                ] : null;
            }),
            
            // Address information
            'addresses' => $this->whenLoaded('addresses', function () {
                return AddressResource::collection($this->addresses);
            }),
            'default_shipping_address' => $this->when(
                $this->relationLoaded('addresses'),
                function () {
                    $defaultShipping = $this->addresses->where('is_default_shipping', true)->first();
                    return $defaultShipping ? new AddressResource($defaultShipping) : null;
                }
            ),
            'default_billing_address' => $this->when(
                $this->relationLoaded('addresses'),
                function () {
                    $defaultBilling = $this->addresses->where('is_default_billing', true)->first();
                    return $defaultBilling ? new AddressResource($defaultBilling) : null;
                }
            ),
            
            // Notification preferences
            'notification_preferences' => $this->notification_preferences ?? [
                'email_notifications' => true,
                'sms_notifications' => false,
                'marketing_emails' => true,
            ],
            
            // Timestamps
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}