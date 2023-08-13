<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->prefix('auth')->group(static function () {
    Route::get('/check', [\App\Http\Controllers\AuthController::class, 'check']);
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
});

Route::prefix('auth')->group(static function () {
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
    // Route::get('/check', [\App\Http\Controllers\AuthController::class, 'check']);
});
