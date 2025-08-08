<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="AddressResource",
 *     title="Address Resource",
 *     description="Address resource representation",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", nullable=true, example="Home"),
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Doe"),
 *     @OA\Property(property="full_name", type="string", example="John Doe"),
 *     @OA\Property(property="company_name", type="string", nullable=true, example="ABC Company"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="+90 555 123 4567"),
 *     @OA\Property(property="address_line_1", type="string", example="123 Main Street"),
 *     @OA\Property(property="address_line_2", type="string", nullable=true, example="Apartment 4B"),
 *     @OA\Property(property="city", type="string", example="Istanbul"),
 *     @OA\Property(property="state", type="string", nullable=true, example="Istanbul"),
 *     @OA\Property(property="postal_code", type="string", example="34000"),
 *     @OA\Property(property="country", type="string", example="TR"),
 *     @OA\Property(property="is_default_shipping", type="boolean", example=true),
 *     @OA\Property(property="is_default_billing", type="boolean", example=false),
 *     @OA\Property(property="type", type="string", enum={"shipping", "billing", "both"}, example="both"),
 *     @OA\Property(property="notes", type="string", nullable=true, example="Ring the doorbell"),
 *     @OA\Property(property="formatted_address", type="string", example="123 Main Street, Apartment 4B, Istanbul, Istanbul 34000, TR"),
 *     @OA\Property(property="created_at", type="string", format="datetime"),
 *     @OA\Property(property="updated_at", type="string", format="datetime")
 * )
 */
class AddressResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'company_name' => $this->company_name,
            'phone' => $this->phone,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'is_default_shipping' => $this->is_default_shipping,
            'is_default_billing' => $this->is_default_billing,
            'type' => $this->type,
            'notes' => $this->notes,
            'formatted_address' => $this->formatted_address,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}