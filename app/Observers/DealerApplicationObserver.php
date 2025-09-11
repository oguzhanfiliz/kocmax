<?php
declare(strict_types=1);

namespace App\Observers;

use App\Models\DealerApplication;
use App\Jobs\SendDealerApplicationApprovedEmail;
use App\Jobs\SendDealerApplicationRejectedEmail;
use App\Jobs\SendDealerApplicationCreatedEmail;
use App\Services\DealerApplication\DealerApplicationService;
use App\Enums\DealerApplicationStatus;
use Illuminate\Support\Facades\Log;

class DealerApplicationObserver
{
    /**
     * Handle the DealerApplication "created" event.
     */
    public function created(DealerApplication $dealerApplication): void
    {
        try {
            // Admin'lere yeni başvuru bildirim e-postası gönder (queue ile)
            SendDealerApplicationCreatedEmail::dispatch($dealerApplication->id);
            
            // Başvuru oluşturma logunu kaydet
            Log::info('Yeni bayi başvurusu oluşturuldu', [
                'application_id' => $dealerApplication->id,
                'user_id' => $dealerApplication->user_id,
                'company_name' => $dealerApplication->company_name,
                'tax_number' => $dealerApplication->tax_number,
            ]);
            
        } catch (\Exception $e) {
            Log::error('DealerApplication created event hatası', [
                'application_id' => $dealerApplication->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the DealerApplication "updated" event.
     */
    public function updated(DealerApplication $dealerApplication): void
    {
        try {
            // Durum değişikliği kontrolü - sonsuz döngüyü önlemek için
            if ($dealerApplication->isDirty('status')) {
                $originalStatus = $dealerApplication->getOriginal('status');
                $newStatus = $dealerApplication->status;
                
                Log::info('Bayi başvurusu durum değişikliği', [
                    'application_id' => $dealerApplication->id,
                    'user_id' => $dealerApplication->user_id,
                    'old_status' => $originalStatus,
                    'new_status' => $newStatus,
                    'company_name' => $dealerApplication->company_name,
                ]);
                
                // Onaylandığında, kullanıcıya bayi kodu atanmış olsun
                if ($newStatus === DealerApplicationStatus::APPROVED) {
                    // Bayi kodu ve kullanıcı durumunu garanti altına al
                    try {
                        app(DealerApplicationService::class)->approveApplication($dealerApplication);
                    } catch (\Throwable $e) {
                        Log::error('approveApplication sırasında hata', [
                            'application_id' => $dealerApplication->id,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    // Onay e-postasını queue ile gönder
                    SendDealerApplicationApprovedEmail::dispatch($dealerApplication->id);

                } elseif ($newStatus === DealerApplicationStatus::REJECTED) {
                    // Red e-postasını queue ile gönder
                    SendDealerApplicationRejectedEmail::dispatch($dealerApplication->id);
                }
            }
            
            // Diğer önemli alan değişiklikleri için log
            $importantFields = ['company_name', 'tax_number', 'authorized_person_name', 'email'];
            $changedFields = [];
            
            foreach ($importantFields as $field) {
                if ($dealerApplication->isDirty($field)) {
                    $changedFields[$field] = [
                        'old' => $dealerApplication->getOriginal($field),
                        'new' => $dealerApplication->$field,
                    ];
                }
            }
            
            if (!empty($changedFields)) {
                Log::info('Bayi başvurusu önemli alan güncellemesi', [
                    'application_id' => $dealerApplication->id,
                    'user_id' => $dealerApplication->user_id,
                    'changed_fields' => $changedFields,
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('DealerApplication updated event hatası', [
                'application_id' => $dealerApplication->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle the DealerApplication "deleted" event.
     */
    public function deleted(DealerApplication $dealerApplication): void
    {
        try {
            Log::warning('Bayi başvurusu silindi', [
                'application_id' => $dealerApplication->id,
                'user_id' => $dealerApplication->user_id,
                'company_name' => $dealerApplication->company_name,
                'tax_number' => $dealerApplication->tax_number,
                'status' => $dealerApplication->status,
            ]);
            
            // Eğer onaylanmış bir başvuru siliniyorsa kullanıcının bayi statüsünü kaldır
            if ($dealerApplication->isApproved()) {
                $dealerApplication->user->update([
                    'is_approved_dealer' => false,
                    'dealer_code' => null,
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('DealerApplication deleted event hatası', [
                'application_id' => $dealerApplication->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the DealerApplication "restored" event.
     */
    public function restored(DealerApplication $dealerApplication): void
    {
        try {
            Log::info('Bayi başvurusu geri yüklendi', [
                'application_id' => $dealerApplication->id,
                'user_id' => $dealerApplication->user_id,
                'company_name' => $dealerApplication->company_name,
                'status' => $dealerApplication->status,
            ]);
            
            // Eğer onaylanmış bir başvuru geri yükleniyorsa kullanıcının bayi statüsünü geri ver
            if ($dealerApplication->isApproved()) {
                app(DealerApplicationService::class)->approveApplication($dealerApplication);
            }
            
        } catch (\Exception $e) {
            Log::error('DealerApplication restored event hatası', [
                'application_id' => $dealerApplication->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the DealerApplication "force deleted" event.
     */
    public function forceDeleted(DealerApplication $dealerApplication): void
    {
        try {
            Log::critical('Bayi başvurusu kalıcı olarak silindi', [
                'application_id' => $dealerApplication->id,
                'user_id' => $dealerApplication->user_id,
                'company_name' => $dealerApplication->company_name,
                'tax_number' => $dealerApplication->tax_number,
            ]);
            
        } catch (\Exception $e) {
            Log::error('DealerApplication forceDeleted event hatası', [
                'application_id' => $dealerApplication->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
