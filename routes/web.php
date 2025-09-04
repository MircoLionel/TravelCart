<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TourController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\TourAdminController;
use App\Http\Controllers\Admin\TourDateAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Middleware\EnsureApproved;

// Home -> Catálogo
Route::get('/', fn () => redirect()->route('tours.index'));

// === Catálogo público ===
Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
Route::get('/tours/{tour}', [TourController::class, 'show'])->name('tours.show');

// === Dashboard (Breeze) ===
Route::get('/dashboard', fn () => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Página informativa cuando el usuario no está aprobado
Route::get('/account/pending', [AccountController::class, 'pending'])->name('account.pending');

// === Área autenticada ===
Route::middleware('auth')->group(function () {
    // Perfil (esto queda igual)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ✅ Requiere usuario aprobado (agregamos la CLASE del middleware)
    Route::middleware([EnsureApproved::class])->group(function () {

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
        Route::get('/orders/{order}/voucher', [OrderController::class, 'voucher'])->name('orders.voucher');
    });
});

// === Admin (auth + gate:admin) ===
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'can:admin'])
    ->group(function () {

        Route::get('test', fn () => 'OK ADMIN')->name('ping');

        // Usuarios —> (NUEVO) rutas necesarias para el menú "admin.users.index"
        Route::get('users', [UserAdminController::class, 'index'])->name('users.index');
        Route::patch('users/{user}', [UserAdminController::class, 'update'])->name('users.update');

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

// Breeze auth routes
require __DIR__.'/auth.php';
