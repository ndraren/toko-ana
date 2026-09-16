@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Page Header & Period Filter -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Ringkasan Toko</h2>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Operasional Normal
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1 font-medium">
                Kamis, 24 Oktober 2024 • Sinkronisasi data kasir otomatis aktif
            </p>
        </div>

        <!-- Period Toggle Buttons -->
        <div class="bg-slate-100 p-1 rounded-xl flex items-center gap-1 text-xs font-semibold text-slate-600">
            <a href="?period=today" class="px-3 py-1.5 rounded-lg {{ $timeFilter == 'today' ? 'bg-white text-slate-900 shadow-xs' : 'hover:text-slate-900' }}">Hari Ini</a>
            <a href="?period=week" class="px-3 py-1.5 rounded-lg {{ $timeFilter == 'week' ? 'bg-white text-slate-900 shadow-xs' : 'hover:text-slate-900' }}">Pekan Ini</a>
            <a href="?period=month" class="px-3 py-1.5 rounded-lg {{ $timeFilter == 'month' ? 'bg-white text-slate-900 shadow-xs' : 'hover:text-slate-900' }}">Bulan Ini</a>
        </div>
    </div>

    <!-- 4 Key Summary Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Stok Fisik -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-xs font-semibold text-slate-500">Total Stok Fisik</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                    <i data-lucide="box" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-3xl font-black text-slate-900 tracking-tight">14.820</span>
                    <span class="text-xs font-semibold text-slate-500">unit</span>
                </div>
                <div class="mt-2 flex items-center gap-1 text-xs font-semibold text-emerald-600">
                    <i data-lucide="arrow-down-right" class="w-3.5 h-3.5"></i>
                    <span>+12 unit masuk shift ini</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Nilai Persediaan -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-xs font-semibold text-slate-500">Nilai Persediaan</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-3xl font-black text-slate-900 tracking-tight">Rp 84,6</span>
                    <span class="text-xs font-semibold text-slate-500">Jt</span>
                </div>
                <div class="mt-2 flex items-center gap-1 text-xs font-medium text-slate-500">
                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Audit fisik 2 hari lalu</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Perlu Restock -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-xs font-semibold text-slate-500">Perlu Restock</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-slate-500"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-3xl font-black text-slate-900 tracking-tight">6</span>
                    <span class="text-xs font-semibold text-slate-500">produk kritis</span>
                </div>
                <div class="mt-2 inline-flex items-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-semibold">
                    Perlu PO segera
                </div>
            </div>
        </div>

        <!-- Card 4: Penjualan Hari Ini -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-xs font-semibold text-slate-500">Penjualan Hari Ini</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-1.5">
                    <span class="text-3xl font-black text-slate-900 tracking-tight">Rp 4,12</span>
                    <span class="text-xs font-semibold text-slate-500">Jt</span>
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-xs font-medium text-slate-500">
                    <span class="font-bold text-slate-800">142</span>
                    <span>struk transaksi tercatat</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid (2 Columns: Restock Table & Recent Movements Timeline) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left Section: Barang Prioritas Restock (8 Columns) -->
        <div class="lg:col-span-7 bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
                <!-- Section Header -->
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Barang Prioritas Restock</h3>
                        <p class="text-xs text-slate-400 mt-0.5 font-medium">Item dengan stok berada di bawah batas minimum keamanan</p>
                    </div>
                    <a href="{{ route('products.index', ['status' => 'menipis']) }}" class="text-xs font-bold text-brand-700 hover:text-brand-800 flex items-center gap-1">
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
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 pr-2">
                                    <p class="font-bold text-slate-900 text-sm">{{ $item->name }}</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">SKU: {{ $item->sku }} • {{ $item->category }}</p>
                                </td>
                                <td class="py-3.5 px-2 text-center">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
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
                                    <a href="{{ route('stok.index') }}" class="inline-flex items-center gap-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold px-3 py-1.5 rounded-lg text-xs transition-all">
                                        <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                        <span>Pesan</span>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Table Footer -->
            <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
                <span>Menampilkan {{ count($restockProducts) }} dari {{ $totalCriticalCount }} barang kritis</span>
                <a href="#" class="text-brand-700 font-bold hover:underline">Cetak Daftar Belanja</a>
            </div>
        </div>

        <!-- Right Section: Aktivitas Terakhir (5 Columns) -->
        <div class="lg:col-span-5 bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
            <div>
                <!-- Section Header -->
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Aktivitas Terakhir</h3>
                        <p class="text-xs text-slate-400 mt-0.5 font-medium">Aliran pergerakan stok shift pagi</p>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                        <i data-lucide="history" class="w-4 h-4"></i>
                    </div>
                </div>

                <!-- Timeline Items -->
                <div class="space-y-4">
                    @foreach($recentMovements as $mv)
                    <div class="flex items-start gap-3.5">
                        <!-- Icon Circle -->
                        <div class="w-9 h-9 rounded-full shrink-0 flex items-center justify-center mt-0.5
                            @if($mv->type == 'in') bg-emerald-100 text-emerald-700
                            @elseif($mv->type == 'damaged') bg-red-100 text-red-700
                            @elseif($mv->type == 'return') bg-blue-100 text-blue-700
                            @else bg-slate-100 text-slate-700 @endif">
                            @if($mv->type == 'in')
                                <i data-lucide="arrow-down-left" class="w-4 h-4"></i>
                            @elseif($mv->type == 'damaged')
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            @elseif($mv->type == 'return')
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            @else
                                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                            @endif
                        </div>

                        <!-- Item details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-baseline justify-between">
                                <h4 class="font-bold text-slate-900 text-xs truncate">{{ $mv->product->name ?? 'Produk' }}</h4>
                                <span class="font-black text-xs shrink-0 ml-2
                                    @if($mv->type == 'in') text-emerald-700
                                    @elseif($mv->type == 'damaged') text-red-700
                                    @else text-slate-900 @endif">
                                    {{ $mv->formatted_change }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $mv->source_category }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                {{ $mv->created_at->format('H.i') }} WIB • oleh {{ $mv->user_name }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Footer Button -->
            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('stok.index') }}" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200/80 text-slate-700 font-semibold text-xs rounded-xl flex items-center justify-center gap-1.5 transition-all">
                    <span>Buka Mutasi Lengkap</span>
                    <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Bottom Banner: Cek Fisik Cepat & Barcode Scanner -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200">
                <i data-lucide="qr-code" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 text-sm">Cek Fisik Cepat & Barcode Scanner</h4>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Hubungkan scanner USB atau gunakan kamera tablet untuk verifikasi rak tanpa membuka menu stok</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl border border-slate-200 transition-all">
                Export Excel
            </button>
            <button @click="barcodeScannerOpen = true" class="px-4 py-2 bg-brand-dark hover:bg-brand-800 text-white text-xs font-semibold rounded-xl flex items-center gap-2 shadow-xs transition-all">
                <i data-lucide="scan" class="w-4 h-4"></i>
                <span>Mulai Pindai</span>
            </button>
        </div>
    </div>
</div>
@endsection
