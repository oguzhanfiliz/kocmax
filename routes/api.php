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

/*
|--------------------------------------------------------------------------
| Authentication API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1/auth')->group(function () {
    // Public authentication routes
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
| Currency API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1/currencies')->group(function () {
    Route::get('/', [CurrencyController::class, 'index'])->name('api.currencies.index');
    Route::get('/rates', [CurrencyController::class, 'rates'])->name('api.currencies.rates');
    Route::post('/convert', [CurrencyController::class, 'convert'])->name('api.currencies.convert');
});

/*
|--------------------------------------------------------------------------
| Cart API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1/cart')->group(function () {
    // Cart management routes
    Route::get('/', [CartController::class, 'show'])->name('api.cart.show');
    Route::post('/items', [CartController::class, 'addItem'])->name('api.cart.add-item');
    Route::put('/items/{item}', [CartController::class, 'updateItem'])->name('api.cart.update-item');
    Route::delete('/items/{item}', [CartController::class, 'removeItem'])->name('api.cart.remove-item');
    Route::delete('/', [CartController::class, 'clear'])->name('api.cart.clear');
    
    // Cart summary and pricing
    Route::get('/summary', [CartController::class, 'summary'])->name('api.cart.summary');
    Route::post('/refresh-pricing', [CartController::class, 'refreshPricing'])->name('api.cart.refresh-pricing');
    
    // Cart migration for guest to authenticated user
    Route::post('/migrate', [CartController::class, 'migrate'])
        ->middleware('auth:sanctum')
        ->name('api.cart.migrate');
});

/*
|--------------------------------------------------------------------------
| Order API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1/orders')->group(function () {
    // Guest checkout (no authentication required)
    Route::post('/guest-checkout', [OrderController::class, 'guestCheckout'])->name('api.orders.guest-checkout');
    
    // Checkout estimation (can be used by guests or authenticated users)
    Route::post('/estimate-checkout', [OrderController::class, 'estimateCheckout'])->name('api.orders.estimate-checkout');
    
    // Public order tracking (no authentication required, but requires order access validation)
    Route::get('/{order:order_number}/tracking', [OrderController::class, 'tracking'])->name('api.orders.tracking');
    
    // Authenticated user routes
    Route::middleware('auth:sanctum')->group(function () {
        // Order CRUD operations
        Route::get('/', [OrderController::class, 'index'])->name('api.orders.index');
        Route::post('/', [OrderController::class, 'store'])->name('api.orders.store');
        Route::get('/{order}', [OrderController::class, 'show'])->name('api.orders.show');
        
        // Order actions
        Route::patch('/{order}/status', [OrderController::class, 'updateStatus'])->name('api.orders.update-status');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('api.orders.cancel');
        Route::post('/{order}/payment', [OrderController::class, 'processPayment'])->name('api.orders.process-payment');
        
        // Order summary and analytics
        Route::get('/user/summary', [OrderController::class, 'summary'])->name('api.orders.summary');
    });
});

/*
|--------------------------------------------------------------------------
| Product API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1/products')->group(function () {
    // Public product routes
    Route::get('/', [ProductController::class, 'index'])->name('api.products.index');
    Route::get('/search-suggestions', [ProductController::class, 'searchSuggestions'])->name('api.products.search-suggestions');
    Route::get('/filters', [ProductController::class, 'filters'])->name('api.products.filters');
    Route::get('/{product}', [ProductController::class, 'show'])->name('api.products.show');
});

/*
|--------------------------------------------------------------------------
| Category API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1/categories')->group(function () {
    // Public category routes
    Route::get('/', [CategoryController::class, 'index'])->name('api.categories.index');
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
    // Address CRUD operations
    Route::get('/', [AddressController::class, 'index'])->name('api.addresses.index');
    Route::post('/', [AddressController::class, 'store'])->name('api.addresses.store');
    Route::get('/{address}', [AddressController::class, 'show'])->name('api.addresses.show');
    Route::put('/{address}', [AddressController::class, 'update'])->name('api.addresses.update');
    Route::delete('/{address}', [AddressController::class, 'destroy'])->name('api.addresses.destroy');
    
    // Default address management
    Route::get('/defaults', [AddressController::class, 'getDefaults'])->name('api.addresses.defaults');
    Route::post('/{address}/set-default-shipping', [AddressController::class, 'setDefaultShipping'])->name('api.addresses.set-default-shipping');
    Route::post('/{address}/set-default-billing', [AddressController::class, 'setDefaultBilling'])->name('api.addresses.set-default-billing');
});
