<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\PaymentController;

Route::get('/', [ItemController::class, 'welcome'])->name('welcome');

Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

Route::post('login', [AuthenticatedSessionController::class, 'store']);

Route::get('register', [RegisteredUserController::class, 'create'])
                ->name('register');

Route::post('register', [RegisteredUserController::class, 'store']);

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
                ->middleware('auth')
                ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ItemController::class, 'index'])->name('dashboard');
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

    // Route pour soumettre une offre
    Route::post('/items/{item}/offers', [OfferController::class, 'store'])->name('offers.store');

    // Routes pour gérer une offre
    Route::patch('/offers/{offer}/accept', [OfferController::class, 'accept'])->name('offers.accept');
    Route::patch('/offers/{offer}/reject', [OfferController::class, 'reject'])->name('offers.reject');

    // Routes pour le paiement
    Route::get('/offers/{offer}/payment', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('/offers/{offer}/payment', [PaymentController::class, 'store'])->name('payment.store');
});

Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
