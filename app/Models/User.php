<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     * Kitlesel olarak atananabilir öznitelikler.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', // User's full name | Kullanıcının tam adı
        'email', // User's email address | Kullanıcının e-posta adresi
        'password', // User's password | Kullanıcının şifresi
        'phone', // User's phone number | Kullanıcının telefon numarası
        'position', // User's position in the company | Kullanıcının şirketteki pozisyonu
        'bio', // A short biography of the user | Kullanıcının kısa biyografisi
        'avatar', // URL to the user's avatar image | Kullanıcının avatar resminin URL'si
        'is_active', // Whether the user account is active | Kullanıcı hesabının aktif olup olmadığı
        'last_login_at', // The last time the user logged in | Kullanıcının son giriş yaptığı zaman
        'dealer_code', // The dealer code associated with the user | Kullanıcıyla ilişkili bayi kodu
        'company_name', // The name of the dealer's company | Bayinin şirket adı
        'tax_number', // The tax number of the dealer's company | Bayinin vergi numarası
        'dealer_discount_percentage', // The discount percentage for the dealer | Bayi için indirim yüzdesi
        'is_approved_dealer', // Whether the user is an approved dealer | Kullanıcının onaylanmış bir bayi olup olmadığı
        'dealer_application_date', // The date the user applied to be a dealer | Kullanıcının bayilik başvuru tarihi
        'approved_at', // The date the user's dealer application was approved | Kullanıcının bayi başvurusunun onaylandığı tarih
        'pricing_tier_id', // Customer pricing tier | Müşteri fiyatlandırma seviyesi
        'customer_type_override', // Customer type override | Müşteri tipi zorlaması
        'custom_discount_percentage', // Custom discount percentage | Özel indirim yüzdesi
        'allow_backorders', // Allow backorders | Ön sipariş izni
        'payment_terms_days', // Payment terms in days | Ödeme vadesi (gün)
        'credit_limit', // Credit limit for B2B customers | B2B müşteriler için kredi limiti
        'loyalty_points', // Loyalty program points | Sadakat programı puanları
        'last_order_at', // Last order date | Son sipariş tarihi
        'lifetime_value', // Customer lifetime value | Müşteri yaşam boyu değeri
        'email_verification_token', // Email verification token | E-posta doğrulama token'ı
    ];

    /**
     * The attributes that should be hidden for serialization.
     * Serileştirme için gizlenmesi gereken öznitelikler.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     * Özniteliklerin dönüştürülmesi gereken türler.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'is_approved_dealer' => 'boolean',
        'dealer_discount_percentage' => 'float',
        'dealer_application_date' => 'datetime',
        'approved_at' => 'datetime',
        'custom_discount_percentage' => 'decimal:2',
        'allow_backorders' => 'boolean',
        'payment_terms_days' => 'integer',
        'credit_limit' => 'decimal:2',
        'loyalty_points' => 'integer',
        'last_order_at' => 'datetime',
        'lifetime_value' => 'decimal:2',
    ];

    /**
     * Determine if the user can access the Filament panel.
     * Kullanıcının Filament paneline erişip erişemeyeceğini belirler.
     *
     * @param Panel $panel The panel being accessed.
     * @return bool True if the user can access the panel, false otherwise.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // Add appropriate authorization logic here for production environments. | Üretim ortamları için buraya uygun yetkilendirme mantığını ekleyin.
    }

    /**
     * Check if the user is an approved dealer.
     * Kullanıcının onaylanmış bir bayi olup olmadığını kontrol eder.
     *
     * @return bool True if the user is an approved dealer, false otherwise.
     */
    public function isDealer(): bool
    {
        return $this->hasRole('dealer') || $this->is_approved_dealer;
    }

    /**
     * Get the pages created by the user.
     * Kullanıcı tarafından oluşturulan sayfaları alır.
     */
    public function createdPages(): HasMany
    {
        return $this->hasMany(Page::class, 'created_by');
    }

    /**
     * Get the pages updated by the user.
     * Kullanıcı tarafından güncellenen sayfaları alır.
     */
    public function updatedPages(): HasMany
    {
        return $this->hasMany(Page::class, 'updated_by');
    }

    /**
     * Get the settings created by the user.
     * Kullanıcı tarafından oluşturulan ayarları alır.
     */
    public function createdSettings(): HasMany
    {
        return $this->hasMany(Setting::class, 'created_by');
    }

    /**
     * Get the settings updated by the user.
     * Kullanıcı tarafından güncellenen ayarları alır.
     */
    public function updatedSettings(): HasMany
    {
        return $this->hasMany(Setting::class, 'updated_by');
    }

    /**
     * Get the user's pricing tier.
     * Kullanıcının fiyatlandırma seviyesini alır.
     */
    public function pricingTier()
    {
        return $this->belongsTo(CustomerPricingTier::class, 'pricing_tier_id');
    }

    /**
     * Get the user's orders.
     * Kullanıcının siparişlerini alır.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }


    /**
     * Get customer type with override support
     */
    public function getCustomerType(): \App\Enums\Pricing\CustomerType
    {
        if ($this->customer_type_override) {
            return \App\Enums\Pricing\CustomerType::from($this->customer_type_override);
        }

        return app(\App\Services\Pricing\CustomerTypeDetector::class)->detect($this);
    }

    /**
     * Check if user has custom pricing
     */
    public function hasCustomPricing(): bool
    {
        return $this->custom_discount_percentage > 0 || $this->pricing_tier_id !== null;
    }

    /**
     * Get effective discount percentage
     */
    public function getEffectiveDiscountPercentage(): float
    {
        // Custom discount takes priority
        if ($this->custom_discount_percentage > 0) {
            return $this->custom_discount_percentage;
        }

        // Then pricing tier discount
        if ($this->pricingTier && $this->pricingTier->isActive()) {
            return $this->pricingTier->discount_percentage;
        }

        // Legacy dealer discount
        if ($this->dealer_discount_percentage > 0) {
            return $this->dealer_discount_percentage;
        }

        return 0;
    }

    /**
     * Get the user's reviews.
     * Kullanıcının yorumlarını alır.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get the user's carts.
     * Kullanıcının sepetlerini alır.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get the user's active cart.
     * Kullanıcının aktif sepetini alır.
     */
    public function activeCart()
    {
        return $this->hasOne(Cart::class)->latest();
    }

    /**
     * Get the dealer's discounts.
     * Bayi indirimlerini alır.
     */
    public function dealerDiscounts(): HasMany
    {
        return $this->hasMany(DealerDiscount::class, 'dealer_id');
    }

    /**
     * Get the user's dealer application.
     * Kullanıcının bayi başvurusunu alır.
     */
    public function dealerApplication()
    {
        return $this->hasOne(DealerApplication::class);
    }

    /**
     * Get the user's addresses
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get the default shipping address
     */
    public function defaultShippingAddress()
    {
        return $this->addresses()
            ->where('is_default_shipping', true)
            ->where(function ($query) {
                $query->where('type', 'shipping')
                      ->orWhere('type', 'both');
            })
            ->first();
    }

    /**
     * Get the default billing address
     */
    public function defaultBillingAddress()
    {
        return $this->addresses()
            ->where('is_default_billing', true)
            ->where(function ($query) {
                $query->where('type', 'billing')
                      ->orWhere('type', 'both');
            })
            ->first();
    }
}
