<?php

use App\Http\Controllers\AntiqueController;
use App\Http\Controllers\OfferController;
use Illuminate\Support\Facades\Route;

Route::get('{any}', function () {
  return view('monopage');
})->where('any', '.*');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::resources([
        'antiques' => AntiqueController::class,
    ]);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/', [AntiqueController::class, 'index']);
Route::post('/autocomplete', [AntiqueController::class, 'autocomplete'])->name('autocomplete');

Route::get('/apropos', function () {
    return view('apropos');
})->name('apropos');

Auth::routes(/* ['verify' => true] */);



Route::get('lang/{locale}', [App\Http\Controllers\LocalizationController::class, 'index']);

