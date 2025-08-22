<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

// Frontend Route - Serve Nuxt app
Route::get('/', function () {
    // Development: Redirect to Nuxt dev server
    if (config('app.debug')) {
        return redirect('http://localhost:3000');
    }
    
    // Production: Serve built frontend
    $indexPath = public_path('frontend/index.html');
    if (file_exists($indexPath)) {
        return response()->file($indexPath);
    }
    
    return 'Frontend build not found. Run build script first.';
});

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
    $allowedOrigins = ['http://localhost:3000', 'http://localhost:5173', 'http://127.0.0.1:3000'];
    
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
    $allowedOrigins = ['http://localhost:3000', 'http://localhost:5173', 'http://127.0.0.1:3000'];
    
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

/*
|--------------------------------------------------------------------------
| Frontend SPA Routes - Serve Nuxt App
|--------------------------------------------------------------------------
*/

// Catch all routes that aren't API routes and serve the frontend
Route::get('/{any}', function () {
    // Development: Redirect to Nuxt dev server
    if (config('app.debug')) {
        return redirect('http://localhost:3000/' . request()->path());
    }
    
    // Production: Serve built frontend index.html for SPA routing
    $indexPath = public_path('frontend/index.html');
    if (file_exists($indexPath)) {
        return response()->file($indexPath);
    }
    
    return 'Frontend build not found. Run build script first.';
})->where('any', '^(?!api|admin|storage).*$'); // Exclude API, admin, and storage routes


