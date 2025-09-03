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
// === Admin (auth + gate:admin) ===
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'can:admin'])
    ->group(function () {

        // (opcional) Home Admin
        Route::get('/', fn () => redirect()->route('admin.users.index'))->name('home');
        
        Route::get('/', fn () => redirect()->route('admin.users.index'))->name('home');

        // Usuarios (aprobación, legajo, rol)
        Route::resource('users', \App\Http\Controllers\Admin\UserAdminController::class)
            ->only(['index','edit','update']);

        // (tus rutas existentes)
        Route::resource('tours', \App\Http\Controllers\Admin\TourAdminController::class)->except(['show']);
        Route::resource('tours.dates', \App\Http\Controllers\Admin\TourDateAdminController::class)
            ->shallow()
            ->except(['show','index']);

        Route::post('tours/{id}/restore', [\App\Http\Controllers\Admin\TourAdminController::class, 'restore'])->name('tours.restore');
        Route::delete('tours/{id}/force', [\App\Http\Controllers\Admin\TourAdminController::class, 'forceDelete'])->name('tours.forceDelete');

        Route::post('dates/{id}/restore', [\App\Http\Controllers\Admin\TourDateAdminController::class, 'restore'])->name('dates.restore');
        Route::delete('dates/{id}/force', [\App\Http\Controllers\Admin\TourDateAdminController::class, 'forceDelete'])->name('dates.forceDelete');
    
        // --- Usuarios (nuevo) ---
        Route::resource('users', \App\Http\Controllers\Admin\UserAdminController::class)
            ->only(['index','edit','update']);

        // --- Lo que ya tenías ---
        Route::get('test', fn () => 'OK ADMIN')->name('ping');

        Route::resource('tours', \App\Http\Controllers\Admin\TourAdminController::class)->except(['show']);

        Route::resource('tours.dates', \App\Http\Controllers\Admin\TourDateAdminController::class)
            ->shallow()
            ->except(['show', 'index']);

        Route::post('tours/{id}/restore', [\App\Http\Controllers\Admin\TourAdminController::class, 'restore'])->name('tours.restore');
        Route::delete('tours/{id}/force', [\App\Http\Controllers\Admin\TourAdminController::class, 'forceDelete'])->name('tours.forceDelete');

        Route::post('dates/{id}/restore', [\App\Http\Controllers\Admin\TourDateAdminController::class, 'restore'])->name('dates.restore');
        Route::delete('dates/{id}/force', [\App\Http\Controllers\Admin\TourDateAdminController::class, 'forceDelete'])->name('dates.forceDelete');
    });


// Rutas Breeze (login/register/etc.)
require __DIR__.'/auth.php';
