@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <span>PENJUALAN</span>
                <span>•</span>
                <span class="text-slate-500">SIKOL SYSTEM</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Riwayat Transaksi</h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Catatan seluruh transaksi penjualan, pembayaran, dan retur pelanggan toko.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl flex items-center gap-1.5 transition-all border border-slate-200">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Export Excel</span>
            </button>
            <button type="button" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl flex items-center gap-1.5 transition-all border border-slate-200">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    <!-- 4 Summary Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Transaksi Hari Ini -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-xs font-semibold text-slate-500">Transaksi Hari Ini</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                    <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $totalTransToday }}</span>
                <span class="text-xs font-semibold text-slate-500 ml-1">transaksi</span>
            </div>
        </div>

        <!-- Pendapatan Hari Ini -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-xs font-semibold text-slate-500">Pendapatan Hari Ini</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-black text-slate-900 tracking-tight">Rp {{ number_format($totalRevenueToday / 1000, 0, ',', '.') }}</span>
                <span class="text-xs font-semibold text-slate-500 ml-1">rb</span>
            </div>
        </div>

        <!-- Rata-rata Transaksi -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-xs font-semibold text-slate-500">Rata-rata Nilai Transaksi</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                    <i data-lucide="calculator" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-black text-slate-900 tracking-tight">Rp {{ number_format($avgTransValue, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Total Semua -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-xs font-semibold text-slate-500">Total Semua Transaksi</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                    <i data-lucide="archive" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $totalAll }}</span>
                <span class="text-xs font-semibold text-slate-500 ml-1">struk</span>
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Filter Bar -->
        <form action="{{ route('transaksi.index') }}" method="GET" class="flex flex-wrap items-end gap-3">
            <!-- Search -->
            <div class="flex-1 min-w-[220px]">
                <label class="block text-xs font-bold text-slate-700 mb-1">Cari Transaksi</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="No. Struk, nama kasir, atau pelanggan..." 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                <select name="status" onchange="this.form.submit()" 
                        class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 focus:outline-none">
                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="success" {{ $status == 'success' ? 'selected' : '' }}>Berhasil</option>
                    <option value="refunded" {{ $status == 'refunded' ? 'selected' : '' }}>Refund</option>
                    <option value="void" {{ $status == 'void' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pembayaran</label>
                <select name="payment" onchange="this.form.submit()" 
                        class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm font-semibold text-slate-700 focus:outline-none">
                    <option value="" {{ !$payment ? 'selected' : '' }}>Semua Metode</option>
                    <option value="Tunai" {{ $payment == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="QRIS" {{ $payment == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                    <option value="Transfer" {{ $payment == 'Transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="Debit" {{ $payment == 'Debit' ? 'selected' : '' }}>Debit</option>
                </select>
            </div>

            <!-- Date Filters -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" 
                       class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" 
                       class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-sm text-slate-700 focus:outline-none">
            </div>

            <button type="submit" class="px-4 py-2 bg-brand-dark hover:bg-brand-800 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-xs transition-all">
                <i data-lucide="filter" class="w-4 h-4"></i>
                <span>Filter</span>
            </button>
        </form>

        <!-- Status Filter Pills -->
        <div class="flex items-center gap-2 pt-2 border-t border-slate-100 text-xs font-semibold">
            <a href="{{ route('transaksi.index', ['status' => 'all']) }}" 
               class="px-4 py-1.5 rounded-full transition-all {{ $status == 'all' ? 'bg-brand-100/80 text-brand-800 font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua <span class="ml-1 text-[11px] font-extrabold opacity-75">{{ $totalAll }}</span>
            </a>
            <a href="{{ route('transaksi.index', ['status' => 'success']) }}" 
               class="px-4 py-1.5 rounded-full transition-all {{ $status == 'success' ? 'bg-emerald-100 text-emerald-800 font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Berhasil <span class="ml-1 text-[11px] font-extrabold opacity-75">{{ $successCount }}</span>
            </a>
            <a href="{{ route('transaksi.index', ['status' => 'refunded']) }}" 
               class="px-4 py-1.5 rounded-full transition-all {{ $status == 'refunded' ? 'bg-amber-100 text-amber-800 font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Refund <span class="ml-1 px-1.5 py-0.2 rounded-full bg-amber-200 text-amber-900 text-[10px] font-extrabold">{{ $refundedCount }}</span>
            </a>
            <a href="{{ route('transaksi.index', ['status' => 'void']) }}" 
               class="px-4 py-1.5 rounded-full transition-all {{ $status == 'void' ? 'bg-red-100 text-red-800 font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Dibatalkan <span class="ml-1 px-1.5 py-0.2 rounded-full bg-red-200 text-red-900 text-[10px] font-extrabold">{{ $voidCount }}</span>
            </a>
        </div>

        <!-- Transaction Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[11px] font-bold">
                        <th class="py-3 font-semibold">NO. STRUK</th>
                        <th class="py-3 font-semibold">TANGGAL & WAKTU</th>
                        <th class="py-3 font-semibold">ITEM</th>
                        <th class="py-3 font-semibold">PEMBAYARAN</th>
                        <th class="py-3 text-right font-semibold">TOTAL</th>
                        <th class="py-3 text-center font-semibold">STATUS</th>
                        <th class="py-3 font-semibold">KASIR</th>
                        <th class="py-3 text-right font-semibold">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $t)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5">
                            <span class="font-bold text-brand-700 text-sm">{{ $t->invoice_number }}</span>
                        </td>
                        <td class="py-3.5">
                            <p class="font-semibold text-slate-900">{{ $t->created_at->format('d M Y') }}</p>
                            <p class="text-[11px] text-slate-400">{{ $t->created_at->format('H:i') }} WIB</p>
                        </td>
                        <td class="py-3.5">
                            <span class="font-semibold text-slate-700">{{ $t->items->count() }} produk</span>
                            <p class="text-[11px] text-slate-400 mt-0.5 truncate max-w-[180px]">
                                {{ $t->items->pluck('product_name')->take(2)->implode(', ') }}{{ $t->items->count() > 2 ? '...' : '' }}
                            </p>
                        </td>
                        <td class="py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                                <i data-lucide="{{ $t->payment_icon }}" class="w-3.5 h-3.5"></i>
                                {{ $t->payment_method }}
                            </span>
                        </td>
                        <td class="py-3.5 text-right font-extrabold text-slate-900 text-sm">
                            Rp {{ number_format($t->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 text-center">
                            <span class="{{ $t->status_badge_class }} font-semibold px-2.5 py-0.5 rounded-full text-xs inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                {{ $t->status_label }}
                            </span>
                        </td>
                        <td class="py-3.5 font-medium text-slate-600">
                            {{ $t->cashier_name }}
                        </td>
                        <td class="py-3.5 text-right">
                            <a href="{{ route('transaksi.show', $t->id) }}" 
                               class="p-1.5 text-slate-400 hover:text-brand-700 rounded-lg hover:bg-slate-100 transition-all inline-flex items-center gap-1 text-xs font-semibold">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                <span>Detail</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400">
                                    <i data-lucide="receipt" class="w-7 h-7"></i>
                                </div>
                                <p class="text-slate-400 font-semibold text-sm">Belum ada transaksi ditemukan.</p>
                                <p class="text-slate-400 text-xs">Transaksi akan otomatis tercatat saat penjualan di kasir.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
            <span>Menampilkan {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} dari {{ $transactions->total() }} transaksi</span>
            <div>
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
