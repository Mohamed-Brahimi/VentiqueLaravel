<?php

use App\Http\Controllers\AntiqueController;
use Illuminate\Support\Facades\Route;

// API autocomplete endpoint (used by Vue SPA)
Route::match(['get', 'post'], '/autocomplete', [AntiqueController::class, 'autocomplete'])->name('autocomplete');

// Root route - serve monopage
Route::get('/', function () {
    return view('monopage');
})->name('home');

// All other routes go to the Vue SPA (monopage)
Route::get('/{any}', function () {
    return view('monopage');
})->where('any', '^(?!api).*$'); // Exclude API routes

