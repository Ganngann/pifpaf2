<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\ItemImageController;
use App\Http\Controllers\PickupAddressController;
use App\Http\Controllers\AiRequestController;
use App\Http\Controllers\StyleguideController;

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
    Route::post('/items/{item}/unpublish', [ItemController::class, 'unpublish'])->name('items.unpublish');
    Route::post('/items/{item}/publish', [ItemController::class, 'publish'])->name('items.publish');
    Route::post('/api/items/{item}/toggle-status', [ItemController::class, 'toggleStatus'])->name('api.items.toggle-status');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    Route::post('/items/{item}/offers', [OfferController::class, 'store'])->name('offers.store');
    Route::post('/items/{item}/buy-now', [OfferController::class, 'buyNow'])->name('offers.buyNow');
    Route::patch('/offers/{offer}/accept', [OfferController::class, 'accept'])->name('offers.accept');
    Route::patch('/offers/{offer}/reject', [OfferController::class, 'reject'])->name('offers.reject');
    Route::get('/payment/{offer}', [PaymentController::class, 'create'])->name('payment.create');
    Route::post('/payment/{offer}', [PaymentController::class, 'store'])->name('payment.store');
    Route::patch('/transactions/{transaction}/confirm-pickup', [TransactionController::class, 'confirmPickup'])->name('transactions.confirm-pickup');
    Route::patch('/transactions/{transaction}/confirm-reception', [TransactionController::class, 'confirmReception'])->name('transactions.confirm-reception');
    Route::patch('/transactions/{transaction}/add-tracking', [TransactionController::class, 'addTracking'])->name('transactions.addTracking');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/wallet', [WalletController::class, 'show'])->name('wallet.show');
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');

    // Routes pour l'IA
    Route::get('/items/create-with-ai', [ItemController::class, 'createWithAi'])->name('items.create-with-ai');
    Route::post('/items/create-from-ai', [ItemController::class, 'createFromAi'])->name('items.create-from-ai');

    // Route pour la suppression d'image
    Route::delete('/item-images/{itemImage}', [ItemImageController::class, 'destroy'])->name('item-images.destroy');
    Route::post('/item-images/{itemImage}/set-primary', [ItemImageController::class, 'setPrimary'])->name('item-images.set-primary');
    Route::post('/item-images/reorder', [ItemImageController::class, 'reorder'])->name('item-images.reorder');

    // Routes pour la gestion des adresses de retrait
    Route::resource('profile/addresses', PickupAddressController::class)->names('profile.addresses');

    // Routes pour la file d'attente IA
    Route::get('/ai-requests', [AiRequestController::class, 'index'])->name('ai-requests.index');
    Route::post('/ai-requests', [AiRequestController::class, 'store'])->name('ai-requests.store');
    Route::post('/ai-requests/{aiRequest}/retry', [AiRequestController::class, 'retry'])->name('ai-requests.retry');
    // Routes pour la messagerie
    Route::resource('conversations', \App\Http\Controllers\ConversationController::class)->only(['index', 'show', 'store']);
    Route::post('conversations/{conversation}/messages', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');

    // Route pour les avis
    Route::post('/transactions/{transaction}/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

    // Routes pour l'historique des transactions
    Route::get('/purchases', [TransactionController::class, 'purchases'])->name('transactions.purchases');
    Route::get('/sales', [TransactionController::class, 'sales'])->name('transactions.sales');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

    // Route pour la création de l'envoi
    Route::post('/transactions/{transaction}/ship', [TransactionController::class, 'createShipment'])->name('transactions.ship');
});

Route::get('/ai-requests/crop-preview', [AiRequestController::class, 'cropPreview'])->name('ai.requests.crop_preview');

Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

Route::get('/test-ai', function (App\Services\GoogleAiService $aiService) {
    $imagePath = storage_path('app/public/images/placeholder.jpg');
    return $aiService->analyzeImage($imagePath);
});

// Routes pour l'administration
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'index'])->name('dashboard');

    // Gestion des utilisateurs
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'usersIndex'])->name('users.index');
    Route::post('/users/{user}/ban', [\App\Http\Controllers\AdminController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [\App\Http\Controllers\AdminController::class, 'unban'])->name('users.unban');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/styleguide', [StyleguideController::class, 'index'])->name('styleguide');
});

// Webhook pour Sendcloud
Route::post('/webhooks/sendcloud', [\App\Http\Controllers\SendcloudWebhookController::class, 'handle'])->name('webhooks.sendcloud');
