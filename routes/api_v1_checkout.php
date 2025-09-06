<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\PayTrCallbackController;

/*
|--------------------------------------------------------------------------
| Checkout & Payment API Routes
|--------------------------------------------------------------------------
|
| PayTR entegrasyonu ile güvenli checkout API rotaları
| Tüm fiyat hesaplamaları backend'de yapılır (frontend manipülyasyon koruması)
|
*/

// Güvenli Checkout Routes (Authentication Required) - v1 API
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:checkout'])->group(function () {
    
    // Checkout Process
    Route::prefix('checkout')->group(function () {
        Route::post('initialize', [CheckoutController::class, 'initialize'])
            ->name('checkout.initialize');
            
        Route::get('session/{sessionId}', [CheckoutController::class, 'getSession'])
            ->name('checkout.session.get');
            
        Route::delete('session/{sessionId}', [CheckoutController::class, 'cancelSession'])
            ->name('checkout.session.cancel');
    });
    
    // Payment Process  
    Route::prefix('checkout/payment')->group(function () {
        Route::post('initialize', [CheckoutController::class, 'initializePayment'])
            ->name('checkout.payment.initialize');
    });
});

// PayTR Webhook (No Authentication - PayTR calls this)
Route::prefix('webhooks/paytr')->group(function () {
    Route::post('callback', [PayTrCallbackController::class, 'handle'])
        ->middleware('throttle:60,1') // 60 calls per minute
        ->name('paytr.callback');
        
    // Test endpoint (local environment only)
    Route::post('test', [PayTrCallbackController::class, 'test'])
        ->name('paytr.callback.test');
});

// Rate Limiting Aliases
Route::aliasMiddleware('throttle:checkout', 'throttle:' . config('payments.rate_limiting.checkout_attempts_per_minute', 10) . ',1');