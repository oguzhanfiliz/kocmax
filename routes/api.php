<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\SliderController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DealerApplicationController;

/**
 * PayTR ödeme (checkout) rotalarını içe aktar (v1)
 */
require __DIR__ . '/api_v1_checkout.php';
use App\Http\Controllers\Api\PricingSystemController;
use App\Http\Controllers\Api\ContactController;

/**
 * API Rotaları
 *
 * Uygulamanızın API rotalarını burada tanımlayabilirsiniz. Bu rotalar
 * RouteServiceProvider tarafından yüklenir ve tamamı "api" middleware
 * grubuna atanır.
 */

/**
 * Tüm API rotaları için genel preflight (CORS) işleyicisi
 * (Origin yansıtma, kimlik bilgileri destekli)
 */
Route::middleware(['api', 'domain.cors'])->options('v1/{any}', function () {
    $origin = request()->header('Origin', '*');
    return response('', 204)
        ->header('Access-Control-Allow-Origin', $origin)
        ->header('Vary', 'Origin')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-API-Key, Origin, Accept')
        ->header('Access-Control-Allow-Credentials', 'true')
        ->header('Access-Control-Max-Age', '86400');
})->where('any', '.*');

/**
 * Kimlik Doğrulama API Rotaları
 */
Route::prefix('v1/auth')->middleware(['api', 'domain.cors', 'throttle:auth'])->group(function () {
    /**
     * Daha sıkı oran sınırlamasıyla herkese açık kimlik doğrulama rotaları
     */
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.auth.forgot-password');
    Route::get('/verify-reset-token', [AuthController::class, 'verifyResetToken'])->name('api.auth.verify-reset-token');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('api.auth.reset-password');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('api.auth.verify-email');
    /**
     * Doğrulama linkinden tıklama ile (GET) doğrulama desteği
     */
    Route::get('/verify-email', [AuthController::class, 'verifyEmail'])->name('api.auth.verify-email.get');
    Route::post('/resend-verification', [AuthController::class, 'resendVerification'])->name('api.auth.resend-verification');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('api.auth.refresh');
    
    /**
     * Korunan kimlik doğrulama rotaları
     */
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::get('/user', [AuthController::class, 'user'])->name('api.auth.user');
    });
});

/**
 * Kullanımdan kaldırıldı — bunun yerine /api/v1/auth/user kullanın
 */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Para Birimi API Rotaları (Korunan)
 */
Route::prefix('v1/currencies')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CurrencyController::class, 'index'])->name('api.currencies.index');
    Route::get('/rates', [CurrencyController::class, 'rates'])->name('api.currencies.rates');
    Route::post('/convert', [CurrencyController::class, 'convert'])->name('api.currencies.convert');
});

/**
 * Sepet API Rotaları (Korunan)
 */
Route::prefix('v1/cart')->middleware('auth:sanctum')->group(function () {
    /**
     * Sepet yönetim rotaları
     */
    Route::get('/', [CartController::class, 'show'])->name('api.cart.show');
    Route::post('/items', [CartController::class, 'addItem'])->name('api.cart.add-item');
    Route::put('/items/{item}', [CartController::class, 'updateItem'])->name('api.cart.update-item');
    Route::delete('/items/{item}', [CartController::class, 'removeItem'])->name('api.cart.remove-item');
    Route::delete('/', [CartController::class, 'clear'])->name('api.cart.clear');
    
    /**
     * Sepet özeti ve fiyatlandırma
     */
    Route::get('/summary', [CartController::class, 'summary'])->name('api.cart.summary');
    Route::post('/refresh-pricing', [CartController::class, 'refreshPricing'])->name('api.cart.refresh-pricing');
    
    /**
     * Sepet kampanyaları
     */
    Route::post('/apply-campaigns', [CartController::class, 'applyCampaigns'])->name('api.cart.apply-campaigns');
    
    /**
     * Misafir sepetinin oturum açmış kullanıcı sepetine taşınması
     */
    Route::post('/migrate', [CartController::class, 'migrate'])->name('api.cart.migrate');
});

/**
 * Sipariş API Rotaları (Korunan)
 */
