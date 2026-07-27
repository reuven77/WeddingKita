<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminItemController;
use App\Http\Controllers\AdminPackageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('home');
Route::get('/items/{id}', [CatalogController::class, 'showItem'])->name('items.show');
Route::get('/packages/{id}', [CatalogController::class, 'showPackage'])->name('packages.show');
Route::post('/rentals', [RentalController::class, 'store'])->name('rentals.store')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Member: Pembayaran & Pembatalan
    Route::post('/rentals/{rental}/payment', [PaymentController::class, 'submitProof'])
        ->name('rentals.payment.submit');
    Route::post('/rentals/{rental}/cancel', [RentalController::class, 'cancel'])
        ->name('rentals.cancel');

    // Invoice (member + admin)
    Route::get('/rentals/{rental}/invoice', [PaymentController::class, 'invoice'])
        ->name('rentals.invoice');

    // ─── Admin: Status Rental & Fitting ───────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::post('/admin/rentals/{id}/status', [DashboardController::class, 'updateRentalStatus'])
            ->name('admin.rentals.status');
        Route::post('/admin/fittings/{id}/status', [DashboardController::class, 'updateFittingStatus'])
            ->name('admin.fittings.status');

        // Admin: Konfirmasi Pembayaran & Izinkan Ambil
        Route::post('/admin/rentals/{rental}/confirm-payment', [PaymentController::class, 'confirmPayment'])
            ->name('admin.rentals.confirm-payment');
        Route::post('/admin/rentals/{rental}/allow-pickup', [PaymentController::class, 'allowPickup'])
            ->name('admin.rentals.allow-pickup');

        // Admin Resource CRUD for Items & Packages
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::resource('items', AdminItemController::class)->except(['show']);
            Route::resource('packages', AdminPackageController::class)->except(['show']);
        });
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
