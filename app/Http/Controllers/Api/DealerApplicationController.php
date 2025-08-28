<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDealerApplicationRequest;
use App\Models\User;
use App\Models\DealerApplication;
use App\Services\DealerApplication\DealerApplicationService;
use App\Enums\DealerApplicationWorkflow;
use App\Enums\DealerApplicationStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DealerApplicationController extends Controller
{
    public function __construct(
        private DealerApplicationService $dealerApplicationService
    ) {
        $this->middleware('auth:sanctum')->except(['store']);
        // $this->middleware('throttle:dealer-applications')->only(['store']); // Geçici olarak kapatıldı
    }

    /**
     * @OA\Get(
     *     path="/api/v1/dealer-applications",
     *     summary="Kullanıcının bayi başvuru durumunu getir",
     *     description="Kimliği doğrulanmış kullanıcının bayi başvurusunun mevcut durumunu döndürür.",
     *     operationId="getDealerApplicationsIndex",
     *     tags={"Dealer Applications"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Başvuru durumu başarıyla alındı",
     *         @OA\JsonContent(
     *             @OA\Property(property="has_application", type="boolean", example=true),
     *             @OA\Property(property="can_apply", type="boolean", example=false),
     *             @OA\Property(property="is_dealer", type="boolean", example=false),
     *             @OA\Property(property="dealer_code", type="string", nullable=true, example="BAYI-ABC-2025-0456"),
     *             @OA\Property(property="application", type="object", nullable=true,
     *                 @OA\Property(property="id", type="integer", example=456),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="status_label", type="string", example="Beklemede"),
     *                 @OA\Property(property="status_color", type="string", example="warning"),
     *                 @OA\Property(property="status_emoji", type="string", example="⏳"),
     *                 @OA\Property(property="company_name", type="string", example="ABC İş Güvenliği Ltd. Şti."),
     *                 @OA\Property(property="authorized_person_name", type="string", example="Ahmet Yılmaz"),
     *                 @OA\Property(property="authorized_person_phone", type="string", example="05321234567"),
     *                 @OA\Property(property="tax_number", type="string", example="1234567890"),
     *                 @OA\Property(property="tax_office", type="string", example="Kadıköy"),
     *                 @OA\Property(property="business_field", type="string", example="İş Güvenliği Danışmanlığı"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     * Get current user's dealer application status.
     */
    public function index(): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'message' => 'Giriş yapmanız gerekiyor.',
            ], 401);
        }

        $application = DealerApplication::where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$application) {
            return response()->json([
                'has_application' => false,
                'can_apply' => $this->dealerApplicationService->canUserApply($user),
                'is_dealer' => $user->is_approved_dealer ?? false,
                'dealer_code' => $user->dealer_code,
            ]);
        }

        return response()->json([
            'has_application' => true,
            'can_apply' => $this->dealerApplicationService->canUserApply($user),
            'is_dealer' => $user->is_approved_dealer ?? false,
            'dealer_code' => $user->dealer_code,
            'application' => [
                'id' => $application->id,
                'status' => $application->status->value,
                'status_label' => $application->status->getLabel(),
                'status_color' => $application->status->getColor(),
                'status_emoji' => $application->status->getEmoji(),
                'company_name' => $application->company_name,
                'authorized_person_name' => $application->authorized_person_name,
                'authorized_person_phone' => $application->authorized_person_phone,
                'tax_number' => $application->tax_number,
                'tax_office' => $application->tax_office,
                'business_field' => $application->business_field,
                'created_at' => $application->created_at->toISOString(),
                'updated_at' => $application->updated_at->toISOString(),
            ],
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/dealer-applications",
     *     summary="Bayi başvurusu oluştur (kullanıcı kaydı ile)",
     *     description="Public endpoint: Yeni kullanıcı oluşturur ve bayi başvurusu kaydeder. multipart/form-data bekler.",
     *     operationId="storeDealerApplication",
     *     tags={"Dealer Applications"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"user_name","user_email","user_password","user_password_confirmation","user_phone","company_name","authorized_person_name","authorized_person_phone","tax_number","tax_office","address","email","business_field","trade_registry_document","tax_plate_document"},
     *                 @OA\Property(property="user_name", type="string", example="Ahmet Yılmaz"),
     *                 @OA\Property(property="user_email", type="string", format="email", example="ahmet@example.com"),
     *                 @OA\Property(property="user_password", type="string", format="password", example="password123"),
     *                 @OA\Property(property="user_password_confirmation", type="string", format="password", example="password123"),
     *                 @OA\Property(property="user_phone", type="string", example="05321234567"),
     *                 @OA\Property(property="company_name", type="string", example="ABC İş Güvenliği Ltd. Şti."),
     *                 @OA\Property(property="authorized_person_name", type="string", example="Ahmet Yılmaz"),
     *                 @OA\Property(property="authorized_person_phone", type="string", example="05321234567"),
     *                 @OA\Property(property="tax_number", type="string", example="1234567890"),
     *                 @OA\Property(property="tax_office", type="string", example="Kadıköy"),
     *                 @OA\Property(property="address", type="string", example="Kadıköy Mah. İnönü Cad. No:123 Kadıköy/İstanbul"),
     *                 @OA\Property(property="landline_phone", type="string", nullable=true, example="02161234567"),
     *                 @OA\Property(property="website", type="string", nullable=true, example="https://www.example.com"),
     *                 @OA\Property(property="email", type="string", format="email", example="ahmet@example.com"),
     *                 @OA\Property(property="business_field", type="string", example="İş Güvenliği Danışmanlığı"),
     *                 @OA\Property(property="reference_companies", type="string", nullable=true, example="XYZ Şirketi, ABC Şirketi"),
     *                 @OA\Property(property="trade_registry_document", type="string", format="binary"),
     *                 @OA\Property(property="tax_plate_document", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Başvuru ve kullanıcı başarıyla oluşturuldu",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Bayi başvurunuz ve kullanıcı hesabınız başarıyla oluşturuldu. Başvurunuz incelemeye alınmıştır."),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=123),
     *                 @OA\Property(property="name", type="string", example="Ahmet Yılmaz"),
     *                 @OA\Property(property="email", type="string", example="ahmet@example.com"),
     *                 @OA\Property(property="phone", type="string", example="05321234567")
     *             ),
     *             @OA\Property(property="application", type="object",
     *                 @OA\Property(property="id", type="integer", example=456),
     *                 @OA\Property(property="status", type="string", example="pending"),
     *                 @OA\Property(property="status_label", type="string", example="Beklemede"),
     *                 @OA\Property(property="company_name", type="string", example="ABC İş Güvenliği Ltd. Şti."),
     *                 @OA\Property(property="created_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation Error"),
     *     @OA\Response(response=500, description="Server Error")
     * )
     * Create a new dealer application with user registration.
     */
    public function store(StoreDealerApplicationRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // User bilgilerini al
            $userData = [
                'name' => $request->validated('user_name'),
                'email' => $request->validated('user_email'),
                'password' => Hash::make($request->validated('user_password')),
                'phone' => $request->validated('user_phone'),
                'is_approved_dealer' => false,
            ];

            // User oluştur veya mevcut olanı al
            $user = User::where('email', $userData['email'])->first();
            
            if ($user) {
                return response()->json([
                    'message' => 'Bu e-mail adresi ile zaten bir kullanıcı kaydı bulunmaktadır.',
                    'errors' => [
                        'user_email' => ['Bu e-mail adresi zaten kullanılmaktadır.']
                    ]
                ], 422);
            }

            // Yeni user oluştur
            $user = User::create($userData);

            // Dosyaları yükle
            $tradeRegistryPath = null;
            $taxPlatePath = null;

            if ($request->hasFile('trade_registry_document')) {
                $tradeRegistryPath = $request->file('trade_registry_document')
                    ->store('dealer-applications/trade-registry', 'private');
            }

            if ($request->hasFile('tax_plate_document')) {
                $taxPlatePath = $request->file('tax_plate_document')
                    ->store('dealer-applications/tax-plate', 'private');
            }

            // Application data'yı hazırla
            $applicationData = array_merge($request->validated(), [
                'trade_registry_document_path' => $tradeRegistryPath,
                'tax_plate_document_path' => $taxPlatePath,
                'email' => $request->validated('user_email'), // Application'da da email sakla
            ]);

            // Gereksiz user alanlarını çıkar
            unset($applicationData['user_name'], $applicationData['user_email'], 
                  $applicationData['user_password'], $applicationData['user_phone'],
                  $applicationData['trade_registry_document'], $applicationData['tax_plate_document']);

            // Başvuruyu oluştur
            $application = $this->dealerApplicationService->createApplication(
                $applicationData,
                DealerApplicationWorkflow::USER_REGISTRATION,
                $user
            );

            DB::commit();

            Log::info('API üzerinden bayi başvurusu oluşturuldu', [
                'application_id' => $application->id,
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return response()->json([
                'message' => 'Bayi başvurunuz ve kullanıcı hesabınız başarıyla oluşturuldu. Başvurunuz incelemeye alınmıştır.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ],
                'application' => [
                    'id' => $application->id,
                    'status' => $application->status->value,
                    'status_label' => $application->status->getLabel(),
                    'company_name' => $application->company_name,
                    'created_at' => $application->created_at->toISOString(),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Bayi başvuru API hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Başvuru işlemi sırasında bir hata oluştu. Lütfen tekrar deneyin.',
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/dealer-applications/{dealerApplication}",
     *     summary="Bayi başvuru detayını getir",
     *     description="Kimliği doğrulanmış kullanıcının kendi başvurusunun detayını döndürür.",
     *     operationId="showDealerApplication",
     *     tags={"Dealer Applications"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="dealerApplication", in="path", required=true, description="Başvuru ID",
     *         @OA\Schema(type="integer", example=456)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Başvuru detayı",
     *         @OA\JsonContent(
     *             @OA\Property(property="application", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="status", type="string", example="approved"),
     *                 @OA\Property(property="status_label", type="string", example="Onaylandı"),
     *                 @OA\Property(property="status_color", type="string", example="success"),
     *                 @OA\Property(property="status_emoji", type="string", example="✅"),
     *                 @OA\Property(property="company_name", type="string"),
     *                 @OA\Property(property="authorized_person_name", type="string"),
     *                 @OA\Property(property="authorized_person_phone", type="string"),
     *                 @OA\Property(property="tax_number", type="string"),
     *                 @OA\Property(property="tax_office", type="string"),
     *                 @OA\Property(property="address", type="string"),
     *                 @OA\Property(property="landline_phone", type="string", nullable=true),
     *                 @OA\Property(property="website", type="string", nullable=true),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="business_field", type="string"),
     *                 @OA\Property(property="reference_companies", type="string", nullable=true),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     * Display the specified dealer application.
     */
    public function show(DealerApplication $dealerApplication): JsonResponse
    {
        $user = auth()->user();
        
        // Sadece kendi başvurusunu görebilir
        if ($dealerApplication->user_id !== $user->id) {
            return response()->json([
                'message' => 'Bu başvuruyu görme yetkiniz bulunmuyor.',
            ], 403);
        }

        return response()->json([
            'application' => [
                'id' => $dealerApplication->id,
                'status' => $dealerApplication->status->value,
                'status_label' => $dealerApplication->status->getLabel(),
                'status_color' => $dealerApplication->status->getColor(),
                'status_emoji' => $dealerApplication->status->getEmoji(),
                'company_name' => $dealerApplication->company_name,
                'authorized_person_name' => $dealerApplication->authorized_person_name,
                'authorized_person_phone' => $dealerApplication->authorized_person_phone,
                'tax_number' => $dealerApplication->tax_number,
                'tax_office' => $dealerApplication->tax_office,
                'address' => $dealerApplication->address,
                'landline_phone' => $dealerApplication->landline_phone,
                'website' => $dealerApplication->website,
                'email' => $dealerApplication->email,
                'business_field' => $dealerApplication->business_field,
                'reference_companies' => $dealerApplication->reference_companies,
                'created_at' => $dealerApplication->created_at->toISOString(),
                'updated_at' => $dealerApplication->updated_at->toISOString(),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/dealer-applications/can-apply",
     *     summary="Başvuru yapabilir mi?",
     *     description="Public endpoint: Giriş yapmamış veya yapmış kullanıcı için başvuru yapılabilir mi bilgisini döndürür.",
     *     operationId="canApplyDealer",
     *     tags={"Dealer Applications"},
     *     @OA\Response(
     *         response=200,
     *         description="Bilgi",
     *         @OA\JsonContent(
     *             @OA\Property(property="can_apply", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Bayi başvurusu yapabilirsiniz."),
     *             @OA\Property(property="is_dealer", type="boolean", example=false),
     *             @OA\Property(property="dealer_code", type="string", nullable=true)
     *         )
     *     )
     * )
     * Check if user can apply for dealer status.
     */
    public function canApply(): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'can_apply' => true,
                'message' => 'Yeni kullanıcı olarak başvuru yapabilirsiniz.',
            ]);
        }

        $canApply = $this->dealerApplicationService->canUserApply($user);
        
        $message = match (true) {
            $user->is_approved_dealer => 'Zaten onaylanmış bir bayisiniz.',
            !$canApply => 'Beklemede olan bir başvurunuz bulunmaktadır.',
            default => 'Bayi başvurusu yapabilirsiniz.',
        };

        return response()->json([
            'can_apply' => $canApply,
            'message' => $message,
            'is_dealer' => $user->is_approved_dealer ?? false,
            'dealer_code' => $user->dealer_code,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/dealer-applications/statuses",
     *     summary="Başvuru status referansları",
     *     description="Public endpoint: Başvuru status referanslarını döndürür.",
     *     operationId="dealerApplicationStatuses",
     *     tags={"Dealer Applications"},
     *     @OA\Response(
     *         response=200,
     *         description="Status listesi",
     *         @OA\JsonContent(
     *             @OA\Property(property="statuses", type="object",
     *                 @OA\Property(property="pending", type="object",
     *                     @OA\Property(property="value", type="string", example="pending"),
     *                     @OA\Property(property="label", type="string", example="Beklemede"),
     *                     @OA\Property(property="color", type="string", example="warning"),
     *                     @OA\Property(property="emoji", type="string", example="⏳")
     *                 ),
     *                 @OA\Property(property="approved", type="object",
     *                     @OA\Property(property="value", type="string", example="approved"),
     *                     @OA\Property(property="label", type="string", example="Onaylandı"),
     *                     @OA\Property(property="color", type="string", example="success"),
     *                     @OA\Property(property="emoji", type="string", example="✅")
     *                 ),
     *                 @OA\Property(property="rejected", type="object",
     *                     @OA\Property(property="value", type="string", example="rejected"),
     *                     @OA\Property(property="label", type="string", example="Reddedildi"),
     *                     @OA\Property(property="color", type="string", example="danger"),
     *                     @OA\Property(property="emoji", type="string", example="❌")
     *                 )
     *             )
     *         )
     *     )
     * )
     * Get application statuses for reference.
     */
    public function statuses(): JsonResponse
    {
        return response()->json([
            'statuses' => [
                'pending' => [
                    'value' => DealerApplicationStatus::PENDING->value,
                    'label' => DealerApplicationStatus::PENDING->getLabel(),
                    'color' => DealerApplicationStatus::PENDING->getColor(),
                    'emoji' => DealerApplicationStatus::PENDING->getEmoji(),
                ],
                'approved' => [
                    'value' => DealerApplicationStatus::APPROVED->value,
                    'label' => DealerApplicationStatus::APPROVED->getLabel(),
                    'color' => DealerApplicationStatus::APPROVED->getColor(),
                    'emoji' => DealerApplicationStatus::APPROVED->getEmoji(),
                ],
                'rejected' => [
                    'value' => DealerApplicationStatus::REJECTED->value,
                    'label' => DealerApplicationStatus::REJECTED->getLabel(),
                    'color' => DealerApplicationStatus::REJECTED->getColor(),
                    'emoji' => DealerApplicationStatus::REJECTED->getEmoji(),
                ],
            ],
        ]);
    }
}
