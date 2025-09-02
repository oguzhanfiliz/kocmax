<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'first_name',
        'last_name',
        'company_name',
        'phone',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default_shipping',
        'is_default_billing',
        'type', // home, work, billing, other
        'category', // shipping, billing, both
        'notes',
    ];

    protected $casts = [
        'is_default_shipping' => 'boolean',
        'is_default_billing' => 'boolean',
    ];

    /**
     * Address types - Daha detaylı türler
     */
    public const TYPE_HOME = 'home';
    public const TYPE_WORK = 'work';
    public const TYPE_BILLING = 'billing';
    public const TYPE_OTHER = 'other';

    /**
     * Address categories - Shipping, billing ya da her ikisi
     */
    public const CATEGORY_SHIPPING = 'shipping';
    public const CATEGORY_BILLING = 'billing';
    public const CATEGORY_BOTH = 'both';

    public static function getTypes(): array
    {
        return [
            self::TYPE_HOME => 'Ev',
            self::TYPE_WORK => 'İş',
            self::TYPE_BILLING => 'Fatura',
            self::TYPE_OTHER => 'Diğer',
        ];
    }

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_SHIPPING => 'Sadece Kargo',
            self::CATEGORY_BILLING => 'Sadece Fatura',
            self::CATEGORY_BOTH => 'Kargo ve Fatura',
        ];
    }

    /**
     * Get the user that owns the address
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get formatted address
     */
    public function getFormattedAddressAttribute(): string
    {
        $address = $this->address_line_1;
        
        if ($this->address_line_2) {
            $address .= ', ' . $this->address_line_2;
        }
        
        $address .= ', ' . $this->city;
        
        if ($this->state) {
            $address .= ', ' . $this->state;
        }
        
        $address .= ' ' . $this->postal_code;
        $address .= ', ' . $this->country;
        
        return $address;
    }

    /**
     * Set as default shipping address
     */
    public function setAsDefaultShipping(): void
    {
        // Remove default from other user addresses
        $this->user->addresses()
            ->where('id', '!=', $this->id)
            ->update(['is_default_shipping' => false]);

        $this->update(['is_default_shipping' => true]);
    }

    /**
     * Set as default billing address
     */
    public function setAsDefaultBilling(): void
    {
        // Remove default from other user addresses
        $this->user->addresses()
            ->where('id', '!=', $this->id)
            ->update(['is_default_billing' => false]);

        $this->update(['is_default_billing' => true]);
    }

    /**
     * Scope for shipping addresses
     */
    public function scopeShipping($query)
    {
        return $query->whereIn('category', [self::CATEGORY_SHIPPING, self::CATEGORY_BOTH]);
    }

    /**
     * Scope for billing addresses
     */
    public function scopeBilling($query)
    {
        return $query->whereIn('category', [self::CATEGORY_BILLING, self::CATEGORY_BOTH]);
    }

    /**
     * Scope by address type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for user addresses
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}