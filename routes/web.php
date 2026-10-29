<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

// Kasir POS Routes
Route::get('/kasir', [CashierController::class, 'index'])->name('kasir.index');
Route::post('/kasir/checkout', [CashierController::class, 'store'])->name('kasir.checkout');
Route::get('/kasir/search', [CashierController::class, 'searchProduct'])->name('kasir.search');
Route::get('/kasir/receipt/{transaction}', [CashierController::class, 'receipt'])->name('kasir.receipt');

// Pelanggan Routes
Route::get('/pelanggan/search', [CustomerController::class, 'search'])->name('pelanggan.search');
Route::resource('pelanggan', CustomerController::class)->parameters(['pelanggan' => 'pelanggan'])->only(['index','create','store','edit','update','destroy']);

// Data Barang Routes
Route::resource('barang', ProductController::class)->parameters([
    'barang' => 'product'
])->names([
    'index' => 'products.index',
    'create' => 'products.create',
    'store' => 'products.store',
    'show' => 'products.show',
    'edit' => 'products.edit',
    'update' => 'products.update',
    'destroy' => 'products.destroy',
]);

// Stok Masuk Routes
Route::get('/stok', [StockMovementController::class, 'index'])->name('stok.index');
Route::post('/stok', [StockMovementController::class, 'store'])->name('stok.store');

// Riwayat Transaksi Routes
Route::get('/transaksi', [TransactionController::class, 'index'])->name('transaksi.index');
Route::get('/transaksi/{transaction}', [TransactionController::class, 'show'])->name('transaksi.show');

// Laporan & Pengaturan Routes
Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');
Route::get('/pengaturan', [SettingController::class, 'index'])->name('pengaturan.index');

