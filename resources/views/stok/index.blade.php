@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ movementType: 'in', quantity: 12 }">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <span>LOGISTIK TOKO</span>
                <span>•</span>
                <span class="text-brand-600">Pembaruan Realtime</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Catat Stok Barang</h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Input pergerakan barang masuk dari supplier atau barang keluar secara ringkas.</p>
        </div>

        <!-- Mode Toggle Tabs -->
        <div class="bg-slate-100 p-1 rounded-xl flex items-center gap-1 text-xs font-semibold text-slate-600">
            <button @click="movementType = 'in'" 
                    :class="movementType === 'in' ? 'bg-white text-emerald-800 shadow-xs' : 'hover:text-slate-900'"
                    class="px-4 py-2 rounded-lg flex items-center gap-2 transition-all">
                <i data-lucide="arrow-down-left" class="w-4 h-4 text-emerald-600"></i>
                <span>Stok Masuk (Restock)</span>
            </button>
            <button @click="movementType = 'out'" 
                    :class="movementType === 'out' ? 'bg-white text-slate-900 shadow-xs' : 'hover:text-slate-900'"
                    class="px-4 py-2 rounded-lg flex items-center gap-2 transition-all">
                <i data-lucide="arrow-up-right" class="w-4 h-4 text-slate-500"></i>
                <span>Stok Keluar / Retur</span>
            </button>
        </div>
    </div>

    <!-- Form Input Box Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs">
        <form action="{{ route('stok.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" x-model="movementType">

            <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                <!-- Select Product / Barcode Search -->
                <div class="md:col-span-6">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-slate-700">Pilih Nama Barang / Barcode</label>
                        <span class="text-[10px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">Tekan F2 utk scan</span>
                    </div>
                    <div class="relative">
                        <i data-lucide="scan-barcode" class="w-5 h-5 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <select name="product_id" required 
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-11 pr-10 py-2.5 text-sm text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 appearance-none">
                            <option value="" disabled selected>Cari nama atau barcode barang...</option>
                            @foreach($allProducts as $p)
                                <option value="{{ $p->id }}" {{ $loop->first ? 'selected' : '' }}>
                                    {{ $p->name }} (SKU: {{ $p->sku }}) - Stok: {{ $p->stock }} {{ $p->unit }}
                                </option>
                            @endforeach
                        </select>
                        <i data-lucide="scan" class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2"></i>
                    </div>
                </div>

                <!-- Quantity Counter -->
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Jumlah Kuantitas</label>
                    <div class="flex items-center bg-slate-50 border border-slate-200 rounded-xl p-1">
                        <button type="button" @click="if(quantity > 1) quantity--" class="w-9 h-8 rounded-lg bg-white shadow-xs border border-slate-200 flex items-center justify-center font-bold text-slate-700 hover:bg-slate-100">
                            -
                        </button>
                        <input type="number" name="quantity" x-model="quantity" min="1" required 
                               class="w-full text-center bg-transparent border-none text-sm font-bold text-slate-900 focus:outline-none">
                        <button type="button" @click="quantity++" class="w-9 h-8 rounded-lg bg-white shadow-xs border border-slate-200 flex items-center justify-center font-bold text-slate-700 hover:bg-slate-100">
                            +
                        </button>
                    </div>
                </div>

                <!-- Unit Selection -->
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Satuan Unit</label>
                    <select name="unit" required 
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        <option value="Dus (Karton)" selected>Dus (Karton)</option>
                        <option value="Pouch">Pouch</option>
                        <option value="Botol">Botol</option>
                        <option value="Bungkus">Bungkus</option>
                        <option value="Kg">Kg</option>
                        <option value="Renceng">Renceng</option>
                        <option value="Kotak">Kotak</option>
                        <option value="Slop">Slop</option>
                        <option value="Unit">Unit</option>
                    </select>
                </div>

                <!-- Source / Transaction Category -->
                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Asal / Kategori Transaksi</label>
                    <select name="source_category" required 
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        <option value="Supplier: PT Indomarco Adi Prima" selected>Supplier: PT Indomarco Adi Prima</option>
                        <option value="Grosir Pasar Induk">Grosir Pasar Induk</option>
                        <option value="Retur Supplier">Retur Supplier</option>
                        <option value="Barang Rusak / Pecah">Barang Rusak / Pecah</option>
                        <option value="Kasir POS 1 • Penjualan">Kasir POS 1 • Penjualan</option>
                    </select>
                </div>

                <!-- Transaction Time -->
                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Waktu Transaksi</label>
                    <div class="relative">
                        <input type="text" value="Hari ini, {{ now()->format('H:i') }} WIB" readonly 
                               class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-3.5 pr-9 py-2.5 text-sm font-semibold text-slate-700">
                        <i data-lucide="clock" class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2"></i>
                    </div>
                </div>

                <!-- Notes / Remarks -->
                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Singkat (Opsional)</label>
                    <input type="text" name="notes" placeholder="Cth: Faktur #INV-8891, Exp. Mar 2026" 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                </div>
            </div>

            <!-- Form Actions -->
            <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <i data-lucide="info" class="w-4 h-4 text-brand-600"></i>
                    <span>Stok live otomatis diperbarui di database toko.</span>
                </div>

                <div class="flex items-center gap-3">
                    <button type="reset" class="px-4 py-2.5 text-slate-500 hover:text-slate-800 font-semibold text-xs rounded-xl transition-all">
                        Kosongkan
                    </button>
                    <button type="submit" class="bg-brand-dark hover:bg-brand-800 text-white font-bold text-xs px-5 py-2.5 rounded-xl flex items-center gap-2 shadow-xs transition-all">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Simpan Catatan Stok</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- 3 Summary Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: Masuk Hari Ini -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500">Masuk Hari Ini</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1 tracking-tight">+148 Dus / Pcs</h3>
            </div>
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                <i data-lucide="arrow-down-left" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Card 2: Keluar / Penjualan -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500">Keluar / Penjualan</p>
                <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight">-312 Unit</h3>
            </div>
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center border border-slate-200">
                <i data-lucide="arrow-up-right" class="w-6 h-6"></i>
            </div>
        </div>

        <!-- Card 3: Barang Rusak / Kadaluarsa -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500">Barang Rusak / Kadaluarsa</p>
                <h3 class="text-2xl font-black text-red-600 mt-1 tracking-tight">2 Item</h3>
            </div>
            <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center border border-red-100">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Table Section: Riwayat Catatan Hari Ini -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Table Header & Filter Tabs -->
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Riwayat Catatan Hari Ini</h3>
                <p class="text-xs text-slate-400 mt-0.5 font-medium">Log aktivitas pergudangan shift pagi • {{ now()->format('d F Y') }}</p>
            </div>

            <!-- Filter Pills -->
            <div class="bg-slate-100 p-1 rounded-xl flex items-center gap-1 text-xs font-semibold text-slate-600">
                <a href="?filter=all" class="px-3 py-1 rounded-lg {{ $filter == 'all' ? 'bg-white text-slate-900 shadow-xs' : 'hover:text-slate-900' }}">Semua (24)</a>
                <a href="?filter=in" class="px-3 py-1 rounded-lg {{ $filter == 'in' ? 'bg-white text-slate-900 shadow-xs' : 'hover:text-slate-900' }}">Masuk</a>
                <a href="?filter=out" class="px-3 py-1 rounded-lg {{ $filter == 'out' ? 'bg-white text-slate-900 shadow-xs' : 'hover:text-slate-900' }}">Keluar</a>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[11px] font-bold">
                        <th class="py-3 font-semibold w-24">JAM</th>
                        <th class="py-3 font-semibold">NAMA BARANG & SPESIFIKASI</th>
                        <th class="py-3 font-semibold">KATEGORI / SUMBER</th>
                        <th class="py-3 text-center font-semibold">PERUBAHAN STOK</th>
                        <th class="py-3 text-right font-semibold">PETUGAS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($movements as $m)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 font-medium text-slate-500">
                            {{ $m->created_at->format('H:i') }} WIB
                        </td>
                        <td class="py-4">
                            <p class="font-bold text-slate-900 text-sm">{{ $m->product->name ?? 'Produk' }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">SKU: {{ $m->product->sku ?? '-' }} • {{ $m->notes ?? 'Pembaruan Stok' }}</p>
                        </td>
                        <td class="py-4">
                            <span class="px-2.5 py-1 rounded-md text-xs font-semibold
                                @if($m->type == 'damaged') bg-red-100 text-red-700
                                @elseif($m->type == 'return') bg-blue-100 text-blue-700
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ $m->source_category }}
                            </span>
                        </td>
                        <td class="py-4 text-center">
                            <span class="inline-block px-3 py-1 rounded-lg text-xs font-extrabold
                                @if($m->type == 'in') bg-emerald-100 text-emerald-800
                                @elseif($m->type == 'damaged') bg-red-100 text-red-800
                                @elseif($m->type == 'return') bg-blue-100 text-blue-800
                                @else bg-slate-100 text-slate-800 @endif">
                                {{ $m->formatted_change }}
                            </span>
                        </td>
                        <td class="py-4 text-right font-semibold text-slate-700 flex items-center justify-end gap-1.5">
                            <span>{{ $m->user_name }}</span>
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
