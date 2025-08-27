<?php
declare(strict_types=1);

namespace App\Models;

use App\Enums\DealerApplicationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Kitlesel olarak atanabilir öznitelikler.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'authorized_person_name',
        'authorized_person_phone',
        'tax_number',
        'tax_office',
        'address',
        'landline_phone',
        'website',
        'email',
        'business_field',
        'reference_companies',
        'trade_registry_document_path',
        'tax_plate_document_path',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'status' => DealerApplicationStatus::class,
    ];

    /**
     * Get the user who submitted the application.
     * Başvuruyu gönderen kullanıcıyı alır.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the validation rules for the dealer application.
     * Bayi başvurusu için validation kurallarını alır.
     */
    public static function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'company_name' => ['required', 'string', 'max:255', 'min:2'],
            'authorized_person_name' => ['required', 'string', 'max:255', 'min:2'],
            'authorized_person_phone' => ['required', 'string', 'max:20', 'min:10', 'regex:/^[\d\s\-\+\(\)]+$/'],
            'tax_number' => ['required', 'string', 'max:20', 'min:10', 'unique:dealer_applications,tax_number'],
            'tax_office' => ['required', 'string', 'max:255', 'min:2'],
            'address' => ['required', 'string', 'max:1000', 'min:10'],
            'landline_phone' => ['nullable', 'string', 'max:20', 'min:10', 'regex:/^[\d\s\-\+\(\)]+$/'],
            'website' => ['nullable', 'url', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'business_field' => ['required', 'string', 'max:255', 'min:2'],
            'reference_companies' => ['nullable', 'string', 'max:2000'],
            'trade_registry_document_path' => ['required', 'string', 'max:255'],
            'tax_plate_document_path' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:pending,approved,rejected'],
        ];
    }

    /**
     * Get the validation rules for updating (without unique constraints).
     */
    public static function updateRules(int $id): array
    {
        $rules = static::rules();
        $rules['tax_number'] = ['required', 'string', 'max:20', 'min:10', 'unique:dealer_applications,tax_number,' . $id];
        return $rules;
    }

    /**
     * Scope for pending applications.
     */
    public function scopePending($query)
    {
        return $query->where('status', DealerApplicationStatus::PENDING);
    }

    /**
     * Scope for approved applications.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', DealerApplicationStatus::APPROVED);
    }

    /**
     * Scope for rejected applications.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', DealerApplicationStatus::REJECTED);
    }

    /**
     * Check if application is pending.
     */
    public function isPending(): bool
    {
        return $this->status === DealerApplicationStatus::PENDING;
    }

    /**
     * Check if application is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === DealerApplicationStatus::APPROVED;
    }

    /**
     * Check if application is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === DealerApplicationStatus::REJECTED;
    }

    /**
     * Get status display with emoji.
     */
    public function getStatusDisplayAttribute(): string
    {
        return $this->status->getDisplayWithEmoji();
    }

    /**
     * Get status icon.
     */
    public function getStatusIconAttribute(): string
    {
        return $this->status->getIcon();
    }

    /**
     * Get status color.
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status->getColor();
    }
}
