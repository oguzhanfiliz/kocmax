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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Global preflight handler for all API routes (reflect Origin, allow credentials)
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

/*
|--------------------------------------------------------------------------
| Authentication API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1/auth')->middleware(['api', 'domain.cors', 'throttle:auth'])->group(function () {
    // Public authentication routes with stricter rate limiting
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('api.auth.forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('api.auth.reset-password');
    Route::post('/verify-email', [AuthController::class, 'verifyEmail'])->name('api.auth.verify-email');
    Route::post('/resend-verification', [AuthController::class, 'resendVerification'])->name('api.auth.resend-verification');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('api.auth.refresh');
    
    // Protected authentication routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::get('/user', [AuthController::class, 'user'])->name('api.auth.user');
    });
});

// Deprecated - use /api/v1/auth/user instead
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Currency API Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/currencies')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CurrencyController::class, 'index'])->name('api.currencies.index');
    Route::get('/rates', [CurrencyController::class, 'rates'])->name('api.currencies.rates');
    Route::post('/convert', [CurrencyController::class, 'convert'])->name('api.currencies.convert');
});

/*
|--------------------------------------------------------------------------
| Cart API Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/cart')->middleware('auth:sanctum')->group(function () {
    // Cart management routes
    Route::get('/', [CartController::class, 'show'])->name('api.cart.show');
    Route::post('/items', [CartController::class, 'addItem'])->name('api.cart.add-item');
    Route::put('/items/{item}', [CartController::class, 'updateItem'])->name('api.cart.update-item');
    Route::delete('/items/{item}', [CartController::class, 'removeItem'])->name('api.cart.remove-item');
    Route::delete('/', [CartController::class, 'clear'])->name('api.cart.clear');
    
    // Cart summary and pricing
    Route::get('/summary', [CartController::class, 'summary'])->name('api.cart.summary');
    Route::post('/refresh-pricing', [CartController::class, 'refreshPricing'])->name('api.cart.refresh-pricing');
    
    // Cart campaigns
    Route::post('/apply-campaigns', [CartController::class, 'applyCampaigns'])->name('api.cart.apply-campaigns');
    
    // Cart migration from guest to authenticated user
    Route::post('/migrate', [CartController::class, 'migrate'])->name('api.cart.migrate');
});

/*
|--------------------------------------------------------------------------
| Order API Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/orders')->middleware('auth:sanctum')->group(function () {
    // All order routes now require authentication (guest checkout removed for security)
    // Order CRUD operations
    Route::get('/', [OrderController::class, 'index'])->name('api.orders.index');
    Route::post('/', [OrderController::class, 'store'])->name('api.orders.store');
    Route::get('/{order}', [OrderController::class, 'show'])->name('api.orders.show');
    Route::get('/{order:order_number}/tracking', [OrderController::class, 'tracking'])->name('api.orders.tracking');
    
    // Checkout operations (now require authentication with strict rate limiting)
    Route::post('/checkout', [OrderController::class, 'guestCheckout'])->name('api.orders.checkout')
          ->middleware('throttle:checkout'); // Renamed from guest-checkout
    Route::post('/estimate-checkout', [OrderController::class, 'estimateCheckout'])->name('api.orders.estimate-checkout')
          ->middleware('throttle:checkout');
    
    // Order actions
    Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('api.orders.update-status');
    Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('api.orders.cancel');
    Route::post('/{order}/payment', [OrderController::class, 'processPayment'])->name('api.orders.process-payment');
    
    // Order summary and analytics
    Route::get('/user/summary', [OrderController::class, 'summary'])->name('api.orders.summary');
});

/*
|--------------------------------------------------------------------------
| Customer Type API Routes (Public with Domain Protection)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/customer')->middleware(['api', 'domain.cors', 'throttle:public'])->group(function () {
    // Customer type detection - optional auth
    Route::get('/type', [CustomerController::class, 'type'])->name('api.customer.type');
});

/*
|--------------------------------------------------------------------------
| Product API Routes (Public with Optional Auth for Smart Pricing)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/products')->middleware(['api', 'domain.cors', 'throttle:public'])->group(function () {
    // 🎯 Smart Pricing: Optional authentication - if user is logged in, show personalized prices
    // Public product catalog routes with optional auth middleware
    Route::get('/', [ProductController::class, 'index'])
         ->middleware('auth.optional')->name('api.products.index');
         
    // These routes remain fully public (no auth needed) - MUST come before {product} route
    Route::get('/search-suggestions', [ProductController::class, 'searchSuggestions'])->name('api.products.search-suggestions');
    Route::get('/filters', [ProductController::class, 'filters'])->name('api.products.filters');
    
    // Product pricing endpoint - supports both ID and slug
    Route::get('/{product}/pricing', [ProductController::class, 'pricing'])
         ->middleware('auth.optional')
         ->where('product', '[0-9]+|[a-z0-9\-]+')
         ->name('api.products.pricing');
    
    // Product detail route - supports both ID and slug - MUST come last to avoid conflicts
    Route::get('/{product}', [ProductController::class, 'show'])
         ->middleware('auth.optional')
         ->where('product', '[0-9]+|[a-z0-9\-]+') // Accepts both ID (numbers) and slug (letters, numbers, hyphens)
         ->name('api.products.show');
});

/*
|--------------------------------------------------------------------------
| Category API Routes (Public with Domain Protection)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/categories')->middleware(['api', 'domain.cors', 'throttle:public'])->group(function () {
    // Public category navigation routes
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

/*
|--------------------------------------------------------------------------
| Currency API Routes (Public)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/currencies')->middleware(['api', 'domain.cors', 'throttle:public'])->group(function () {
    // Public currency routes
    Route::get('/', [CurrencyController::class, 'index'])->name('api.currencies.index');
    Route::get('/default', [CurrencyController::class, 'default'])->name('api.currencies.default');
    Route::get('/rates', [CurrencyController::class, 'rates'])->name('api.currencies.rates');
    Route::post('/convert', [CurrencyController::class, 'convert'])->name('api.currencies.convert');
    Route::get('/{code}', [CurrencyController::class, 'show'])->name('api.currencies.show')
          ->where('code', '[A-Z]{3}');
});

/*
|--------------------------------------------------------------------------
| User Profile API Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/users')->middleware('auth:sanctum')->group(function () {
    // User profile management
    Route::get('/profile', [UserController::class, 'profile'])->name('api.users.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('api.users.update-profile');
    Route::post('/change-password', [UserController::class, 'changePassword'])->name('api.users.change-password');
    
    // Avatar management
    Route::post('/upload-avatar', [UserController::class, 'uploadAvatar'])->name('api.users.upload-avatar');
    Route::put('/avatar', [UserController::class, 'uploadAvatar'])->name('api.users.update-avatar'); // RESTful alias
    Route::post('/avatar', [UserController::class, 'uploadAvatar'])->name('api.users.post-avatar'); // Frontend compatibility
    Route::delete('/avatar', [UserController::class, 'deleteAvatar'])->name('api.users.delete-avatar');
    
    // Dealer application management
    Route::get('/dealer-status', [UserController::class, 'dealerStatus'])->name('api.users.dealer-status');
    Route::post('/dealer-application', [UserController::class, 'submitDealerApplication'])->name('api.users.dealer-application');
});

/*
|--------------------------------------------------------------------------
| Address Management API Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/addresses')->middleware('auth:sanctum')->group(function () {
    // Default address management (must come before parametric routes)
    Route::get('/defaults', [AddressController::class, 'getDefaults'])->name('api.addresses.defaults');
    
    // Address CRUD operations
    Route::get('/', [AddressController::class, 'index'])->name('api.addresses.index');
    Route::post('/', [AddressController::class, 'store'])->name('api.addresses.store');
    Route::get('/{address}', [AddressController::class, 'show'])->name('api.addresses.show');
    Route::put('/{address}', [AddressController::class, 'update'])->name('api.addresses.update');
    Route::delete('/{address}', [AddressController::class, 'destroy'])->name('api.addresses.destroy');
    
    // Address default management
    Route::post('/{address}/set-default-shipping', [AddressController::class, 'setDefaultShipping'])->name('api.addresses.set-default-shipping');
    Route::post('/{address}/set-default-billing', [AddressController::class, 'setDefaultBilling'])->name('api.addresses.set-default-billing');
});

/*
|--------------------------------------------------------------------------
| Wishlist API Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/wishlist')->middleware('auth:sanctum')->group(function () {
    // Wishlist statistics (must be before {wishlist} routes)
    Route::get('/stats', [WishlistController::class, 'stats'])->name('api.wishlist.stats');
    
    // Wishlist CRUD operations
    Route::get('/', [WishlistController::class, 'index'])->name('api.wishlist.index');
    Route::post('/', [WishlistController::class, 'store'])->name('api.wishlist.store');
    Route::get('/{wishlist}', [WishlistController::class, 'show'])->name('api.wishlist.show');
    Route::put('/{wishlist}', [WishlistController::class, 'update'])->name('api.wishlist.update');
    Route::delete('/{wishlist}', [WishlistController::class, 'destroy'])->name('api.wishlist.destroy');
    
    // Wishlist actions
    Route::post('/{wishlist}/toggle-favorite', [WishlistController::class, 'toggleFavorite'])->name('api.wishlist.toggle-favorite');
    Route::delete('/clear', [WishlistController::class, 'clear'])->name('api.wishlist.clear');
});

/*
|--------------------------------------------------------------------------
| Campaign API Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/campaigns')->middleware(['auth:sanctum', 'throttle:campaigns'])->group(function () {
    // All campaign routes now require authentication with specific rate limiting
    Route::get('/', [CampaignController::class, 'index'])->name('api.campaigns.index');
    Route::get('/{campaign}', [CampaignController::class, 'show'])->name('api.campaigns.show')
          ->where('campaign', '[A-Za-z0-9\-_]+'); // ID or slug
    Route::post('/{campaign}/validate', [CampaignController::class, 'validateCampaign'])->name('api.campaigns.validate')
          ->where('campaign', '[0-9]+'); // Only numeric IDs for validation
});

/*
|--------------------------------------------------------------------------
| Coupon API Routes (Protected)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/coupons')->middleware('auth:sanctum')->group(function () {
    // All coupon routes now require authentication
    Route::post('/validate', [CouponController::class, 'validateCoupon'])->name('api.coupons.validate');
    Route::get('/public', [CouponController::class, 'publicCoupons'])->name('api.coupons.public');
    Route::post('/apply', [CouponController::class, 'apply'])->name('api.coupons.apply');
    Route::get('/my-coupons', [CouponController::class, 'myCoupons'])->name('api.coupons.my-coupons');
});

/*
|--------------------------------------------------------------------------
| Settings API Routes (Public with Domain Protection)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/settings')->middleware(['api', 'throttle:public'])->group(function () {
    // Public settings routes - accessible by frontend without authentication
    Route::get('/', [SettingController::class, 'index'])->name('api.settings.index');
    Route::get('/grouped', [SettingController::class, 'grouped'])->name('api.settings.grouped');
    Route::get('/essential', [SettingController::class, 'essential'])->name('api.settings.essential');
    Route::get('/features', [SettingController::class, 'features'])->name('api.settings.features');
    Route::get('/{key}', [SettingController::class, 'show'])->name('api.settings.show')
          ->where('key', '[a-zA-Z0-9_\-]+');
});

/*
|--------------------------------------------------------------------------
| Slider API Routes (Public with Domain Protection)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/sliders')->middleware(['api', 'domain.cors', 'throttle:public'])->group(function () {
    // Public slider routes - accessible by frontend without authentication
    Route::get('/', [SliderController::class, 'index'])->name('api.sliders.index');
    Route::get('/{slider}', [SliderController::class, 'show'])->name('api.sliders.show')
          ->where('slider', '[0-9]+');
});

/*
|--------------------------------------------------------------------------
| Search API Routes (Public with Domain Protection)
|--------------------------------------------------------------------------
*/
Route::prefix('v1/search')->middleware(['api', 'domain.cors', 'throttle:public'])->group(function () {
    // Public search routes - accessible by frontend without authentication
    Route::get('/popular', [SearchController::class, 'popular'])->name('api.search.popular');
    Route::get('/autocomplete', [SearchController::class, 'autocomplete'])->name('api.search.autocomplete');
    Route::post('/record', [SearchController::class, 'record'])->name('api.search.record');
});
