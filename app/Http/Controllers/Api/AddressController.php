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
 * @OA\Tag(name="Addresses", description="Kullanıcı adres yönetimi API uç noktaları")
 */
class AddressController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/addresses",
     *     summary="Adresleri listele",
     *     description="Kimliği doğrulanmış kullanıcının tüm adreslerini getirir",
     *     operationId="getAddresses",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Adres tipi filtresi (teslimat, fatura veya her ikisi)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"shipping", "billing", "both"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Adresler başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AddressResource")),
     *             @OA\Property(property="message", type="string", example="Adresler başarıyla getirildi")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $query = $request->user()->addresses();

        // Filter by category if provided (shipping, billing, both)
        if ($request->filled('category')) {
            $category = $request->string('category');
            if ($category === 'shipping') {
                $query->shipping();
            } elseif ($category === 'billing') {
                $query->billing();
            } elseif ($category === 'both') {
                $query->where('category', 'both');
            }
        }
        
        // Filter by type if provided (home, work, billing, other)
        if ($request->filled('type')) {
            $type = $request->string('type');
            $query->byType($type);
        }

        $addresses = $query->orderBy('is_default_shipping', 'desc')
                          ->orderBy('is_default_billing', 'desc')
                          ->orderBy('created_at', 'desc')
                          ->get();

        return response()->json([
            'success' => true,
            'data' => AddressResource::collection($addresses),
            'message' => 'Adresler başarıyla getirildi'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/addresses",
     *     summary="Adres oluştur",
     *     description="Kimliği doğrulanmış kullanıcı için yeni bir adres oluşturur",
     *     operationId="createAddress",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Adres bilgileri",
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "address_line_1", "city", "postal_code", "country"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Ev", description="Adres başlığı"),
     *             @OA\Property(property="first_name", type="string", maxLength=255, example="Ahmet", description="Ad"),
     *             @OA\Property(property="last_name", type="string", maxLength=255, example="Yılmaz", description="Soyad"),
     *             @OA\Property(property="company_name", type="string", maxLength=255, nullable=true, example="ABC Şirket", description="Şirket adı"),
     *             @OA\Property(property="phone", type="string", maxLength=20, nullable=true, example="+90 555 123 4567", description="Telefon numarası"),
     *             @OA\Property(property="address_line_1", type="string", example="Atatürk Caddesi No:123", description="Adres satırı 1"),
     *             @OA\Property(property="address_line_2", type="string", nullable=true, example="Daire 4B", description="Adres satırı 2"),
     *             @OA\Property(property="city", type="string", maxLength=255, example="İstanbul", description="Şehir"),
     *             @OA\Property(property="state", type="string", maxLength=255, nullable=true, example="İstanbul", description="İl/Eyalet"),
     *             @OA\Property(property="postal_code", type="string", maxLength=20, example="34000", description="Posta kodu"),
     *             @OA\Property(property="country", type="string", maxLength=2, example="TR", description="Ülke"),
     *             @OA\Property(property="type", type="string", enum={"shipping", "billing", "both"}, example="both", description="Adres türü"),
     *             @OA\Property(property="is_default_shipping", type="boolean", example=false, description="Varsayılan teslimat adresi mi"),
     *             @OA\Property(property="is_default_billing", type="boolean", example=false, description="Varsayılan fatura adresi mi"),
     *             @OA\Property(property="notes", type="string", nullable=true, example="Zili çalın", description="Notlar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Adres başarıyla oluşturuldu",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/AddressResource"),
     *             @OA\Property(property="message", type="string", example="Adres başarıyla oluşturuldu")
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
            'type' => ['required', Rule::in(['home', 'work', 'billing', 'other'])],
            'category' => ['required', Rule::in(['shipping', 'billing', 'both'])],
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
            'success' => true,
            'data' => new AddressResource($address),
            'message' => 'Adres başarıyla oluşturuldu'
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/addresses/{id}",
     *     summary="Adres detaylarını getir",
     *     description="Belirtilen ID'ye göre adres detaylarını getirir",
     *     operationId="getAddress",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Adres benzersiz kimliği",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Adres başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/AddressResource"),
     *             @OA\Property(property="message", type="string", example="Adres başarıyla getirildi")
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
                'success' => false,
                'message' => 'Adres bulunamadı'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new AddressResource($address),
            'message' => 'Adres başarıyla getirildi'
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/addresses/{id}",
     *     summary="Adresi güncelle",
     *     description="Belirli bir adresi günceller",
     *     operationId="updateAddress",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Adres benzersiz kimliği",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Güncellenecek adres bilgileri",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", maxLength=255, example="Ev", description="Adres başlığı"),
     *             @OA\Property(property="first_name", type="string", maxLength=255, example="Ahmet", description="Ad"),
     *             @OA\Property(property="last_name", type="string", maxLength=255, example="Yılmaz", description="Soyad"),
     *             @OA\Property(property="company_name", type="string", maxLength=255, nullable=true, example="ABC Şirket", description="Şirket adı"),
     *             @OA\Property(property="phone", type="string", maxLength=20, nullable=true, example="+90 555 123 4567", description="Telefon numarası"),
     *             @OA\Property(property="address_line_1", type="string", example="Atatürk Caddesi No:123", description="Adres satırı 1"),
     *             @OA\Property(property="address_line_2", type="string", nullable=true, example="Daire 4B", description="Adres satırı 2"),
     *             @OA\Property(property="city", type="string", maxLength=255, example="İstanbul", description="Şehir"),
     *             @OA\Property(property="state", type="string", maxLength=255, nullable=true, example="İstanbul", description="İl/Eyalet"),
     *             @OA\Property(property="postal_code", type="string", maxLength=20, example="34000", description="Posta kodu"),
     *             @OA\Property(property="country", type="string", maxLength=2, example="TR", description="Ülke"),
     *             @OA\Property(property="type", type="string", enum={"shipping", "billing", "both"}, example="both", description="Adres türü"),
     *             @OA\Property(property="is_default_shipping", type="boolean", example=false, description="Varsayılan teslimat adresi mi"),
     *             @OA\Property(property="is_default_billing", type="boolean", example=false, description="Varsayılan fatura adresi mi"),
     *             @OA\Property(property="notes", type="string", nullable=true, example="Zili çalın", description="Notlar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Adres başarıyla güncellendi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/AddressResource"),
     *             @OA\Property(property="message", type="string", example="Adres başarıyla güncellendi")
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
                'success' => false,
                'message' => 'Adres bulunamadı'
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
            'type' => ['sometimes', 'required', Rule::in(['home', 'work', 'billing', 'other'])],
            'category' => ['sometimes', 'required', Rule::in(['shipping', 'billing', 'both'])],
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
            'success' => true,
            'data' => new AddressResource($address->fresh()),
            'message' => 'Adres başarıyla güncellendi'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/addresses/{id}",
     *     summary="Adresi sil",
     *     description="Belirli bir adresi siler (geçici silme)",
     *     operationId="deleteAddress",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Adres benzersiz kimliği",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Adres başarıyla silindi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Adres başarıyla silindi")
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
                'success' => false,
                'message' => 'Adres bulunamadı'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Adres başarıyla silindi'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/addresses/{id}/set-default-shipping",
     *     summary="Varsayılan teslimat adresini ayarla",
     *     description="Belirtilen adresi varsayılan teslimat adresi olarak ayarlar",
     *     operationId="setDefaultShipping",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Adres benzersiz kimliği",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Varsayılan teslimat adresi başarıyla ayarlandı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/AddressResource"),
     *             @OA\Property(property="message", type="string", example="Varsayılan teslimat adresi başarıyla ayarlandı")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(
     *         response=422,
     *         description="Adres teslimat için kullanılamaz",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Bu adres teslimat için kullanılamaz")
     *         )
     *     )
     * )
     */
    public function setDefaultShipping(Request $request, Address $address)
    {
        // Ensure user can only modify their own addresses
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Adres bulunamadı'
            ], 404);
        }

        // Check if address can be used for shipping
        if (!in_array($address->category, ['shipping', 'both'])) {
            return response()->json([
                'success' => false,
                'message' => 'Bu adres teslimat için kullanılamaz'
            ], 422);
        }

        $address->setAsDefaultShipping();

        return response()->json([
            'success' => true,
            'data' => new AddressResource($address->fresh()),
            'message' => 'Varsayılan teslimat adresi başarıyla ayarlandı'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/addresses/{id}/set-default-billing",
     *     summary="Varsayılan fatura adresini ayarla",
     *     description="Belirtilen adresi varsayılan fatura adresi olarak ayarlar",
     *     operationId="setDefaultBilling",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Adres benzersiz kimliği",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Varsayılan fatura adresi başarıyla ayarlandı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/AddressResource"),
     *             @OA\Property(property="message", type="string", example="Varsayılan fatura adresi başarıyla ayarlandı")
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthenticated"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(
     *         response=422,
     *         description="Adres fatura için kullanılamaz",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Bu adres fatura için kullanılamaz")
     *         )
     *     )
     * )
     */
    public function setDefaultBilling(Request $request, Address $address)
    {
        // Ensure user can only modify their own addresses
        if ($address->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Adres bulunamadı'
            ], 404);
        }

        // Check if address can be used for billing
        if (!in_array($address->category, ['billing', 'both'])) {
            return response()->json([
                'success' => false,
                'message' => 'Bu adres fatura için kullanılamaz'
            ], 422);
        }

        $address->setAsDefaultBilling();

        return response()->json([
            'success' => true,
            'data' => new AddressResource($address->fresh()),
            'message' => 'Varsayılan fatura adresi başarıyla ayarlandı'
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/addresses/defaults",
     *     summary="Varsayılan adresleri getir",
     *     description="Kullanıcının varsayılan teslimat ve fatura adreslerini getirir",
     *     operationId="getDefaultAddresses",
     *     tags={"Addresses"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Varsayılan adresler başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object", description="Varsayılan adresler",
     *                 @OA\Property(property="shipping", ref="#/components/schemas/AddressResource", nullable=true, description="Varsayılan teslimat adresi"),
     *                 @OA\Property(property="billing", ref="#/components/schemas/AddressResource", nullable=true, description="Varsayılan fatura adresi")
     *             ),
     *             @OA\Property(property="message", type="string", example="Varsayılan adresler başarıyla getirildi")
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
            'success' => true,
            'data' => [
                'shipping' => $defaultShipping ? new AddressResource($defaultShipping) : null,
                'billing' => $defaultBilling ? new AddressResource($defaultBilling) : null,
            ],
            'message' => 'Varsayılan adresler başarıyla getirildi'
        ]);
    }
}