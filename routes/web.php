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

Route::get('/', function () {
    return 'Yeni projeniz hazır!';
});

// Simple login info page for web requests (API-only application)
Route::get('/login-required', function () {
    return response()->json([
        'message' => 'Authentication required. This is an API-only application.',
        'api_docs' => '/docs/api',
        'login_endpoint' => '/api/v1/auth/login'
    ], 401);
})->name('login.required');

