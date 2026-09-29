<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PublicCardController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

// Auth (before wildcard routes)
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [LoginController::class, 'showForm'])->name('admin.login');
    Route::post('/admin/login', [LoginController::class, 'login'])
        ->name('admin.login.post')
        ->middleware('throttle:10,1');
});

Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout')->middleware('auth');

// Admin (protected) — prefix routes BEFORE wildcard
Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Http\Middleware\AdminAuth::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('employees', EmployeeController::class)->only(['index', 'show', 'edit', 'update']);
    Route::post('employees/{employee}/toggle', [EmployeeController::class, 'toggleActive'])->name('employees.toggle');

    Route::get('imports', [ImportController::class, 'index'])->name('imports.index');
    Route::get('imports/create', [ImportController::class, 'create'])->name('imports.create');
    Route::post('imports', [ImportController::class, 'store'])->name('imports.store');
    Route::get('imports/{import}', [ImportController::class, 'show'])->name('imports.show');
});

// Public homepage
Route::get('/', fn() => redirect()->route('admin.login'));

// Public wildcard routes LAST (must come after all specific routes)
Route::get('/{slug}/vcard', [PublicCardController::class, 'vcard'])->name('card.vcard');
Route::get('/{slug}/qr', [PublicCardController::class, 'qrPng'])->name('card.qr');
Route::get('/{slug}/photo', [PhotoController::class, 'show'])->name('card.photo');
Route::get('/{slug}/apple-wallet', [WalletController::class, 'apple'])->name('card.apple-wallet');
Route::get('/{slug}/google-wallet', [WalletController::class, 'google'])->name('card.google-wallet');
Route::get('/{slug}', [PublicCardController::class, 'show'])->name('card.show');
