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
        $this->middleware('throttle:dealer-applications')->only(['store']);
    }

    /**
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
