<?php

use App\Http\Controllers\AntiqueController;
use App\Http\Controllers\OfferController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::resources([
        'antiques' => AntiqueController::class,
        "offers" => OfferController::class
    ]);
});

// Public routes
Route::get('/', [AntiqueController::class, 'index']);
Route::post('/autocomplete', [AntiqueController::class, 'autocomplete'])->name('autocomplete');

Auth::routes(['verify' => true]); // Enable email verification routes

Route::get('lang/{locale}', [App\Http\Controllers\LocalizationController::class, 'index']);