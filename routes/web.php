<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\CardController;
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

    // Media
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::post('media', [MediaController::class, 'store'])->name('media.store');
    Route::get('media/{medium}/thumb', [MediaController::class, 'thumb'])->name('media.thumb');
    Route::get('media/{medium}', [MediaController::class, 'show'])->name('media.show');
    Route::delete('media/{medium}', [MediaController::class, 'destroy'])->name('media.destroy');

    // Organizations
    Route::resource('organizations', OrganizationController::class)->except(['show']);

    // Groups
    Route::resource('groups', GroupController::class)->except(['show']);

    // Cards
    Route::get('cards', [CardController::class, 'index'])->name('cards.index');
    Route::get('cards/create', [CardController::class, 'create'])->name('cards.create');
    Route::post('cards', [CardController::class, 'store'])->name('cards.store');
    Route::get('cards/templates', [CardController::class, 'templates'])->name('cards.templates');
    Route::post('cards/from-template', [CardController::class, 'fromTemplate'])->name('cards.from-template');
    Route::post('cards/generate', [CardController::class, 'generate'])->name('cards.generate');
    Route::get('cards/{card}/builder', [CardController::class, 'builder'])->name('cards.builder');
    Route::post('cards/{card}/config', [CardController::class, 'saveConfig'])->name('cards.save-config');
    Route::get('cards/{card}/preview', [CardController::class, 'preview'])->name('cards.preview');
    Route::post('cards/{card}/publish', [CardController::class, 'publish'])->name('cards.publish');
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