Route::prefix('v1/orders')->middleware('auth:sanctum')->group(function () {
    /**
     * Tüm sipariş rotaları artık kimlik doğrulaması gerektirir
     * (güvenlik için misafir ödeme kaldırıldı)
     * Sipariş CRUD işlemleri
     */
    Route::get('/', [OrderController::class, 'index'])->name('api.orders.index');
    Route::post('/', [OrderController::class, 'store'])->name('api.orders.store');
    Route::get('/{order:order_number}', [OrderController::class, 'show'])->name('api.orders.show');
    Route::get('/{order:order_number}/tracking', [OrderController::class, 'tracking'])->name('api.orders.tracking');
    
    /**
     * Ödeme (checkout) işlemleri — artık kimlik doğrulaması ve sıkı oran sınırlaması gerektirir
     */
    Route::post('/checkout', [OrderController::class, 'guestCheckout'])->name('api.orders.checkout')
          ->middleware('throttle:checkout');
    Route::post('/estimate-checkout', [OrderController::class, 'estimateCheckout'])->name('api.orders.estimate-checkout')
          ->middleware('throttle:checkout');
    
    /**
     * Sipariş işlemleri
     */
    Route::patch('/{order:order_number}/status', [OrderController::class, 'updateStatus'])->name('api.orders.update-status');
    Route::post('/{order:order_number}/cancel', [OrderController::class, 'cancel'])->name('api.orders.cancel');
    Route::post('/{order:order_number}/payment', [OrderController::class, 'processPayment'])->name('api.orders.process-payment');
    
    /**
     * Ödeme sırasında ödeme işlemi — Frontend sepet ödeme akışı
     */
    Route::post('/checkout-payment', [OrderController::class, 'processCheckoutPayment'])
         ->name('api.orders.checkout-payment')
         ->middleware('throttle:checkout');
    
    /**
     * Kullanıcı sipariş özeti ve analitik
     */
    Route::get('/user/summary', [OrderController::class, 'summary'])->name('api.orders.summary');
});

/**
 * Müşteri Tipi API Rotaları (Alan adı korumalı — Public)
 */
Route::prefix('v1/customer')->middleware(['api', 'domain.cors', 'throttle:public'])->group(function () {
    /**
     * Müşteri tipinin tespiti — isteğe bağlı kimlik doğrulama
     */
    Route::get('/type', [CustomerController::class, 'type'])->name('api.customer.type');
});

/**
 * Fiyatlandırma Sistemi API Rotaları (Public)
 */
Route::prefix('v1/pricing')->middleware(['api', 'domain.cors', 'throttle:public'])->group(function () {
    /**
     * Fiyatlandırma sistemi uç noktaları
     */
    Route::get('/customer-types', [PricingSystemController::class, 'getCustomerTypes'])->name('api.pricing.customer-types');
    Route::get('/rules', [PricingSystemController::class, 'getPricingRules'])->name('api.pricing.rules');
    Route::get('/calculate', [PricingSystemController::class, 'calculatePricing'])->name('api.pricing.calculate');
});

/**
 * Ürün API Rotaları (Akıllı Fiyatlandırma için isteğe bağlı kimlik doğrulama ile Public)
 */
Route::prefix('v1/products')->middleware(['api', 'domain.cors', 'throttle:public', 'pricing.headers'])->group(function () {
    /**
     * Akıllı Fiyatlandırma: Kullanıcı giriş yapmışsa kişiselleştirilmiş fiyatlar gösterilir
     * Ürün kataloğu rotaları — isteğe bağlı kimlik doğrulama middleware'i ile
     */
    Route::get('/', [ProductController::class, 'index'])
         ->middleware('auth.optional')->name('api.products.index');
         
    /**
     * Bu rotalar tamamen herkese açıktır (kimlik doğrulama yok) — {product} rotasından önce gelmelidir
     */
    Route::get('/search-suggestions', [ProductController::class, 'searchSuggestions'])->name('api.products.search-suggestions');
    Route::get('/filters', [ProductController::class, 'filters'])->name('api.products.filters');
    Route::get('/variant-types', [ProductController::class, 'getVariantTypes'])->name('api.products.variant-types');
    
    /**
     * Ürün fiyatlandırma uç noktası — hem ID hem slug destekler
     */
    Route::get('/{product}/pricing', [ProductController::class, 'pricing'])
         ->middleware('auth.optional')
         ->where('product', '[0-9]+|[a-z0-9\-]+')
         ->name('api.products.pricing');
    
    /**
     * Ürün detay rotası — hem ID hem slug destekler — çakışmaları önlemek için en sonda olmalıdır
     */
    Route::get('/{product}', [ProductController::class, 'show'])
         ->middleware('auth.optional')
         ->where('product', '[0-9]+|[a-z0-9\-]+') // Hem ID (rakamlar) hem slug (harfler, rakamlar, tire) kabul edilir
         ->name('api.products.show');
});

