<?php

use App\Http\Controllers\AntiqueController;
use App\Http\Controllers\OfferController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth' /* , 'verified' */])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::resources([
        'antiques' => AntiqueController::class,
        "offers" => OfferController::class
    ]);
});
// Public routes
Route::get('/', [AntiqueController::class, 'index']);
Route::post('/autocomplete', [AntiqueController::class, 'autocomplete'])->name('autocomplete');


Auth::routes(/* ['verify' => true] */);

Route::get('lang/{locale}', [App\Http\Controllers\LocalizationController::class, 'index']);
Route::get('/admin/antiques', [AntiqueController::class, 'index'])->middleware('admin')->name('articles.index');
Route::get('/admin/antiques/create', [AntiqueController::class, 'create'])->middleware('admin')->name('articles.create');
Route::post('/admin/antiques/store', [AntiqueController::class, 'store'])->middleware('admin')->name('articles.store');
Route::get('/admin/antiques/{id}', [AntiqueController::class, 'show'])->middleware('admin')->name('articles.show');
Route::get('/admin/antiques/{id}/edit', [AntiqueController::class, 'edit'])->middleware('admin')->name('articles.edit');
Route::patch('/admin/antiques/{id}/update', [AntiqueController::class, 'update'])->middleware('admin')->name('articles.update');
Route::delete('/admin/antiques/{id}', [AntiqueController::class, 'destroy'])->middleware('admin')->name('articles.destroy');