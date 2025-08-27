<?php
declare(strict_types=1);

namespace App\Services\DealerApplication;

use App\Models\User;
use App\Models\DealerApplication;
use App\Enums\DealerApplicationStatus;
use App\Enums\DealerApplicationWorkflow;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DealerApplicationService
{
    /**
     * Create a dealer application with user management based on workflow type.
     */
    public function createApplication(
        array $applicationData,
        DealerApplicationWorkflow $workflow = DealerApplicationWorkflow::USER_REGISTRATION,
        ?User $existingUser = null
    ): DealerApplication {
        Log::info('Bayi başvuru oluşturma başladı', [
            'workflow' => $workflow->value,
            'company_name' => $applicationData['company_name'],
            'has_existing_user' => !is_null($existingUser)
        ]);

        $user = match ($workflow) {
            DealerApplicationWorkflow::GUEST_REGISTRATION => $this->createUserFromApplication($applicationData),
            DealerApplicationWorkflow::USER_REGISTRATION => $existingUser ?? auth()->user(),
            DealerApplicationWorkflow::APPROVAL_REGISTRATION => null, // User onaylandığında oluşacak
        };

        // Application data'yı hazırla
        $finalApplicationData = array_merge($applicationData, [
            'user_id' => $user?->id,
            'status' => DealerApplicationStatus::PENDING,
        ]);

        // Eğer user yok ise (APPROVAL_REGISTRATION), email'i application'da sakla
        if (!$user && $workflow === DealerApplicationWorkflow::APPROVAL_REGISTRATION) {
            $finalApplicationData['email'] = $applicationData['email'];
        }

        $application = DealerApplication::create($finalApplicationData);

        Log::info('Bayi başvurusu oluşturuldu', [
            'application_id' => $application->id,
            'workflow' => $workflow->value,
            'user_id' => $user?->id,
        ]);

        return $application;
    }

    /**
     * Approve a dealer application and handle user creation if needed.
     */
    public function approveApplication(DealerApplication $application): DealerApplication
    {
        Log::info('Bayi başvuru onaylanıyor', [
            'application_id' => $application->id,
            'has_user' => !is_null($application->user_id)
        ]);

        // Eğer user yoksa oluştur (APPROVAL_REGISTRATION workflow'u)
        if (!$application->user_id) {
            $user = $this->createUserFromApplication($application->toArray());
            $application->update(['user_id' => $user->id]);
            $application->refresh();
        }

        // User'ı approved dealer yap
        $dealerCode = $this->generateDealerCode($application);
        $application->user->update([
            'is_approved_dealer' => true,
            'dealer_code' => $dealerCode,
        ]);

        // Status'u approved yap
        $application->update(['status' => DealerApplicationStatus::APPROVED]);

        Log::info('Bayi başvurusu onaylandı', [
            'application_id' => $application->id,
            'user_id' => $application->user_id,
            'dealer_code' => $dealerCode,
        ]);

        return $application->refresh();
    }

    /**
     * Reject a dealer application.
     */
    public function rejectApplication(DealerApplication $application): DealerApplication
    {
        Log::info('Bayi başvuru reddediliyor', [
            'application_id' => $application->id,
            'user_id' => $application->user_id
        ]);

        // User varsa dealer statüsünü kaldır
        if ($application->user_id) {
            $application->user->update([
                'is_approved_dealer' => false,
                'dealer_code' => null,
            ]);
        }

        // Status'u rejected yap
        $application->update(['status' => DealerApplicationStatus::REJECTED]);

        Log::info('Bayi başvurusu reddedildi', [
            'application_id' => $application->id,
        ]);

        return $application->refresh();
    }

    /**
     * Create user from application data.
     */
    private function createUserFromApplication(array $applicationData): User
    {
        $userData = [
            'name' => $applicationData['authorized_person_name'],
            'email' => $applicationData['email'],
            'password' => Hash::make(Str::random(12)), // Random password, user will reset
            'phone' => $applicationData['authorized_person_phone'],
            'is_approved_dealer' => false, // Başlangıçta false, onaylandığında true olacak
        ];

        $user = User::create($userData);

        Log::info('Bayi başvuru için kullanıcı oluşturuldu', [
            'user_id' => $user->id,
            'email' => $user->email,
            'company' => $applicationData['company_name']
        ]);

        return $user;
    }

    /**
     * Generate unique dealer code.
     */
    private function generateDealerCode(DealerApplication $application): string
    {
        $prefix = 'BAYI';
        $companyInitials = strtoupper(substr($application->company_name, 0, 3));
        $year = date('Y');
        $id = str_pad((string) $application->id, 4, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$companyInitials}-{$year}-{$id}";
    }

    /**
     * Get recommended workflow based on context.
     */
    public function getRecommendedWorkflow(?User $user = null): DealerApplicationWorkflow
    {
        // Eğer kullanıcı giriş yapmışsa USER_REGISTRATION
        if ($user || auth()->check()) {
            return DealerApplicationWorkflow::USER_REGISTRATION;
        }

        // Misafir için varsayılan GUEST_REGISTRATION
        return DealerApplicationWorkflow::GUEST_REGISTRATION;
    }

    /**
     * Check if user can apply as dealer.
     */
    public function canUserApply(?User $user = null): bool
    {
        $user = $user ?? auth()->user();

        if (!$user) {
            return true; // Guest her zaman başvurabilir
        }

        // Zaten onaylanmış bayi ise başvuramaz
        if ($user->is_approved_dealer) {
            return false;
        }

        // Bekleyen başvurusu varsa başvuramaz
        $hasPendingApplication = DealerApplication::where('user_id', $user->id)
            ->where('status', DealerApplicationStatus::PENDING)
            ->exists();

        return !$hasPendingApplication;
    }
}