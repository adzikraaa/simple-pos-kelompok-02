<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('pos.create');
});

Route::middleware('guest')->group(function () {
    Route::get('/info', fn () => view('info'))->name('info');

    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

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

// Kategori, produk, dan laporan hanya untuk admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('categories', CategoryController::class)
        ->except('show');

    Route::resource('products', ProductController::class)
        ->except(['show', 'destroy']);

    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index');

    Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])
        ->name('reports.export.csv');

    Route::get('/reports/export/xlsx', [ReportController::class, 'exportExcel'])
        ->name('reports.export.xlsx');

    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])
        ->name('reports.export.pdf');
        
    Route::get('/import', [ReportController::class, 'importForm'])
        ->name('import.create');

    Route::post('/import', [ReportController::class, 'import'])
        ->name('import.store');

    Route::post('/import-excel', [ReportController::class, 'importExcelStore'])
        ->name('import.excel.store');
});