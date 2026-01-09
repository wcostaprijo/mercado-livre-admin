<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MercadoLivreController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/mercado-livre/auth', [MercadoLivreController::class, 'saveAuthorizationCode'])->name('mercado-livre.auth');
    Route::get('/mercado-livre/categories/{id?}', [MercadoLivreController::class, 'categories'])->name('mercado-livre.categories');
    Route::get('/mercado-livre/categories/{id}/attributes', [MercadoLivreController::class, 'attributes'])->name('mercado-livre.categories.attributes');
    Route::get('/mercado-livre/listing-types', [MercadoLivreController::class, 'listingTypes'])->name('mercado-livre.listing-types');

    Route::resource('products', ProductController::class)->except(['show']);
    Route::post('/products/upload-images', [ProductController::class, 'uploadImages'])->name('products.upload-images');

    Route::resource('orders', OrderController::class)->only(['index', 'show']);
});

require __DIR__.'/settings.php';