/**
 * Yeni Ürünler API Rotaları (Optimizeli — Alan adı korumalı Public)
 */
Route::prefix('v1/newproducts')->middleware(['api', 'domain.cors', 'throttle:public', 'pricing.headers'])->group(function () {
    /**
     * Optimize edilmiş ürün listesi uç noktası (geliştirme testi için)
     */
    Route::get('/', [ProductController::class, 'newProducts'])
         ->middleware('auth.optional')->name('api.newproducts.index');
});

/**
 * Kategori API Rotaları (Alan adı korumalı — Public)
 */
Route::prefix('v1/categories')->middleware(['api', 'domain.cors', 'throttle:public'])->group(function () {
    /**
     * Herkese açık kategori gezinme rotaları
     */
    Route::get('/', [CategoryController::class, 'index'])->name('api.categories.index');
    Route::get('/menu', [CategoryController::class, 'menu'])->name('api.categories.menu');
    Route::get('/featured', [CategoryController::class, 'featured'])->name('api.categories.featured');
    Route::get('/tree', [CategoryController::class, 'tree'])->name('api.categories.tree');
    Route::get('/breadcrumb/{id}', [CategoryController::class, 'breadcrumb'])->name('api.categories.breadcrumb')
          ->where('id', '[0-9]+');
    Route::get('/{id}', [CategoryController::class, 'show'])->name('api.categories.show')
          ->where('id', '[0-9]+');
    Route::get('/{id}/products', [CategoryController::class, 'products'])->name('api.categories.products')
          ->where('id', '[0-9]+');
});

/**
 * Para Birimi API Rotaları (Public)
 */
Route::prefix('v1/currencies')->middleware(['api', 'domain.cors', 'throttle:public'])->group(function () {
    /**
     * Herkese açık para birimi rotaları
     */
    Route::get('/', [CurrencyController::class, 'index'])->name('api.currencies.index');
    Route::get('/default', [CurrencyController::class, 'default'])->name('api.currencies.default');
    Route::get('/rates', [CurrencyController::class, 'rates'])->name('api.currencies.rates');
    Route::post('/convert', [CurrencyController::class, 'convert'])->name('api.currencies.convert');
    Route::get('/{code}', [CurrencyController::class, 'show'])->name('api.currencies.show')
          ->where('code', '[A-Z]{3}');
});

/**
 * Kullanıcı Profili API Rotaları (Korunan)
 */
