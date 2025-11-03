<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\AntiqueController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('register', [RegisterController::class, 'register']);

Route::post('login', [RegisterController::class, 'login']);
Route::get('/antiques', [AntiqueController::class, 'index']);
Route::get('/antiques/{id}', [AntiqueController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('antiques/', [AntiqueController::class, 'store']);
    Route::get('antiques/edit/{id}', [AntiqueController::class, 'edit']);
    Route::put('antiques/update/{id}', [AntiqueController::class, 'update']);
    Route::delete('antiques/{id}', [AntiqueController::class, 'destroy']);

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});