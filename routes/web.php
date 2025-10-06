<?php

use App\Http\Controllers\AntiqueController;
use App\Http\Controllers\OfferController;
use Illuminate\Support\Facades\Route;


Route::get('/', [AntiqueController::class, 'index']);

Route::post(uri: '/autocomplete', action: [AntiqueController::class, 'autocomplete'])->name('autocomplete');

Route::resources([
    'antiques' => AntiqueController::class,
    "offers" => OfferController::class
]);
// Route::get('/antiques', [AntiqueController::class, 'index']);
// Route::prefix('antiques')->controller(AntiqueController::class)->group(function () {
//     Route::get('/', 'index');
//     Route::get('create', 'create');
//     Route::get('{id}', 'show');

//     Route::post('store', 'store');
//     Route::patch('{id}', 'update');
//     Route::delete('{id}', 'destroy');
// });



