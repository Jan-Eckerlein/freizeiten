<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\VerifyAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Admin
Route::middleware(['auth:sanctum', AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/users', static function () {
        return User::all();
    });
    Route::resource('organizations', \App\Http\Controllers\Admin\OrganizationAdminController::class);
});

// Auth
Route::prefix('auth')->group(static function () {
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(static function () {
        Route::get('/check', [\App\Http\Controllers\AuthController::class, 'check']);
        Route::get('/tokens', [\App\Http\Controllers\AuthController::class, 'getTokens']);
        Route::delete('/tokens/{id}', [\App\Http\Controllers\AuthController::class, 'deleteToken']);
    });
});

// Organization
Route::middleware(['auth:sanctum'])->prefix('organizations')->group(static function () {
    Route::get('/', [\App\Http\Controllers\OrganizationController::class, 'index']);
    Route::get('/{id}', [\App\Http\Controllers\OrganizationController::class, 'show']);
    Route::put('/{id}', [\App\Http\Controllers\OrganizationController::class, 'update']);
    Route::delete('/{id}', [\App\Http\Controllers\OrganizationController::class, 'destroy']);
});
