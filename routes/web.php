<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TourController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;

// Admin controllers
use App\Http\Controllers\Admin\TourAdminController;
use App\Http\Controllers\Admin\TourDateAdminController;
use App\Http\Controllers\Admin\UserAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home -> Catálogo
Route::get('/', fn () => redirect()->route('tours.index'));

// === Catálogo público ===
Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
Route::get('/tours/{tour}', [TourController::class, 'show'])->name('tours.show');

// === Dashboard (Breeze) ===
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// === Página para usuarios NO aprobados (logueados) ===
Route::view('/account/pending', 'account.pending')
    ->middleware('auth')
    ->name('account.pending');

// === Área autenticada y aprobada ===
Route::middleware(['auth', 'approved'])->group(function () {
    // Perfil
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

    // Órdenes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Voucher imprimible
    Route::get('/orders/{order}/voucher', [OrderController::class, 'voucher'])->name('orders.voucher');
});

// === Admin (auth + gate:admin) ===
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'can:admin'])
    ->group(function () {

        // Ping opcional
        Route::get('test', fn () => 'OK ADMIN')->name('ping');

        // Gestión de usuarios (aprobación y rol admin)
        Route::get('users',            [UserAdminController::class, 'index'])->name('users.index');
        Route::patch('users/{user}/approve',     [UserAdminController::class, 'approve'])->name('users.approve');
        Route::patch('users/{user}/revoke',      [UserAdminController::class, 'revoke'])->name('users.revoke');
        Route::patch('users/{user}/toggle-admin',[UserAdminController::class, 'toggleAdmin'])->name('users.toggleAdmin');

        // CRUD de Tours
        Route::resource('tours', TourAdminController::class)->except(['show']);

        // Fechas anidadas (con shallow)
        Route::resource('tours.dates', TourDateAdminController::class)
            ->shallow()
            ->except(['show', 'index']);

        // Soft delete helpers — Tours
        Route::post('tours/{id}/restore', [TourAdminController::class, 'restore'])->name('tours.restore');
        Route::delete('tours/{id}/force', [TourAdminController::class, 'forceDelete'])->name('tours.forceDelete');

        // Soft delete helpers — Dates
        Route::post('dates/{id}/restore', [TourDateAdminController::class, 'restore'])->name('dates.restore');
        Route::delete('dates/{id}/force', [TourDateAdminController::class, 'forceDelete'])->name('dates.forceDelete');
    });

// Rutas Breeze (login/register/etc.)
require __DIR__.'/auth.php';
