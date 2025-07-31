<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
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
