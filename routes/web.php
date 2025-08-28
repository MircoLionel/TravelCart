<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\TourAdminController;
use App\Http\Controllers\Admin\TourDateAdminController;

// Home -> catálogo
Route::get('/', fn () => redirect()->route('tours.index'));

// === Catálogo público ===
Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
Route::get('/tours/{tour}', [TourController::class, 'show'])->name('tours.show');

// === Dashboard (Breeze) ===
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])->name('dashboard');

// === Área autenticada ===
Route::middleware('auth')->group(function () {
    // Perfil (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Carrito
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('/cart/items', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/items/{item}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'placeOrder'])->name('checkout.place');

    // Orden / Confirmación
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

Route::prefix('admin')->name('admin.')->middleware(['auth','can:admin'])->group(function () {
    // Test simple para confirmar que el grupo Admin está cargando
    Route::get('ping', fn () => 'pong')->name('ping');

    // CRUD de Tours
    Route::resource('tours', \App\Http\Controllers\Admin\TourAdminController::class)->except(['show']);

    // Fechas (anidadas, con shallow)
    Route::resource('tours.dates', \App\Http\Controllers\Admin\TourDateAdminController::class)
        ->shallow()
        ->except(['show','index']);

    // Soft delete helpers — Tours
    Route::post('tours/{id}/restore', [\App\Http\Controllers\Admin\TourAdminController::class, 'restore'])
        ->name('tours.restore');
    Route::delete('tours/{id}/force', [\App\Http\Controllers\Admin\TourAdminController::class, 'forceDelete'])
        ->name('tours.forceDelete');

    // Soft delete helpers — Dates
    Route::post('dates/{id}/restore', [\App\Http\Controllers\Admin\TourDateAdminController::class, 'restore'])
        ->name('dates.restore');
    Route::delete('dates/{id}/force', [\App\Http\Controllers\Admin\TourDateAdminController::class, 'forceDelete'])
        ->name('dates.forceDelete');
});
   

// Necesario para que Breeze funcione (login/register/etc)
require __DIR__.'/auth.php';
