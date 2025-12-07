<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;

Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('login')->group(function () {
        Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/', [AuthController::class, 'store'])->name('store');
    });

    Route::post('logout', [AuthController::class, 'destroy'])->name('logout');
});

Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return \Inertia\Inertia::render('Dashboard');
    })->name('dashboard');
});
