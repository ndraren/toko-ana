@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Page Header & Period Filter -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Ringkasan Toko</h2>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Operasional Normal
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1 font-medium">
                {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </p>
        </div>

        <!-- Period Toggle Buttons -->
        <div class="bg-brand-50 border border-brand-200/50 p-1 rounded-xl flex items-center overflow-x-auto whitespace-nowrap gap-1 text-xs font-semibold text-slate-600">
            <a href="?period=today" class="px-3 py-1.5 rounded-lg {{ $timeFilter == 'today' ? 'bg-white text-brand-800 shadow-xs border border-brand-200/60' : 'hover:text-brand-800' }}">Hari Ini</a>
            <a href="?period=week" class="px-3 py-1.5 rounded-lg {{ $timeFilter == 'week' ? 'bg-white text-brand-800 shadow-xs border border-brand-200/60' : 'hover:text-brand-800' }}">Pekan Ini</a>
            <a href="?period=month" class="px-3 py-1.5 rounded-lg {{ $timeFilter == 'month' ? 'bg-white text-brand-800 shadow-xs border border-brand-200/60' : 'hover:text-brand-800' }}">Bulan Ini</a>
        </div>
    </div>

    <!-- Key Summary Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Card 1: Omzet / Penjualan (HIGHLIGHTED - Green gradient) -->
        <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-2xl p-5 shadow-md flex flex-col justify-between text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-10 translate-x-10"></div>
            <div class="absolute bottom-0 left-0 w-20 h-20 bg-white/5 rounded-full translate-y-8 -translate-x-8"></div>
            <div class="relative">
                <div class="flex items-start justify-between">
                    <span class="text-xs font-semibold text-brand-100">Omzet Penjualan</span>
                    <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-4 h-4 text-white"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-black tracking-tight">Rp {{ number_format($todaySalesValue, 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-2 flex items-center gap-1.5 text-xs font-medium text-brand-100">
                        <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                        <span class="font-bold text-white">{{ $todayTransactionsCount }}</span>
                        <span>transaksi tercatat</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Stok Menipis (HIGHLIGHTED - Amber/Warning) -->
        <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200/80 rounded-2xl p-5 shadow-xs flex flex-col justify-between relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-amber-100/50 rounded-full -translate-y-8 translate-x-8"></div>
            <div class="relative">
                <div class="flex items-start justify-between">
                    <span class="text-xs font-semibold text-amber-700">Stok Menipis</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-100 border border-amber-200/60 flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-3xl font-black text-amber-800 tracking-tight">{{ $criticalProductsCount }}</span>
                        <span class="text-xs font-semibold text-amber-600">produk kritis</span>
                    </div>
                    <div class="mt-2">
                        @if($criticalProductsCount > 0)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-100 border border-amber-200/60 text-amber-800 text-[11px] font-bold">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                Perlu PO segera
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200/60 text-emerald-700 text-[11px] font-bold">
                                <i data-lucide="check-circle-2" class="w-3 h-3"></i>
                                Semua stok aman
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Stok & Nilai Persediaan (Combined, subtle) -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-xs font-semibold text-slate-500">Stok & Persediaan</span>
                <div class="w-8 h-8 rounded-lg bg-brand-50 border border-brand-200/40 flex items-center justify-center">
                    <i data-lucide="box" class="w-4 h-4 text-brand-600"></i>
                </div>
            </div>
            <div class="mt-4 space-y-3">
                <div>
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($totalStockUnits, 0, ',', '.') }}</span>
                        <span class="text-xs font-semibold text-slate-500">unit</span>
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">Total stok fisik</p>
                </div>
                <div class="pt-2.5 border-t border-slate-100">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-lg font-bold text-brand-700">Rp {{ number_format($inventoryValue / 1000000, 1, ',', '.') }}</span>
                        <span class="text-xs font-semibold text-slate-500">Juta</span>
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">Estimasi nilai persediaan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Barang Prioritas Restock (Full Width) -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs">
        <!-- Section Header -->
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Barang Prioritas Restock</h3>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Item dengan stok berada di bawah batas minimum keamanan</p>
            </div>
            <a href="{{ route('products.index', ['status' => 'menipis']) }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 flex items-center gap-1 bg-brand-50 px-3 py-1.5 rounded-lg border border-brand-200/50 transition-all hover:bg-brand-100">
                <span>Lihat Semua ({{ $totalCriticalCount }})</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[11px] font-bold">
                        <th class="pb-3 font-semibold">NAMA BARANG</th>
                        <th class="pb-3 text-center font-semibold">SISA STOK</th>
                        <th class="pb-3 text-center font-semibold">BATAS MIN.</th>
                        <th class="pb-3 font-semibold">SUPPLIER</th>
                        <th class="pb-3 text-right font-semibold">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($restockProducts as $item)
                    <tr class="hover:bg-amber-50/40 transition-colors">
                        <td class="py-3.5 pr-2">
                            <p class="font-bold text-slate-900 text-sm">{{ $item->name }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">SKU: {{ $item->sku }} • {{ $item->category }}</p>
                        </td>
                        <td class="py-3.5 px-2 text-center">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $item->stock <= 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $item->stock }} {{ strtolower($item->unit) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-2 text-center font-semibold text-slate-600">
                            {{ $item->min_stock }} {{ strtolower($item->unit) }}
                        </td>
                        <td class="py-3.5 px-2 font-medium text-slate-600">
                            {{ $item->supplier_name ?? 'Supplier Direct' }}
                        </td>
                        <td class="py-3.5 pl-2 text-right">
                            <a href="{{ route('stok.index') }}" class="inline-flex items-center gap-1.5 bg-brand-50 hover:bg-brand-100 text-brand-800 font-semibold px-3 py-1.5 rounded-lg text-xs border border-brand-200/50 transition-all">
                                <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                <span>Pesan</span>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Table Footer -->
        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
            <span>Menampilkan {{ count($restockProducts) }} dari {{ $totalCriticalCount }} barang kritis</span>
            <a href="#" class="text-brand-700 font-bold hover:underline">Cetak Daftar Belanja</a>
        </div>
    </div>

    <!-- Bottom Banner: Cek Fisik Cepat & Barcode Scanner -->
    <div class="bg-gradient-to-r from-brand-50 to-emerald-50 border border-brand-200/50 rounded-2xl p-5 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-5">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center shrink-0 border border-brand-200">
                <i data-lucide="qr-code" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 text-sm">Cek Fisik Cepat & Barcode Scanner</h4>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Hubungkan scanner USB atau gunakan kamera tablet untuk verifikasi rak tanpa membuka menu stok</p>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <button class="flex-1 md:flex-none px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 transition-all text-center">
                Export Excel
            </button>
            <button @click="barcodeScannerOpen = true" class="flex-1 md:flex-none justify-center px-4 py-2 bg-brand-dark hover:bg-brand-800 text-white text-xs font-semibold rounded-xl flex items-center gap-2 shadow-sm transition-all">
                <i data-lucide="scan" class="w-4 h-4"></i>
                <span>Mulai Pindai</span>
            </button>
        </div>
    </div>

</div>
@endsection
