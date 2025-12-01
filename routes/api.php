<?php

use App\Http\Controllers\Api\AntiqueController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;
use Illuminate\Http\Request;

// Public API routes
Route::post('register', [RegisterController::class, 'register']);
Route::post('login', [RegisterController::class, 'login']);

// Antiques API
Route::get('antiques', [AntiqueController::class, 'index']);
Route::get('antiques/{id}', [AntiqueController::class, 'show']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('antiques', [AntiqueController::class, 'store']);
    Route::get('antiques/edit/{id}', [AntiqueController::class, 'edit']);
    Route::put('antiques/update/{id}', [AntiqueController::class, 'update']);
    Route::delete('antiques/{id}', [AntiqueController::class, 'destroy']);

    Route::post('logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'message' => 'Logged out successfully']);
    });
    
    Route::get('user', function (Request $request) {
        return $request->user();
    });
});