Route::prefix('v1/users')->middleware('auth:sanctum')->group(function () {
    /**
     * Kullanıcı profili yönetimi
     */
    Route::get('/profile', [UserController::class, 'profile'])->name('api.users.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('api.users.update-profile');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('api.users.change-password');
    
    /**
     * Avatar yönetimi
     */
    Route::post('/upload-avatar', [UserController::class, 'uploadAvatar'])->name('api.users.upload-avatar');
    Route::put('/avatar', [UserController::class, 'uploadAvatar'])->name('api.users.update-avatar'); // RESTful alias
    Route::post('/avatar', [UserController::class, 'uploadAvatar'])->name('api.users.post-avatar'); // Frontend compatibility
    Route::delete('/avatar', [UserController::class, 'deleteAvatar'])->name('api.users.delete-avatar');
    
    /**
     * Bayi başvurusu yönetimi — kullanımdan kaldırıldı, /dealer-applications uç noktasını kullanın
     */
    Route::get('/dealer-status', [UserController::class, 'dealerStatus'])->name('api.users.dealer-status');
    Route::post('/dealer-application', [UserController::class, 'submitDealerApplication'])->name('api.users.dealer-application');
});

/**
 * Bayi Başvurusu API Rotaları
 */
Route::prefix('v1/dealer-applications')->middleware(['api', 'domain.cors'])->group(function () {
    /**
     * Herkese açık bayi başvurusu uç noktası (kullanıcı kaydını içerir)
     */
    Route::post('/', [DealerApplicationController::class, 'store'])
         ->name('api.dealer-applications.store');
    
    /**
     * Başvuru yapılabilir mi kontrolü (herkese açık uç nokta)
     */
    Route::get('/can-apply', [DealerApplicationController::class, 'canApply'])
         ->name('api.dealer-applications.can-apply');
    
    /**
     * Durum referans verilerini getir (herkese açık uç nokta)
     */
    Route::get('/statuses', [DealerApplicationController::class, 'statuses'])
         ->name('api.dealer-applications.statuses');
    
    /**
      * Korunan bayi başvurusu uç noktaları
      */
    Route::middleware('auth:sanctum')->group(function () {
        /**
         * Mevcut kullanıcının bayi başvuru durumunu getir
         */
        Route::get('/', [DealerApplicationController::class, 'index'])
             ->name('api.dealer-applications.index');
        
        /**
         * Belirli bayi başvuru detaylarını getir
         */
        Route::get('/{dealerApplication}', [DealerApplicationController::class, 'show'])
             ->name('api.dealer-applications.show');
    });
});

/**
 * Adres Yönetimi API Rotaları (Korunan)
 */
Route::prefix('v1/addresses')->middleware('auth:sanctum')->group(function () {
    /**
     * Varsayılan adres yönetimi (parametrik rotalardan önce gelmelidir)
     */
    Route::get('/defaults', [AddressController::class, 'getDefaults'])->name('api.addresses.defaults');
    
    /**
     * Adres CRUD işlemleri
     */
    Route::get('/', [AddressController::class, 'index'])->name('api.addresses.index');
    Route::post('/', [AddressController::class, 'store'])->name('api.addresses.store');
    Route::get('/{address}', [AddressController::class, 'show'])->name('api.addresses.show');
    Route::put('/{address}', [AddressController::class, 'update'])->name('api.addresses.update');
    Route::delete('/{address}', [AddressController::class, 'destroy'])->name('api.addresses.destroy');
    
    /**
     * Adres varsayılan yönetimi
     */
    Route::post('/{address}/set-default-shipping', [AddressController::class, 'setDefaultShipping'])->name('api.addresses.set-default-shipping');
    Route::post('/{address}/set-default-billing', [AddressController::class, 'setDefaultBilling'])->name('api.addresses.set-default-billing');
});

/**
 * Favori Listesi (Wishlist) API Rotaları (Korunan)
 */
Route::prefix('v1/wishlist')->middleware('auth:sanctum')->group(function () {
    /**
     * Favori istatistikleri ({wishlist} rotalarından önce gelmelidir)
     */
    Route::get('/stats', [WishlistController::class, 'stats'])->name('api.wishlist.stats');
    
    /**
     * Favori listesi CRUD işlemleri
     */
    Route::get('/', [WishlistController::class, 'index'])->name('api.wishlist.index');
    Route::post('/', [WishlistController::class, 'store'])->name('api.wishlist.store');
    Route::get('/{wishlist}', [WishlistController::class, 'show'])->name('api.wishlist.show');
    Route::put('/{wishlist}', [WishlistController::class, 'update'])->name('api.wishlist.update');
    Route::delete('/{wishlist}', [WishlistController::class, 'destroy'])->name('api.wishlist.destroy');
    
    /**
     * Favori listesi işlemleri
     */
    Route::post('/{wishlist}/toggle-favorite', [WishlistController::class, 'toggleFavorite'])->name('api.wishlist.toggle-favorite');
    Route::delete('/clear', [WishlistController::class, 'clear'])->name('api.wishlist.clear');
});

/**
 * Kampanya API Rotaları (Korunan)
 */
Route::prefix('v1/campaigns')->middleware(['auth:sanctum', 'throttle:campaigns'])->group(function () {
    /**
     * Tüm kampanya rotaları kimlik doğrulaması ve özel oran sınırlaması gerektirir
     */
    Route::get('/', [CampaignController::class, 'index'])->name('api.campaigns.index');
    Route::get('/available', [CampaignController::class, 'available'])->name('api.campaigns.available');
    Route::get('/{campaign}', [CampaignController::class, 'show'])->name('api.campaigns.show')
          ->where('campaign', '[A-Za-z0-9\-_]+'); // ID veya slug
    Route::post('/{campaign}/validate', [CampaignController::class, 'validateCampaign'])->name('api.campaigns.validate')
          ->where('campaign', '[0-9]+'); // Yalnızca doğrulama için sayısal ID'ler
});

/**
 * Kupon API Rotaları (Korunan)
 */
Route::prefix('v1/coupons')->middleware('auth:sanctum')->group(function () {
    /**
     * Tüm kupon rotaları artık kimlik doğrulaması gerektirir
     */
    Route::post('/validate', [CouponController::class, 'validateCoupon'])->name('api.coupons.validate');
    Route::get('/public', [CouponController::class, 'publicCoupons'])->name('api.coupons.public');
    Route::post('/apply', [CouponController::class, 'apply'])->name('api.coupons.apply');
    Route::get('/my-coupons', [CouponController::class, 'myCoupons'])->name('api.coupons.my-coupons');
});

/**
 * Ayarlar API Rotaları (Alan adı korumalı — Public)
 */
Route::prefix('v1/settings')->middleware(['api', 'throttle:public'])->group(function () {
    /**
     * Herkese açık ayar rotaları — frontend kimlik doğrulaması olmadan erişilebilir
     */
    Route::get('/', [SettingController::class, 'index'])->name('api.settings.index');
    Route::get('/grouped', [SettingController::class, 'grouped'])->name('api.settings.grouped');
    Route::get('/essential', [SettingController::class, 'essential'])->name('api.settings.essential');
    Route::get('/features', [SettingController::class, 'features'])->name('api.settings.features');
    Route::get('/{key}', [SettingController::class, 'show'])->name('api.settings.show')
          ->where('key', '[a-zA-Z0-9_\-]+');
});

/**
 * Slider API Rotaları (Alan adı korumalı — Public)
 */
/**
 * Dosya Erişim Rotaları (Korunan)
 */
Route::prefix('v1/files')->middleware(['api', 'auth:sanctum'])->group(function () {
    Route::get('/{path}', [App\Http\Controllers\Api\FileController::class, 'show'])
         ->where('path', '.*')
         ->name('api.files.show');
});

Route::prefix('v1/sliders')->middleware(['api', 'domain.cors', 'throttle:public'])->group(function () {
    /**
     * Herkese açık slider rotaları — frontend kimlik doğrulaması olmadan erişilebilir
     */
    Route::get('/', [SliderController::class, 'index'])->name('api.sliders.index');
    Route::get('/{slider}', [SliderController::class, 'show'])->name('api.sliders.show')
          ->where('slider', '[0-9]+');
});

/**
 * Arama API Rotaları (Alan adı korumalı — Public)
 */
Route::prefix('v1/search')->middleware(['api', 'domain.cors', 'throttle:public'])->group(function () {
    /**
     * Herkese açık arama rotaları — frontend kimlik doğrulaması olmadan erişilebilir
     */
    Route::get('/popular', [SearchController::class, 'popular'])->name('api.search.popular');
    Route::get('/autocomplete', [SearchController::class, 'autocomplete'])->name('api.search.autocomplete');
    Route::post('/record', [SearchController::class, 'record'])->name('api.search.record');
});

/**
 * İletişim API Rotaları (Alan adı korumalı — oran sınırlamalı Public)
 */
Route::prefix('v1/contact')->middleware(['api', 'domain.cors', 'throttle:contact'])->group(function () {
    /**
     * İletişim formu gönderimi — sıkı oran sınırlamalı herkese açık uç nokta
     */
    Route::post('/', [ContactController::class, 'store'])->name('api.contact.store');
});
