<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

// Data Barang Routes
Route::resource('barang', ProductController::class)->names([
    'index' => 'products.index',
    'create' => 'products.create',
    'store' => 'products.store',
    'show' => 'products.show',
    'edit' => 'products.edit',
    'update' => 'products.update',
    'destroy' => 'products.destroy',
]);

// Stok Masuk & Keluar Routes
Route::get('/stok', [StockMovementController::class, 'index'])->name('stok.index');
Route::post('/stok', [StockMovementController::class, 'store'])->name('stok.store');

// Riwayat Transaksi Routes
Route::get('/transaksi', [TransactionController::class, 'index'])->name('transaksi.index');
Route::get('/transaksi/{transaction}', [TransactionController::class, 'show'])->name('transaksi.show');

// Laporan & Pengaturan Routes
Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
Route::get('/pengaturan', [SettingController::class, 'index'])->name('pengaturan.index');
