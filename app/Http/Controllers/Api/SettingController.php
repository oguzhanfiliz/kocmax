<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Settings", description="Site ayarları API uç noktaları")
 */
class SettingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/settings",
     *     summary="Genel site ayarlarını al",
     *     description="Frontend için gerekli tüm genel ayarları getirir (sadece herkese açık ayarlar)",
     *     operationId="getSettings",
     *     tags={"Settings"},
     *     @OA\Parameter(
     *         name="group",
     *         in="query",
     *         description="Belirli bir grup ayarlarını filtrele",
     *         required=false,
     *         @OA\Schema(type="string", enum={"general", "contact", "company", "social", "ui", "notification"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ayarlar başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="site_title", type="string", example="B2B/B2C E-Ticaret"),
     *                 @OA\Property(property="site_description", type="string", example="İş güvenliği ürünleri"),
     *                 @OA\Property(property="contact_phone", type="string", example="+90 555 123 4567"),
     *                 @OA\Property(property="contact_email", type="string", example="info@site.com"),
     *                 @OA\Property(property="company_name", type="string", example="ABC Şirketi")
     *             ),
     *             @OA\Property(property="message", type="string", example="Ayarlar başarıyla getirildi")
     *         )
     *     ),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function index(Request $request)
    {
        try {
            $query = Setting::public(); // Only public settings

            // Filter by group if provided
            if ($request->filled('group')) {
                $group = $request->string('group');
                $query->where('group', $group);
            }

            $settings = $query->get();

            // Transform to key-value pairs
            $data = $settings->mapWithKeys(function (Setting $setting) {
                $value = $setting->type === 'image' ? $this->getFullImageUrl($setting->value) : $setting->value;
                return [$setting->key => $value];
            })->toArray();

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Ayarlar başarıyla getirildi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ayarlar getirilirken bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/settings/grouped",
     *     summary="Gruplara göre ayarları al",
     *     description="Ayarları gruplara göre organize edilmiş şekilde getirir",
     *     operationId="getGroupedSettings",
     *     tags={"Settings"},
     *     @OA\Response(
     *         response=200,
     *         description="Gruplu ayarlar başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="general", type="object",
     *                     @OA\Property(property="site_title", type="string", example="B2B/B2C E-Ticaret"),
     *                     @OA\Property(property="site_description", type="string", example="İş güvenliği ürünleri")
     *                 ),
     *                 @OA\Property(property="contact", type="object",
     *                     @OA\Property(property="contact_phone", type="string", example="+90 555 123 4567"),
     *                     @OA\Property(property="contact_email", type="string", example="info@site.com")
     *                 ),
     *                 @OA\Property(property="company", type="object",
     *                     @OA\Property(property="company_name", type="string", example="ABC Şirketi")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Gruplu ayarlar başarıyla getirildi")
     *         )
     *     ),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function grouped()
    {
        try {
            $settings = Setting::public()->get();

            // Group by category
            $grouped = $settings->groupBy('group')->map(function ($groupSettings) {
                return $groupSettings->mapWithKeys(function (Setting $setting) {
                    $value = $setting->type === 'image' ? $this->getFullImageUrl($setting->value) : $setting->value;
                    return [$setting->key => $value];
                });
            });

            return response()->json([
                'success' => true,
                'data' => $grouped,
                'message' => 'Gruplu ayarlar başarıyla getirildi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gruplu ayarlar getirilirken bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/settings/{key}",
     *     summary="Belirli bir ayarı al",
     *     description="Verilen anahtara göre belirli bir ayar değerini getirir",
     *     operationId="getSetting",
     *     tags={"Settings"},
     *     @OA\Parameter(
     *         name="key",
     *         in="path",
     *         description="Ayar anahtarı",
     *         required=true,
     *         @OA\Schema(type="string", example="site_title")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Ayar başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="key", type="string", example="site_title"),
     *                 @OA\Property(property="value", type="string", example="B2B/B2C E-Ticaret"),
     *                 @OA\Property(property="group", type="string", example="general")
     *             ),
     *             @OA\Property(property="message", type="string", example="Ayar başarıyla getirildi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ayar bulunamadı",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Ayar bulunamadı")
     *         )
     *     ),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function show(string $key)
    {
        try {
            $setting = Setting::public()->where('key', $key)->first();

            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ayar bulunamadı'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'key' => $setting->key,
                    'value' => $setting->type === 'image' ? $this->getFullImageUrl($setting->value) : $setting->value,
                    'group' => $setting->group,
                    'label' => $setting->label,
                    'description' => $setting->description,
                ],
                'message' => 'Ayar başarıyla getirildi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ayar getirilirken bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/settings/essential",
     *     summary="Temel site bilgilerini al",
     *     description="Frontend için kritik olan temel site bilgilerini hızlı şekilde getirir",
     *     operationId="getEssentialSettings",
     *     tags={"Settings"},
     *     @OA\Response(
     *         response=200,
     *         description="Temel ayarlar başarıyla getirildi",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="site", type="object",
     *                     @OA\Property(property="title", type="string", example="B2B/B2C E-Ticaret"),
     *                     @OA\Property(property="description", type="string", example="İş güvenliği ürünleri"),
     *                     @OA\Property(property="logo", type="string", example="/images/logo.png")
     *                 ),
     *                 @OA\Property(property="contact", type="object",
     *                     @OA\Property(property="phone", type="string", example="+90 555 123 4567"),
     *                     @OA\Property(property="email", type="string", example="info@site.com"),
     *                     @OA\Property(property="address", type="string", example="İstanbul, Türkiye")
     *                 ),
     *                 @OA\Property(property="company", type="object",
     *                     @OA\Property(property="name", type="string", example="ABC Şirketi"),
     *                     @OA\Property(property="tax_number", type="string", example="1234567890")
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Temel ayarlar başarıyla getirildi")
     *         )
     *     ),
     *     @OA\Response(response=500, ref="#/components/responses/ServerError")
     * )
     */
    public function essential()
    {
        try {
            // Define essential setting keys
            $essentialKeys = [
                // Site bilgileri
                'site_title', 'site_description', 'site_logo', 'site_favicon',
                // İletişim bilgileri  
                'contact_phone', 'contact_email', 'contact_address',
                // Şirket bilgileri
                'company_name', 'company_tax_number',
                // Sosyal medya
                'social_facebook', 'social_twitter', 'social_instagram', 'social_linkedin',
                // UI ayarları
                'theme_color', 'enable_dark_mode'
            ];

            $settings = Setting::public()
                ->whereIn('key', $essentialKeys)
                ->get()
                ->mapWithKeys(function (Setting $setting) {
                    return [$setting->key => $setting->value];
                });

            // Organize into logical groups
            $organized = [
                'site' => [
                    'title' => $settings['site_title'] ?? 'E-Ticaret',
                    'description' => $settings['site_description'] ?? '',
                    'logo' => $this->getFullImageUrl($settings['site_logo'] ?? null),
                    'favicon' => $this->getFullImageUrl($settings['site_favicon'] ?? null),
                ],
                'contact' => [
                    'phone' => $settings['contact_phone'] ?? null,
                    'email' => $settings['contact_email'] ?? null,
                    'address' => $settings['contact_address'] ?? null,
                ],
                'company' => [
                    'name' => $settings['company_name'] ?? null,
                    'tax_number' => $settings['company_tax_number'] ?? null,
                ],
                'social' => [
                    'facebook' => $settings['social_facebook'] ?? null,
                    'twitter' => $settings['social_twitter'] ?? null,
                    'instagram' => $settings['social_instagram'] ?? null,
                    'linkedin' => $settings['social_linkedin'] ?? null,
                ],
                'ui' => [
                    'theme_color' => $settings['theme_color'] ?? '#3b82f6',
                    'dark_mode' => $settings['enable_dark_mode'] ?? false,
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $organized,
                'message' => 'Temel ayarlar başarıyla getirildi'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Temel ayarlar getirilirken bir hata oluştu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get full URL for image settings
     */
    private function getFullImageUrl(?string $imagePath): ?string
    {
        if (!$imagePath) {
            return null;
        }

        // Eğer zaten tam URL ise olduğu gibi döndür
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // Storage path ise tam URL oluştur
        if (str_starts_with($imagePath, 'storage/') || str_starts_with($imagePath, '/storage/')) {
            return url($imagePath);
        }

        // Public path ise tam URL oluştur
        if (str_starts_with($imagePath, '/')) {
            return url($imagePath);
        }

        // Eğer sadece dosya adıysa (FileUpload'dan geliyorsa) settings/images altında
        if (!str_contains($imagePath, '/')) {
            return url('storage/settings/images/' . $imagePath);
        }

        // Diğer relatif path'ler için storage
        return url('storage/' . $imagePath);
    }
}