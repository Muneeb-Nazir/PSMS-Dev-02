<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\FuelTypeController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard - All authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Transactions - All roles can view and create
    Route::resource('transactions', TransactionController::class)->except(['edit', 'update']);

    // Vehicles - All roles
    Route::resource('vehicles', VehicleController::class);

    // Fuel Types - Admin and Manager only
    Route::middleware(['check.role:admin,manager'])->group(function () {
        Route::resource('fuel-types', FuelTypeController::class);
    });

    // Stock - Admin and Manager only
    Route::middleware(['check.role:admin,manager'])->group(function () {
        Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
        Route::patch('/stock/{stock}', [StockController::class, 'update'])->name('stock.update');
    });

    // Accounts - Admin and Manager only
    Route::middleware(['check.role:admin,manager'])->group(function () {
        Route::resource('accounts', AccountController::class);
    });

    // Users - Admin only
    Route::middleware(['check.role:admin'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // Settings - Admin only
    Route::middleware(['check.role:admin'])->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});

require __DIR__.'/auth.php';
