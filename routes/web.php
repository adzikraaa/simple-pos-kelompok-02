<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', DashboardController::class)
    ->name('dashboard');

// POS dan transaksi untuk admin, kasir, dan manager
Route::middleware(['auth', 'role:admin,kasir,manager'])->group(function () {
    Route::get('/pos', [TransactionController::class, 'create'])
        ->name('pos.create');

    Route::post('/pos', [TransactionController::class, 'store'])
        ->name('transactions.store');

    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions.index');

    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])
        ->name('transactions.show');
});

// Kategori dan produk hanya untuk admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('categories', CategoryController::class)->except('show');

    Route::resource('products', ProductController::class)->except(['show', 'destroy']);
});
