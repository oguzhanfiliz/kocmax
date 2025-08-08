<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Addresses", description="User address management")
 */
class AddressController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/addresses",
     *     summary="Get user addresses",
     *     description="Retrieve all addresses for the authenticated user",
     *     operationId="getAddresses",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter addresses by type",
     *         required=false,
     *         @OA\Schema(type="string", enum={"shipping", "billing", "both"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Addresses retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AddressResource")),
     *             @OA\Property(property="message", type="string", example="Addresses retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $query = $request->user()->addresses();

        // Filter by type if provided
        if ($request->filled('type')) {
            $type = $request->string('type');
            if ($type === 'shipping') {
                $query->shipping();
            } elseif ($type === 'billing') {
                $query->billing();
            } elseif ($type === 'both') {
                $query->where('type', 'both');
            }
        }

        $addresses = $query->orderBy('is_default_shipping', 'desc')
                          ->orderBy('is_default_billing', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->get();

        return response()->json([
            'data' => AddressResource::collection($addresses),
            'message' => 'Addresses retrieved successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/addresses",
     *     summary="Create new address",
     *     description="Create a new address for the authenticated user",
     *     operationId="createAddress",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "address_line_1", "city", "postal_code", "country"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Home"),
     *             @OA\Property(property="first_name", type="string", maxLength=255, example="John"),
     *             @OA\Property(property="last_name", type="string", maxLength=255, example="Doe"),
     *             @OA\Property(property="company_name", type="string", maxLength=255, nullable=true, example="ABC Company"),
     *             @OA\Property(property="phone", type="string", maxLength=20, nullable=true, example="+90 555 123 4567"),
     *             @OA\Property(property="address_line_1", type="string", example="123 Main Street"),
     *             @OA\Property(property="address_line_2", type="string", nullable=true, example="Apartment 4B"),
     *             @OA\Property(property="city", type="string", maxLength=255, example="Istanbul"),
     *             @OA\Property(property="state", type="string", maxLength=255, nullable=true, example="Istanbul"),
     *             @OA\Property(property="postal_code", type="string", maxLength=20, example="34000"),
     *             @OA\Property(property="country", type="string", maxLength=2, example="TR"),
     *             @OA\Property(property="type", type="string", enum={"shipping", "billing", "both"}, example="both"),
     *             @OA\Property(property="is_default_shipping", type="boolean", example=false),
     *             @OA\Property(property="is_default_billing", type="boolean", example=false),
     *             @OA\Property(property="notes", type="string", nullable=true, example="Ring the doorbell")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Address created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/AddressResource"),
     *             @OA\Property(property="message", type="string", example="Address created successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address_line_1' => ['required', 'string'],
            'address_line_2' => ['nullable', 'string'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:20'],
            'country' => ['required', 'string', 'size:2'],
            'type' => ['required', Rule::in(['shipping', 'billing', 'both'])],
            'is_default_shipping' => ['boolean'],
            'is_default_billing' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = $request->user()->id;

        $address = Address::create($validated);

        // Handle default address logic
        if ($validated['is_default_shipping'] ?? false) {
            $address->setAsDefaultShipping();
        }

        if ($validated['is_default_billing'] ?? false) {
            $address->setAsDefaultBilling();
        }

        return response()->json([
            'data' => new AddressResource($address),
            'message' => 'Address created successfully'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/addresses/{id}",
     *     summary="Get specific address",
     *     description="Retrieve a specific address by ID",
     *     operationId="getAddress",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Address ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/AddressResource"),
     *             @OA\Property(property="message", type="string", example="Address retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(Request $request, Address $address)
    {
        // Ensure user can only access their own addresses
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Address not found'
            ], 404);
        }

        return response()->json([
            'data' => new AddressResource($address),
            'message' => 'Address retrieved successfully'
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/addresses/{id}",
     *     summary="Update address",
     *     description="Update a specific address",
     *     operationId="updateAddress",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Address ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", maxLength=255, example="Home"),
     *             @OA\Property(property="first_name", type="string", maxLength=255, example="John"),
     *             @OA\Property(property="last_name", type="string", maxLength=255, example="Doe"),
     *             @OA\Property(property="company_name", type="string", maxLength=255, nullable=true, example="ABC Company"),
     *             @OA\Property(property="phone", type="string", maxLength=20, nullable=true, example="+90 555 123 4567"),
     *             @OA\Property(property="address_line_1", type="string", example="123 Main Street"),
     *             @OA\Property(property="address_line_2", type="string", nullable=true, example="Apartment 4B"),
     *             @OA\Property(property="city", type="string", maxLength=255, example="Istanbul"),
     *             @OA\Property(property="state", type="string", maxLength=255, nullable=true, example="Istanbul"),
     *             @OA\Property(property="postal_code", type="string", maxLength=20, example="34000"),
     *             @OA\Property(property="country", type="string", maxLength=2, example="TR"),
     *             @OA\Property(property="type", type="string", enum={"shipping", "billing", "both"}, example="both"),
     *             @OA\Property(property="is_default_shipping", type="boolean", example=false),
     *             @OA\Property(property="is_default_billing", type="boolean", example=false),
     *             @OA\Property(property="notes", type="string", nullable=true, example="Ring the doorbell")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/AddressResource"),
     *             @OA\Property(property="message", type="string", example="Address updated successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(Request $request, Address $address)
    {
        // Ensure user can only update their own addresses
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Address not found'
            ], 404);
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'company_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'address_line_1' => ['sometimes', 'required', 'string'],
            'address_line_2' => ['sometimes', 'nullable', 'string'],
            'city' => ['sometimes', 'required', 'string', 'max:255'],
            'state' => ['sometimes', 'nullable', 'string', 'max:255'],
            'postal_code' => ['sometimes', 'required', 'string', 'max:20'],
            'country' => ['sometimes', 'required', 'string', 'size:2'],
            'type' => ['sometimes', 'required', Rule::in(['shipping', 'billing', 'both'])],
            'is_default_shipping' => ['boolean'],
            'is_default_billing' => ['boolean'],
            'notes' => ['sometimes', 'nullable', 'string'],
        ]);

        $address->update($validated);

        // Handle default address logic
        if (isset($validated['is_default_shipping']) && $validated['is_default_shipping']) {
            $address->setAsDefaultShipping();
        } elseif (isset($validated['is_default_shipping']) && !$validated['is_default_shipping']) {
            $address->update(['is_default_shipping' => false]);
        }

        if (isset($validated['is_default_billing']) && $validated['is_default_billing']) {
            $address->setAsDefaultBilling();
        } elseif (isset($validated['is_default_billing']) && !$validated['is_default_billing']) {
            $address->update(['is_default_billing' => false]);
        }

        return response()->json([
            'data' => new AddressResource($address->fresh()),
            'message' => 'Address updated successfully'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/addresses/{id}",
     *     summary="Delete address",
     *     description="Delete a specific address (soft delete)",
     *     operationId="deleteAddress",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Address ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Address deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Address deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(Request $request, Address $address)
    {
        // Ensure user can only delete their own addresses
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Address not found'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'message' => 'Address deleted successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/addresses/{id}/set-default-shipping",
     *     summary="Set as default shipping address",
     *     description="Set the specified address as the default shipping address",
     *     operationId="setDefaultShipping",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Address ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Default shipping address set successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/AddressResource"),
     *             @OA\Property(property="message", type="string", example="Default shipping address set successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(
     *         response=422,
     *         description="Address cannot be used for shipping",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="This address cannot be used for shipping")
     *         )
     *     )
     * )
     */
    public function setDefaultShipping(Request $request, Address $address)
    {
        // Ensure user can only modify their own addresses
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Address not found'
            ], 404);
        }

        // Check if address can be used for shipping
        if (!in_array($address->type, ['shipping', 'both'])) {
            return response()->json([
                'message' => 'This address cannot be used for shipping'
            ], 422);
        }

        $address->setAsDefaultShipping();

        return response()->json([
            'data' => new AddressResource($address->fresh()),
            'message' => 'Default shipping address set successfully'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/addresses/{id}/set-default-billing",
     *     summary="Set as default billing address",
     *     description="Set the specified address as the default billing address",
     *     operationId="setDefaultBilling",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Address ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Default billing address set successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/AddressResource"),
     *             @OA\Property(property="message", type="string", example="Default billing address set successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(
     *         response=422,
     *         description="Address cannot be used for billing",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="This address cannot be used for billing")
     *         )
     *     )
     * )
     */
    public function setDefaultBilling(Request $request, Address $address)
    {
        // Ensure user can only modify their own addresses
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Address not found'
            ], 404);
        }

        // Check if address can be used for billing
        if (!in_array($address->type, ['billing', 'both'])) {
            return response()->json([
                'message' => 'This address cannot be used for billing'
            ], 422);
        }

        $address->setAsDefaultBilling();

        return response()->json([
            'data' => new AddressResource($address->fresh()),
            'message' => 'Default billing address set successfully'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/addresses/defaults",
     *     summary="Get default addresses",
     *     description="Get the user's default shipping and billing addresses",
     *     operationId="getDefaultAddresses",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Default addresses retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="shipping", ref="#/components/schemas/AddressResource", nullable=true),
     *                 @OA\Property(property="billing", ref="#/components/schemas/AddressResource", nullable=true)
     *             ),
     *             @OA\Property(property="message", type="string", example="Default addresses retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function getDefaults(Request $request)
    {
        $user = $request->user();
        $defaultShipping = $user->defaultShippingAddress();
        $defaultBilling = $user->defaultBillingAddress();

        return response()->json([
            'data' => [
                'shipping' => $defaultShipping ? new AddressResource($defaultShipping) : null,
                'billing' => $defaultBilling ? new AddressResource($defaultBilling) : null,
            ],
            'message' => 'Default addresses retrieved successfully'
        ]);
    }
}