<?php

use Illuminate\Support\Facades\Route;

// Admin dosya indirme (Filament oturumuyla korunur)
Route::middleware(['web', 'auth'])
    ->get('/admin/files/{path}', [\App\Http\Controllers\Admin\AdminFileController::class, 'show'])
    ->where('path', '.*')
    ->name('admin.files.show');

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Bu Laravel uygulaması sadece API ve Admin Panel sunuyor
// Frontend ayrı domain'de (kocmax.mutfakyapim.net) çalışacak

// Simple login info page for web requests (API-only application)
Route::get('/login-required', function () {
    return response()->json([
        'message' => 'Authentication required. This is an API-only application.',
        'api_docs' => '/docs/api',
        'login_endpoint' => '/api/v1/auth/login'
    ], 401);
})->name('login.required');

/*
|--------------------------------------------------------------------------
| CORS-enabled Storage Route
|--------------------------------------------------------------------------
*/
// OPTIONS handler for preflight requests
Route::options('storage/{path}', function (Request $request) {
    $origin = $request->header('Origin');
    $allowedOrigins = [
        'http://localhost:3000', 
        'http://localhost:5173', 
        'http://127.0.0.1:3000',
        'https://b2bb2c.mutfakyapim.net', // Production domain
        'https://b2bb2c-frontend.vercel.app' // Frontend domain
    ];
    
    $response = response('', 200);
    
    if ($origin && in_array($origin, $allowedOrigins)) {
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept');
        $response->headers->set('Access-Control-Max-Age', '3600');
    }
    
    return $response;
})->where('path', '.*');

Route::get('storage/{path}', function (Request $request, $path) {
    // Handle nested path like sliders/image.png
    $file = storage_path('app/public/' . $path);
    
    if (!file_exists($file)) {
        return response()->json(['error' => 'File not found: ' . $path], 404);
    }
    
    $origin = $request->header('Origin');
    $allowedOrigins = [
        'http://localhost:3000', 
        'http://localhost:5173', 
        'http://127.0.0.1:3000',
        'https://b2bb2c.mutfakyapim.net', // Production domain
        'https://b2bb2c-frontend.vercel.app' // Frontend domain
    ];
    
    // Get the file info
    $mimeType = mime_content_type($file);
    $fileSize = filesize($file);
    
    // Create response with proper headers
    $response = response()->file($file, [
        'Content-Type' => $mimeType,
        'Content-Length' => $fileSize,
    ]);
    
    // Add CORS headers if origin is allowed
    if ($origin && in_array($origin, $allowedOrigins)) {
        $response->headers->set('Access-Control-Allow-Origin', $origin);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept');
        $response->headers->set('Access-Control-Max-Age', '3600');
    }
    
    // Add cache headers
    $response->headers->set('Cache-Control', 'public, max-age=3600');
    
    return $response;
})->where('path', '.*');

// Email Verification Routes (guest-friendly, signed URL doğrulaması)
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    // İmza geçerliliğini kontrol et
    if (!URL::hasValidSignature($request)) {
        return response()->json(['message' => 'Invalid or expired verification link'], 403);
    }

    $user = User::findOrFail($id);

    // Hash doğrulaması (Laravel default: sha1(email))
    if (! hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
        return response()->json(['message' => 'Invalid verification hash'], 403);
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new Verified($user));
    }

    $redirectUrl = config('app.frontend_url', 'https://kocmax.tr') . '/email-verified';
    return response()->view('auth.email-verified', ['redirectUrl' => $redirectUrl]);
})->middleware(['web', 'signed', 'throttle:6,1'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    // Doğrulama e-postasını tekrar gönder
    if ($request->user()) {
        $request->user()->sendEmailVerificationNotification();
    }
    return response()->json(['message' => 'verification-link-sent']);
})->middleware(['web', 'auth', 'throttle:6,1'])->name('verification.send');

/*
|--------------------------------------------------------------------------
| API ve Admin Panel Uygulaması
|--------------------------------------------------------------------------
| 
| Bu Laravel uygulaması sadece API ve Admin Panel hizmeti sunuyor.
| Frontend ayrı domain'de (kocmax.mutfakyapim.net) çalışıyor.
|*/


// Ana sayfayı Filament Admin giriş ekranına yönlendir
Route::get('/', function () {
    return redirect('/admin/login');
});


