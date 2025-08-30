# Development Mode - Ultra Minimal Backend Değişiklikler

## 🚀 Sadece Temel Olanlar (Development için)

### 1. Environment Variables (.env - 30 saniye)

```bash
# Smart Pricing Configuration
SMART_PRICING_ENABLED=true

# Development Mode - Rate limiting kapalı
APP_ENV=local
THROTTLE_DISABLED=true
```

### 2. Tek Middleware (5 dakika)

**app/Http/Middleware/AddPricingHeaders.php:**
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AddPricingHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Development modda sadece temel headers
        if ($user = $request->user()) {
            $customerType = $this->determineCustomerType($user);
            $response->headers->set('X-Customer-Type', $customerType);
            $response->headers->set('X-Is-Dealer', in_array($customerType, ['B2B', 'WHOLESALE']) ? 'true' : 'false');
        } else {
            $response->headers->set('X-Customer-Type', 'GUEST');
            $response->headers->set('X-Is-Dealer', 'false');
        }
        
        return $response;
    }
    
    private function determineCustomerType($user): string
    {
        // Customer type override varsa öncelik ver
        if (!empty($user->customer_type_override)) {
            return strtoupper($user->customer_type_override);
        }
        
        // Roller kontrol et
        if ($user->hasRole('wholesale')) {
            return 'WHOLESALE';
        }
        
        if ($user->hasRole('dealer') || $user->is_approved_dealer) {
            return 'B2B';
        }
        
        if ($user->hasRole('retail')) {
            return 'RETAIL';
        }
        
        // Company bilgisi varsa B2B kabul et
        if (!empty($user->company_name) || !empty($user->tax_number)) {
            return 'B2B';
        }
        
        // Lifetime value yüksekse wholesale
        if (($user->lifetime_value ?? 0) >= 50000) {
            return 'WHOLESALE';
        }
        
        // Default B2C
        return 'B2C';
    }
}
```

### 3. Kernel'a Middleware Ekle (1 dakika)

**app/Http/Kernel.php:**
```php
protected $routeMiddleware = [
    // ... mevcut middleware'ler
    'pricing.headers' => \App\Http\Middleware\AddPricingHeaders::class,
];
```

### 4. Route'lara Sadece Header Middleware (1 dakika)

**routes/api.php (sadece bu satırı değiştir):**
```php
// Bu satırı bul:
Route::prefix('v1/products')->middleware(['api', 'domain.cors', 'throttle:public'])->group(function () {

// Bu şekilde değiştir (throttle'ı kaldır):
Route::prefix('v1/products')->middleware(['api', 'domain.cors', 'pricing.headers'])->group(function () {
```

### 5. ProductResource'a Pricing Ekle (10 dakika)

**app/Http/Resources/ProductResource.php (sadece toArray metodunu genişlet):**
```php
public function toArray($request)
{
    // Mevcut array'e pricing ekle
    $data = [
        'id' => $this->id,
        'name' => $this->name,
        'slug' => $this->slug,
        'description' => $this->description,
        'base_price' => $this->base_price,
        'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),
        'images' => ProductImageResource::collection($this->whenLoaded('images')),
        'categories' => CategoryResource::collection($this->whenLoaded('categories')),
        
        // Smart Pricing Eklentisi
        'pricing' => $this->calculatePricingForUser($request->user()),
    ];
    
    return $data;
}

private function calculatePricingForUser($user)
{
    $basePrice = (float) $this->base_price;
    
    // Guest pricing
    if (!$user) {
        return [
            'base_price' => $basePrice,
            'your_price' => $basePrice,
            'currency' => 'TRY',
            'customer_type' => 'GUEST',
            'is_dealer_price' => false,
            'discount_percentage' => 0.0,
            'base_price_formatted' => number_format($basePrice, 2, ',', '.') . ' ₺',
            'your_price_formatted' => number_format($basePrice, 2, ',', '.') . ' ₺',
        ];
    }
    
    // Customer type tespiti
    $customerType = $this->determineCustomerType($user);
    $discountPercentage = $this->getDiscountPercentage($user, $customerType);
    $yourPrice = $basePrice * (1 - $discountPercentage / 100);
    
    return [
        'base_price' => $basePrice,
        'your_price' => $yourPrice,
        'currency' => 'TRY', 
        'customer_type' => $customerType,
        'is_dealer_price' => in_array($customerType, ['B2B', 'WHOLESALE']),
        'discount_percentage' => $discountPercentage,
        'savings' => $basePrice - $yourPrice,
        'base_price_formatted' => number_format($basePrice, 2, ',', '.') . ' ₺',
        'your_price_formatted' => number_format($yourPrice, 2, ',', '.') . ' ₺',
        'pricing_tier' => $user->pricingTier?->name,
        'customer_type_label' => $this->getCustomerTypeLabel($customerType),
    ];
}

private function determineCustomerType($user)
{
    // Customer type override varsa öncelik ver
    if (!empty($user->customer_type_override)) {
        return strtoupper($user->customer_type_override);
    }
    
    // Roller kontrol et
    if ($user->hasRole('wholesale')) {
        return 'WHOLESALE';
    }
    
    if ($user->hasRole('dealer') || $user->is_approved_dealer) {
        return 'B2B';
    }
    
    if ($user->hasRole('retail')) {
        return 'RETAIL';
    }
    
    // Company bilgisi varsa B2B kabul et
    if (!empty($user->company_name) || !empty($user->tax_number)) {
        return 'B2B';
    }
    
    // Lifetime value yüksekse wholesale
    if (($user->lifetime_value ?? 0) >= 50000) {
        return 'WHOLESALE';
    }
    
    // Default B2C
    return 'B2C';
}

private function getDiscountPercentage($user, $customerType)
{
    // Pricing tier'dan indirim al
    if ($user->pricingTier) {
        return $user->pricingTier->discount_percentage ?? 0.0;
    }
    
    // Custom discount varsa
    if ($user->custom_discount_percentage > 0) {
        return $user->custom_discount_percentage;
    }
    
    // Customer type'a göre default indirimler
    return match($customerType) {
        'WHOLESALE' => 15.0,  // Toptan satış
        'B2B' => 10.0,        // Bayiler  
        'RETAIL' => 5.0,      // Perakende
        'B2C' => 0.0,         // Bireysel
        default => 0.0
    };
}

private function getCustomerTypeLabel($customerType)
{
    return match($customerType) {
        'B2B' => '🏢 Bayi Fiyatı',
        'B2C' => '👤 Bireysel Fiyat',
        'WHOLESALE' => '📦 Toptan Fiyat', 
        'RETAIL' => '🛍️ Perakende Fiyat',
        'GUEST' => 'Liste Fiyatı',
        default => 'Standart Fiyat'
    };
}
```

## ✅ Bu Kadar! (Toplam: ~17 dakika)

**Değişen Dosyalar:**
1. `.env` - 1 satır ekleme
2. `AddPricingHeaders.php` - yeni dosya
3. `Kernel.php` - 1 satır ekleme  
4. `routes/api.php` - 1 satır değişiklik
5. `ProductResource.php` - 1 method ekleme

## 🧪 Test Etmek İçin:

```bash
# Guest pricing test
curl -H "Accept: application/json" \
     "http://your-local-api.test/api/v1/products/1"

# B2B pricing test (auth token ile)
curl -H "Accept: application/json" \
     -H "Authorization: Bearer YOUR_TOKEN" \
     "http://your-local-api.test/api/v1/products/1"
```

**Beklenen Response (B2B Kullanıcı):**
```json
{
  "data": {
    "id": 1,
    "name": "Test Product",
    "pricing": {
      "base_price": 150.00,
      "your_price": 135.00,
      "customer_type": "B2B",
      "customer_type_label": "🏢 Bayi Fiyatı",
      "is_dealer_price": true,
      "discount_percentage": 10.0,
      "savings": 15.00,
      "your_price_formatted": "135,00 ₺"
    }
  }
}
```

**Headers:**
```
X-Customer-Type: B2B
X-Is-Dealer: true
```

**Beklenen Response (Toptan Müşteri):**
```json
{
  "data": {
    "pricing": {
      "base_price": 150.00,
      "your_price": 127.50,
      "customer_type": "WHOLESALE",
      "customer_type_label": "📦 Toptan Fiyat",
      "is_dealer_price": true,
      "discount_percentage": 15.0,
      "savings": 22.50
    }
  }
}
```

## 🎯 Production'a Geçerken Eklenecekler:

Development tamamlandıktan sonra bu güvenlik katmanlarını ekleriz:

1. **Rate Limiting**: `throttle:public` middleware'ini geri ekle
2. **CORS Configuration**: Domain protection
3. **Input Validation**: Request validation rules
4. **Error Handling**: Proper exception handling  
5. **Logging**: Performance ve error logs
6. **Caching**: Redis ile response caching

## 💡 Development Tips:

```bash
# API loglarını izlemek için
tail -f storage/logs/laravel.log | grep -i pricing

# Cache temizlemek için (gerekirse)
php artisan cache:clear
php artisan config:clear

# Route listesini kontrol etmek için
php artisan route:list --path=api/v1/products
```

Bu minimal yaklaşımla development sırasında hiçbir engel yaşamazsın, sistem akıcı çalışır! 🚀