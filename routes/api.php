<?php

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

Route::middleware('auth:sanctum', VerifyAdmin::class)->get('/users', function (Request $request) {
    return User::all();
});

Route::prefix('auth')->group(static function () {
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(static function () {
        Route::get('/check', [\App\Http\Controllers\AuthController::class, 'check']);
        Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
        Route::get('/tokens', [\App\Http\Controllers\AuthController::class, 'getTokens']);
        Route::delete('/tokens/{id}', [\App\Http\Controllers\AuthController::class, 'deleteToken']);
    });
});
