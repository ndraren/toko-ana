@extends('layouts.app')

@section('title', 'Stok Barang')

@section('content')
<div x-data="stokBarangApp()" class="space-y-5 max-w-[1600px] mx-auto" x-cloak>

    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition>
        <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
        <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
        <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-700"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3" x-data="{ show: true }" x-show="show" x-transition>
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 shrink-0"></i>
        <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
        <button @click="show = false" class="ml-auto text-red-400 hover:text-red-700"><i data-lucide="x" class="w-4 h-4"></i></button>
    </div>
    @endif

    <!-- ============================================ -->
    <!-- PAGE HEADER + ALERT BADGES                   -->
    <!-- ============================================ -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <span>INVENTARIS & LOGISTIK</span>
                <span>•</span>
                <span class="text-brand-700 font-semibold">SIKOL SYSTEM</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Stok Barang</h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">
                Kelola data inventaris, stok, harga barang, dan pencatatan stok masuk dari supplier.
            </p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            {{-- Alert Cards --}}
            @if($menipisCount + $habisCount > 0)
            <div class="bg-white rounded-xl border border-amber-200 p-2.5 flex items-center gap-2.5 shadow-xs">
                <div class="w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                    <i data-lucide="package-x" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-slate-500">Perlu Restock</p>
                    <p class="text-base font-bold text-amber-700">{{ number_format($menipisCount + $habisCount, 0, ',', '.') }}</p>
                </div>
            </div>
            @endif

            @if($nearExpiryCount > 0)
            <div class="bg-white rounded-xl border border-rose-200 p-2.5 flex items-center gap-2.5 shadow-xs">
                <div class="w-9 h-9 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600">
                    <i data-lucide="calendar-clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-slate-500">Hampir Expired</p>
                    <p class="text-base font-bold text-rose-700">{{ $nearExpiryCount }}</p>
                </div>
            </div>
            @endif

            <div class="bg-white rounded-xl border border-slate-200 p-2.5 flex items-center gap-2.5 shadow-xs">
                <div class="w-9 h-9 rounded-lg bg-brand-50 flex items-center justify-center text-brand-600">
                    <i data-lucide="package" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-slate-500">Total SKU</p>
                    <p class="text-base font-bold text-slate-800">{{ number_format($totalCount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- MAIN TAB NAVIGATION                          -->
    <!-- ============================================ -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
        {{-- Tab Bar --}}
        <div class="flex items-center border-b border-slate-200/80 px-5 pt-1">
            <a href="{{ route('products.index', ['tab' => 'data-barang']) }}" 
               class="px-4 py-3 text-sm font-bold transition-all border-b-2 flex items-center gap-2 {{ $tab == 'data-barang' ? 'border-brand-600 text-brand-800' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                <i data-lucide="database" class="w-4 h-4"></i>
                <span>Data Inventaris</span>
                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold {{ $tab == 'data-barang' ? 'bg-brand-100 text-brand-800' : 'bg-slate-100 text-slate-500' }}">{{ $totalCount }}</span>
            </a>
            <a href="{{ route('products.index', ['tab' => 'stok-masuk']) }}" 
               class="px-4 py-3 text-sm font-bold transition-all border-b-2 flex items-center gap-2 {{ $tab == 'stok-masuk' ? 'border-brand-600 text-brand-800' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                <i data-lucide="package-plus" class="w-4 h-4"></i>
                <span>Catat Stok Masuk</span>
            </a>
        </div>

        {{-- ======================================== --}}
        {{-- TAB 1: DATA INVENTARIS (PRODUCTS TABLE)  --}}
        {{-- ======================================== --}}
        @if($tab === 'data-barang')
        <div class="p-5 space-y-5">
            {{-- Controls bar --}}
            <form method="GET" action="{{ route('products.index') }}" class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <input type="hidden" name="tab" value="data-barang">
                <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                    <div class="relative w-full md:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau SKU..." class="pl-10 w-full rounded-lg border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500 py-2">
                    </div>
                    
                    <select name="category" class="w-full md:w-48 rounded-lg border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500 py-2" onchange="this.form.submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    
                    <button type="submit" class="hidden">Submit</button>
                </div>
                
                <div class="flex items-center gap-3 w-full md:w-auto">
                    <button type="button" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 border border-slate-200 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Export
                    </button>
                    <button type="button" @click="openAddModal()" class="flex-1 md:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition-colors">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Tambah Barang
                    </button>
                </div>
            </form>

            {{-- Status Filter Pills --}}
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('products.index', ['status' => '', 'tab' => 'data-barang']) }}" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors border {{ empty($status) ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
                    Semua ({{ number_format($totalCount, 0, ',', '.') }})
                </a>
                <a href="{{ route('products.index', ['status' => 'Aman', 'tab' => 'data-barang']) }}" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors border {{ $status == 'Aman' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-emerald-700 border-slate-200 hover:bg-emerald-50' }}">
                    Stok Aman ({{ number_format($amanCount, 0, ',', '.') }})
                </a>
                <a href="{{ route('products.index', ['status' => 'Menipis', 'tab' => 'data-barang']) }}" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors border {{ $status == 'Menipis' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-amber-700 border-slate-200 hover:bg-amber-50' }}">
                    Menipis ({{ number_format($menipisCount, 0, ',', '.') }})
                </a>
                <a href="{{ route('products.index', ['status' => 'Habis', 'tab' => 'data-barang']) }}" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors border {{ $status == 'Habis' ? 'bg-rose-500 text-white border-rose-500' : 'bg-white text-rose-700 border-slate-200 hover:bg-rose-50' }}">
                    Habis ({{ number_format($habisCount, 0, ',', '.') }})
                </a>
            </div>

            {{-- Products Table --}}
            <div class="border border-slate-200/80 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
                        <thead class="bg-slate-50/80 border-b border-slate-200 text-xs uppercase font-medium text-slate-500">
                            <tr>
                                <th class="px-4 py-3 w-10 text-center"><input type="checkbox" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"></th>
                                <th class="px-4 py-3">NAMA BARANG & SKU</th>
                                <th class="px-4 py-3">KATEGORI</th>
                                <th class="px-4 py-3">STOK</th>
                                <th class="px-4 py-3">SATUAN</th>
                                <th class="px-4 py-3">KADALUARSA</th>
                                <th class="px-4 py-3 text-right">HARGA MODAL</th>
                                <th class="px-4 py-3 text-right">HARGA JUAL</th>
                                <th class="px-4 py-3 text-center">KEMASAN</th>
                                <th class="px-4 py-3 text-center">STATUS</th>
                                <th class="px-4 py-3 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($products as $p)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 text-center"><input type="checkbox" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"></td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-800">{{ $p->name }}</div>
                                    <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $p->sku }} {{ $p->barcode ? '• '.$p->barcode : '' }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                        {{ $p->category }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-medium {{ $p->stock <= 0 ? 'text-rose-600' : ($p->stock <= $p->min_stock ? 'text-amber-600' : 'text-slate-700') }}">
                                    {{ number_format($p->stock, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3">{{ $p->unit }}</td>
                                <td class="px-4 py-3">
                                    @if($p->expiry_date)
                                        @php
                                            $expDate = \Carbon\Carbon::parse($p->expiry_date);
                                            $isExpired = $expDate->isPast();
                                            $isNear = $expDate->diffInDays(now()) <= 30 && !$isExpired;
                                        @endphp
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-md {{ $isExpired ? 'bg-red-100 text-red-700' : ($isNear ? 'bg-amber-100 text-amber-700' : 'text-slate-600 bg-slate-100') }}">
                                            @if($isExpired || $isNear)
                                                <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                            @endif
                                            {{ $expDate->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-slate-700">Rp {{ number_format($p->cost_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right font-medium text-brand-600">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if(count($p->units) > 1)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                            +{{ count($p->units) - 1 }} satuan
                                        </span>
                                    @else
                                        <span class="text-slate-300">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $p->status_badge_class }}">
                                        {{ $p->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right space-x-1">
                                    <button @click="openEditModal({{ $p->id }})" class="p-1.5 text-slate-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors" title="Edit">
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    </button>
                                    
                                    <div x-data="{ popoverOpen: false }" class="relative inline-block text-left">
                                        <button @click="popoverOpen = !popoverOpen" @click.away="popoverOpen = false" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                        
                                        <div x-show="popoverOpen" x-transition.opacity class="absolute right-0 bottom-full mb-2 w-64 bg-white rounded-xl shadow-lg border border-slate-200 p-4 z-10" style="display: none;">
                                            <p class="text-sm text-slate-700 font-medium mb-1">Hapus Produk?</p>
                                            <p class="text-xs text-slate-500 mb-3 whitespace-normal">Tindakan ini tidak dapat dibatalkan. Riwayat penjualan untuk produk ini akan tetap ada.</p>
                                            <div class="flex gap-2 justify-end">
                                                <button type="button" @click="popoverOpen = false" class="px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">Batal</button>
                                                <form method="POST" action="{{ route('products.destroy', $p->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-rose-600 text-white hover:bg-rose-700 rounded-lg transition-colors">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="px-4 py-8 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i data-lucide="inbox" class="w-12 h-12 text-slate-300 mb-3"></i>
                                        <p class="font-medium text-slate-600">Tidak ada data produk</p>
                                        <p class="text-sm mt-1 text-slate-400">Silakan tambah produk baru atau ubah filter pencarian.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($products->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $products->appends(['tab' => 'data-barang', 'search' => $search, 'category' => $category, 'status' => $status])->links() }}
                </div>
                @endif
            </div>
        </div>

        @else
        {{-- ======================================== --}}
        {{-- TAB 2: CATAT STOK MASUK                  --}}
        {{-- ======================================== --}}
        <div class="p-5 space-y-5">

            {{-- Header with AI Scanner Button --}}
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg tracking-tight">Catat Stok Masuk</h3>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Input pergerakan barang masuk dari supplier secara ringkas.</p>
                </div>
                <div class="flex items-center gap-3">
                    {{-- AI Scan Faktur Button --}}
                    <button @click="openScanModal()" type="button"
                        class="bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl flex items-center gap-2.5 shadow-lg shadow-violet-200 transition-all hover:shadow-xl hover:shadow-violet-300 hover:-translate-y-0.5">
                        <i data-lucide="scan-eye" class="w-4 h-4"></i>
                        <span>Scan Faktur (AI)</span>
                        <span class="bg-white/20 text-[10px] px-1.5 py-0.5 rounded font-bold">BARU</span>
                    </button>

                    <div class="bg-emerald-50 border border-emerald-200 p-1 rounded-xl flex items-center gap-1 text-xs font-semibold text-emerald-800">
                        <div class="px-4 py-2 rounded-lg flex items-center gap-2 bg-white shadow-xs">
                            <i data-lucide="arrow-down-left" class="w-4 h-4 text-emerald-600"></i>
                            <span>Stok Masuk (Restock)</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Manual Input Form --}}
            <div class="bg-slate-50/50 border border-slate-200/80 rounded-2xl p-6">
                <form action="{{ route('stok.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="in">

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
                                        class="w-full bg-white border border-slate-200 rounded-xl pl-11 pr-10 py-2.5 text-sm text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 appearance-none">
                                    <option value="" disabled selected>Cari nama atau barcode barang...</option>
                                    @foreach($allProducts as $ap)
                                        <option value="{{ $ap->id }}">
                                            {{ $ap->name }} (SKU: {{ $ap->sku }}) - Stok: {{ $ap->stock }} {{ $ap->unit }}
                                        </option>
                                    @endforeach
                                </select>
                                <i data-lucide="scan" class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2"></i>
                            </div>
                        </div>

                        <!-- Quantity Counter -->
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Jumlah Kuantitas</label>
                            <div class="flex items-center bg-white border border-slate-200 rounded-xl p-1">
                                <button type="button" @click="if(quantity > 1) quantity--" class="w-9 h-8 rounded-lg bg-slate-50 shadow-xs border border-slate-200 flex items-center justify-center font-bold text-slate-700 hover:bg-slate-100">-</button>
                                <input type="number" name="quantity" x-model="quantity" min="1" required class="w-full text-center bg-transparent border-none text-sm font-bold text-slate-900 focus:outline-none">
                                <button type="button" @click="quantity++" class="w-9 h-8 rounded-lg bg-slate-50 shadow-xs border border-slate-200 flex items-center justify-center font-bold text-slate-700 hover:bg-slate-100">+</button>
                            </div>
                        </div>

                        <!-- Unit Selection -->
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Satuan Unit</label>
                            <select name="unit" required class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
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
                            <select name="source_category" required class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
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
                                       class="w-full bg-white border border-slate-200 rounded-xl pl-3.5 pr-9 py-2.5 text-sm font-semibold text-slate-700">
                                <i data-lucide="clock" class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2"></i>
                            </div>
                        </div>

                        <!-- Notes / Remarks -->
                        <div class="md:col-span-4">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Singkat (Opsional)</label>
                            <input type="text" name="notes" placeholder="Cth: Faktur #INV-8891, Exp. Mar 2026" 
                                   class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-6 pt-4 border-t border-slate-200/60 flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                            <i data-lucide="info" class="w-4 h-4 text-brand-600"></i>
                            <span>Stok live otomatis diperbarui di database toko.</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="reset" class="px-4 py-2.5 text-slate-500 hover:text-slate-800 font-semibold text-xs rounded-xl transition-all">Kosongkan</button>
                            <button type="submit" class="bg-brand-dark hover:bg-brand-800 text-white font-bold text-xs px-5 py-2.5 rounded-xl flex items-center gap-2 shadow-xs transition-all">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                <span>Simpan Catatan Stok</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- 3 Summary Stat Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Masuk Hari Ini</p>
                        <h3 class="text-2xl font-black text-emerald-600 mt-1 tracking-tight">+{{ $todayIn }} Unit</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                        <i data-lucide="arrow-down-left" class="w-6 h-6"></i>
                    </div>
                </div>
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Keluar / Penjualan</p>
                        <h3 class="text-2xl font-black text-slate-900 mt-1 tracking-tight">-{{ $todayOut }} Unit</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center border border-slate-200">
                        <i data-lucide="arrow-up-right" class="w-6 h-6"></i>
                    </div>
                </div>
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500">Barang Rusak / Kadaluarsa</p>
                        <h3 class="text-2xl font-black text-red-600 mt-1 tracking-tight">{{ $todayDamaged }} Item</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-red-50 text-red-600 flex items-center justify-center border border-red-100">
                        <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            {{-- Movement History Table --}}
            <div class="border border-slate-200/80 rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3.5 bg-slate-50/80 border-b border-slate-200/80">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Riwayat Catatan Hari Ini</h3>
                        <p class="text-xs text-slate-400 mt-0.5 font-medium">Log aktivitas pergudangan • {{ now()->format('d F Y') }}</p>
                    </div>
                    <div class="bg-slate-100 p-1 rounded-xl flex items-center gap-1 text-xs font-semibold text-slate-600">
                        <a href="{{ route('products.index', ['tab' => 'stok-masuk', 'filter' => 'all']) }}" class="px-3 py-1 rounded-lg {{ $filter == 'all' ? 'bg-white text-slate-900 shadow-xs' : 'hover:text-slate-900' }}">Semua</a>
                        <a href="{{ route('products.index', ['tab' => 'stok-masuk', 'filter' => 'in']) }}" class="px-3 py-1 rounded-lg {{ $filter == 'in' ? 'bg-white text-slate-900 shadow-xs' : 'hover:text-slate-900' }}">Masuk</a>
                        <a href="{{ route('products.index', ['tab' => 'stok-masuk', 'filter' => 'out']) }}" class="px-3 py-1 rounded-lg {{ $filter == 'out' ? 'bg-white text-slate-900 shadow-xs' : 'hover:text-slate-900' }}">Keluar</a>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[11px] font-bold">
                                <th class="py-3 px-4 font-semibold w-24">JAM</th>
                                <th class="py-3 px-4 font-semibold">NAMA BARANG & SPESIFIKASI</th>
                                <th class="py-3 px-4 font-semibold">KATEGORI / SUMBER</th>
                                <th class="py-3 px-4 text-center font-semibold">PERUBAHAN STOK</th>
                                <th class="py-3 px-4 text-right font-semibold">PETUGAS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($movements as $m)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-4 font-medium text-slate-500">
                                    {{ $m->created_at->format('H:i') }} WIB
                                </td>
                                <td class="py-4 px-4">
                                    <p class="font-bold text-slate-900 text-sm">{{ $m->product->name ?? 'Produk' }}</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">SKU: {{ $m->product->sku ?? '-' }} • {{ $m->notes ?? 'Pembaruan Stok' }}</p>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold
                                        @if($m->type == 'damaged') bg-red-100 text-red-700
                                        @elseif($m->type == 'return') bg-blue-100 text-blue-700
                                        @else bg-slate-100 text-slate-700 @endif">
                                        {{ $m->source_category }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-block px-3 py-1 rounded-lg text-xs font-extrabold
                                        @if($m->type == 'in') bg-emerald-100 text-emerald-800
                                        @elseif($m->type == 'damaged') bg-red-100 text-red-800
                                        @elseif($m->type == 'return') bg-blue-100 text-blue-800
                                        @else bg-slate-100 text-slate-800 @endif">
                                        {{ $m->formatted_change }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right font-semibold text-slate-700 flex items-center justify-end gap-1.5">
                                    <span>{{ $m->user_name }}</span>
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                                            <i data-lucide="inbox" class="w-6 h-6"></i>
                                        </div>
                                        <p class="text-slate-500 font-bold text-xs">Belum ada catatan stok hari ini.</p>
                                        <p class="text-slate-400 text-[11px]">Mulai catat stok masuk menggunakan form di atas.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- ============================================ -->
    <!-- BOTTOM SHORTCUT BAR                          -->
    <!-- ============================================ -->
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900/90 backdrop-blur-sm text-white px-6 py-3 rounded-full shadow-lg flex items-center gap-6 z-40">
        <button type="button" @click="openAddModal()" class="flex items-center gap-2 hover:text-brand-300 transition-colors">
            <span class="w-6 h-6 rounded bg-white/20 flex items-center justify-center text-xs font-bold font-mono">N</span>
            <span class="text-sm font-medium">Barang Baru</span>
        </button>
        <div class="w-px h-4 bg-slate-700"></div>
        <a href="{{ route('products.index', ['tab' => 'stok-masuk']) }}" class="flex items-center gap-2 hover:text-brand-300 transition-colors">
            <span class="w-6 h-6 rounded bg-white/20 flex items-center justify-center text-xs font-bold font-mono">S</span>
            <span class="text-sm font-medium">Stok Masuk</span>
        </a>
        <div class="w-px h-4 bg-slate-700"></div>
        <button type="button" onclick="document.querySelector('input[name=search]')?.focus()" class="flex items-center gap-2 hover:text-brand-300 transition-colors">
            <span class="w-6 h-6 rounded bg-white/20 flex items-center justify-center text-xs font-bold font-mono">/</span>
            <span class="text-sm font-medium">Cari</span>
        </button>
    </div>

    <!-- ============================================ -->
    <!-- ADD PRODUCT MODAL                            -->
    <!-- ============================================ -->
    <div x-show="addModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="addModalOpen" x-transition.opacity @click="addModalOpen = false" class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm"></div>

            <div x-show="addModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">
                
                <form action="{{ route('products.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[85vh] overflow-y-auto">
                        <div class="flex items-start justify-between mb-6">
                            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <div class="p-2 bg-brand-50 text-brand-600 rounded-lg">
                                    <i data-lucide="plus" class="w-5 h-5"></i>
                                </div>
                                Tambah Barang Baru
                            </h3>
                            <button type="button" @click="addModalOpen = false" class="text-slate-400 hover:text-slate-500 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <!-- Basic Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-8">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Barang <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" required class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">SKU <span class="text-rose-500">*</span></label>
                                <input type="text" name="sku" required class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Barcode</label>
                                <input type="text" name="barcode" class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                                <input type="text" name="category" list="category-list" required class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                                <datalist id="category-list">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}">
                                    @endforeach
                                </datalist>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Satuan Dasar <span class="text-rose-500">*</span></label>
                                <input type="text" name="unit" x-model="baseUnitName" @input="updateBaseUnit('add')" required placeholder="Mis: Pcs, Kg, Dus" class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 bg-amber-50 border-amber-200 focus:border-amber-500 focus:ring-amber-500">
                                <p class="text-xs text-amber-600 mt-1">Satuan terkecil untuk stok produk ini.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Stok Awal <span class="text-rose-500">*</span></label>
                                <input type="number" name="stock" required min="0" step="0.01" class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Batas Min. Stok <span class="text-rose-500">*</span></label>
                                <input type="number" name="min_stock" required min="0" step="0.01" value="0" class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Tgl Kadaluarsa <span class="text-slate-400 text-xs font-normal">(opsional)</span></label>
                                <input type="date" name="expiry_date" class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Harga Modal (HPP) <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-slate-500 sm:text-sm">Rp</span>
                                    </div>
                                    <input type="number" name="cost_price" required min="0" class="pl-10 w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Supplier</label>
                                <input type="text" name="supplier_name" class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                            </div>
                        </div>
                        
                        <hr class="border-slate-200 my-6">

                        <!-- Units Repeater -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-base font-bold text-slate-800">Daftar Satuan & Harga Jual</h4>
                                    <p class="text-xs text-slate-500">Tentukan harga jual untuk satuan dasar dan tambahkan satuan kemasan lainnya (opsional).</p>
                                </div>
                                <button type="button" @click="addUnit('add')" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-brand-600 rounded-lg text-sm font-medium hover:bg-slate-200 transition-colors">
                                    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Satuan Kemasan
                                </button>
                            </div>

                            <div class="space-y-4">
                                <template x-for="(unit, index) in addUnits" :key="index">
                                    <div class="p-4 rounded-xl border relative" :class="index === 0 ? 'bg-amber-50/30 border-amber-200' : 'bg-slate-50 border-slate-200'">
                                        <div class="flex justify-between items-center mb-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold px-2 py-0.5 rounded-full" :class="index === 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-700'" x-text="index === 0 ? 'Satuan Dasar' : 'Satuan Kemasan #' + index"></span>
                                            </div>
                                            <button type="button" x-show="index > 0" @click="removeUnit('add', index)" class="text-rose-400 hover:text-rose-600 p-1 rounded-md hover:bg-rose-50 transition-colors" title="Hapus Satuan">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Nama Satuan <span class="text-rose-500">*</span></label>
                                                <input type="text" x-model="unit.unit_name" :name="`units[${index}][unit_name]`" required class="w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500" :readonly="index === 0" :class="index === 0 ? 'bg-slate-100 text-slate-500' : ''">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Konversi (Jml Sat. Dasar) <span class="text-rose-500">*</span></label>
                                                <div class="relative">
                                                    <input type="number" x-model="unit.conversion_factor" :name="`units[${index}][conversion_factor]`" required min="1" step="0.01" class="w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500" :readonly="index === 0" :class="index === 0 ? 'bg-slate-100 text-slate-500' : ''">
                                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none" x-show="index > 0">
                                                        <span class="text-slate-400 sm:text-xs" x-text="baseUnitName || 'Pcs'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Harga Eceran <span class="text-rose-500">*</span></label>
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none"><span class="text-slate-500 sm:text-xs">Rp</span></div>
                                                    <input type="number" x-model="unit.price_retail" :name="`units[${index}][price_retail]`" required min="0" class="pl-8 w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Harga Grosir</label>
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none"><span class="text-slate-500 sm:text-xs">Rp</span></div>
                                                    <input type="number" x-model="unit.price_wholesale" :name="`units[${index}][price_wholesale]`" min="0" class="pl-8 w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Min. Beli Grosir</label>
                                                <input type="number" x-model="unit.min_wholesale_qty" :name="`units[${index}][min_wholesale_qty]`" min="1" class="w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 flex justify-end gap-2 border-t border-slate-200 rounded-b-2xl">
                        <button type="button" @click="addModalOpen = false" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-brand-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-brand-700 transition-colors">Simpan Barang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- EDIT PRODUCT MODAL                           -->
    <!-- ============================================ -->
    <div x-show="editModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="editModalOpen" x-transition.opacity @click="editModalOpen = false" class="fixed inset-0 transition-opacity bg-slate-900/50 backdrop-blur-sm"></div>

            <div x-show="editModalOpen" 
                 x-transition:enter="ease-out duration-300" 
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave="ease-in duration-200" 
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                 class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">
                
                <form :action="`/barang/${activeItem.id}`" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 max-h-[85vh] overflow-y-auto">
                        <div class="flex items-start justify-between mb-6">
                            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <div class="p-2 bg-brand-50 text-brand-600 rounded-lg">
                                    <i data-lucide="pencil" class="w-5 h-5"></i>
                                </div>
                                Edit Data Barang
                            </h3>
                            <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-500 p-1 rounded-lg hover:bg-slate-100 transition-colors">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <!-- Basic Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 mb-8">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Barang <span class="text-rose-500">*</span></label>
                                <input type="text" name="name" x-model="activeItem.name" required class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">SKU <span class="text-rose-500">*</span></label>
                                <input type="text" name="sku" x-model="activeItem.sku" required class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Barcode</label>
                                <input type="text" name="barcode" x-model="activeItem.barcode" class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Kategori <span class="text-rose-500">*</span></label>
                                <input type="text" name="category" x-model="activeItem.category" list="edit-category-list" required class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                                <datalist id="edit-category-list">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}">
                                    @endforeach
                                </datalist>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Satuan Dasar <span class="text-rose-500">*</span></label>
                                <input type="text" name="unit" x-model="baseUnitName" @input="updateBaseUnit('edit')" required placeholder="Mis: Pcs, Kg, Dus" class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500 bg-amber-50 border-amber-200 focus:border-amber-500 focus:ring-amber-500">
                                <p class="text-xs text-amber-600 mt-1">Hati-hati mengubah satuan dasar karena mempengaruhi stok.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Stok <span class="text-rose-500">*</span></label>
                                <input type="number" name="stock" x-model="activeItem.stock" required min="0" step="0.01" class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Batas Min. Stok <span class="text-rose-500">*</span></label>
                                <input type="number" name="min_stock" x-model="activeItem.min_stock" required min="0" step="0.01" class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Tgl Kadaluarsa <span class="text-slate-400 text-xs font-normal">(opsional)</span></label>
                                <input type="date" name="expiry_date" x-model="activeItem.expiry_date" class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Harga Modal (HPP) <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-slate-500 sm:text-sm">Rp</span></div>
                                    <input type="number" name="cost_price" x-model="activeItem.cost_price" required min="0" class="pl-10 w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Supplier</label>
                                <input type="text" name="supplier_name" x-model="activeItem.supplier_name" class="w-full rounded-lg border-slate-200 focus:border-brand-500 focus:ring-brand-500">
                            </div>
                        </div>
                        
                        <hr class="border-slate-200 my-6">

                        <!-- Units Repeater -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <h4 class="text-base font-bold text-slate-800">Daftar Satuan & Harga Jual</h4>
                                    <p class="text-xs text-slate-500">Perbarui harga jual atau satuan kemasan tambahan.</p>
                                </div>
                                <button type="button" @click="addUnit('edit')" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 text-brand-600 rounded-lg text-sm font-medium hover:bg-slate-200 transition-colors">
                                    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Satuan Kemasan
                                </button>
                            </div>

                            <div class="space-y-4">
                                <template x-for="(unit, index) in editUnits" :key="index">
                                    <div class="p-4 rounded-xl border relative" :class="index === 0 ? 'bg-amber-50/30 border-amber-200' : 'bg-slate-50 border-slate-200'">
                                        <template x-if="unit.id">
                                            <input type="hidden" :name="`units[${index}][id]`" x-model="unit.id">
                                        </template>
                                        <div class="flex justify-between items-center mb-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold px-2 py-0.5 rounded-full" :class="index === 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-700'" x-text="index === 0 ? 'Satuan Dasar' : 'Satuan Kemasan #' + index"></span>
                                            </div>
                                            <button type="button" x-show="index > 0" @click="removeUnit('edit', index)" class="text-rose-400 hover:text-rose-600 p-1 rounded-md hover:bg-rose-50 transition-colors" title="Hapus Satuan">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Nama Satuan <span class="text-rose-500">*</span></label>
                                                <input type="text" x-model="unit.unit_name" :name="`units[${index}][unit_name]`" required class="w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500" :readonly="index === 0" :class="index === 0 ? 'bg-slate-100 text-slate-500' : ''">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Konversi (Jml Sat. Dasar) <span class="text-rose-500">*</span></label>
                                                <div class="relative">
                                                    <input type="number" x-model="unit.conversion_factor" :name="`units[${index}][conversion_factor]`" required min="1" step="0.01" class="w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500" :readonly="index === 0" :class="index === 0 ? 'bg-slate-100 text-slate-500' : ''">
                                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none" x-show="index > 0">
                                                        <span class="text-slate-400 sm:text-xs" x-text="baseUnitName || 'Pcs'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Harga Eceran <span class="text-rose-500">*</span></label>
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none"><span class="text-slate-500 sm:text-xs">Rp</span></div>
                                                    <input type="number" x-model="unit.price_retail" :name="`units[${index}][price_retail]`" required min="0" class="pl-8 w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Harga Grosir</label>
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none"><span class="text-slate-500 sm:text-xs">Rp</span></div>
                                                    <input type="number" x-model="unit.price_wholesale" :name="`units[${index}][price_wholesale]`" min="0" class="pl-8 w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Min. Beli Grosir</label>
                                                <input type="number" x-model="unit.min_wholesale_qty" :name="`units[${index}][min_wholesale_qty]`" min="1" class="w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:px-6 flex justify-end gap-2 border-t border-slate-200 rounded-b-2xl">
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-brand-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-brand-700 transition-colors">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
    </div>

    {{-- ================================================================== --}}
    {{-- AI INVOICE SCAN MODAL (Full-screen overlay)                        --}}
    {{-- ================================================================== --}}
    <template x-teleport="body">
    <div x-show="scanModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" style="display:none;">

        <div @click.away="closeScanModal()" x-show="scanModalOpen"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[92vh] overflow-hidden flex flex-col">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white">
                        <i data-lucide="scan-eye" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">Scan Faktur Otomatis</h3>
                        <p class="text-xs text-slate-500 font-medium">Upload foto faktur/nota supplier, AI akan membaca & mengisi data otomatis</p>
                    </div>
                </div>
                <button @click="closeScanModal()" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition-all">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            {{-- Modal Body (scrollable) --}}
            <div class="overflow-y-auto flex-1 p-6 space-y-5">

                {{-- STEP 1: UPLOAD --}}
                <div x-show="scanStep === 'upload'">
                    <div class="border-2 border-dashed rounded-2xl p-8 text-center transition-all"
                         :class="isDragging ? 'border-violet-400 bg-violet-50' : 'border-slate-200 bg-slate-50 hover:border-violet-300 hover:bg-violet-50/50'"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="isDragging = false; handleFileDrop($event)">
                        <template x-if="previewUrl">
                            <div class="mb-4">
                                <img :src="previewUrl" class="max-h-56 mx-auto rounded-xl shadow-lg border border-slate-200" alt="Preview Faktur">
                            </div>
                        </template>
                        <template x-if="!previewUrl">
                            <div class="space-y-3">
                                <div class="w-16 h-16 mx-auto rounded-2xl bg-violet-100 text-violet-600 flex items-center justify-center">
                                    <i data-lucide="image-plus" class="w-8 h-8"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-700">Drag & drop foto faktur di sini</p>
                                    <p class="text-xs text-slate-400 mt-1">atau klik tombol di bawah untuk memilih file</p>
                                </div>
                            </div>
                        </template>
                        <div class="flex items-center justify-center gap-3 mt-4">
                            <label class="cursor-pointer bg-white border border-slate-200 hover:border-violet-300 text-slate-700 hover:text-violet-700 font-semibold text-xs px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-xs transition-all">
                                <i data-lucide="upload" class="w-4 h-4"></i>
                                <span>Pilih File</span>
                                <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="handleFileSelect($event)">
                            </label>
                            <label class="cursor-pointer bg-white border border-slate-200 hover:border-violet-300 text-slate-700 hover:text-violet-700 font-semibold text-xs px-4 py-2.5 rounded-xl flex items-center gap-2 shadow-xs transition-all">
                                <i data-lucide="camera" class="w-4 h-4"></i>
                                <span>Buka Kamera</span>
                                <input type="file" accept="image/*" capture="environment" class="hidden" @change="handleFileSelect($event)">
                            </label>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-3">Format: JPG, PNG, WebP • Maks. 10MB</p>
                    </div>
                    <template x-if="selectedFile">
                        <div class="mt-4 flex justify-end">
                            <button @click="startScan()" type="button"
                                class="bg-gradient-to-r from-violet-600 to-indigo-600 hover:from-violet-700 hover:to-indigo-700 text-white font-bold text-sm px-6 py-3 rounded-xl flex items-center gap-2.5 shadow-lg shadow-violet-200 transition-all">
                                <i data-lucide="sparkles" class="w-4 h-4"></i>
                                <span>Mulai Scan dengan AI</span>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- STEP 2: SCANNING --}}
                <div x-show="scanStep === 'scanning'" class="py-12 text-center">
                    <div class="relative w-20 h-20 mx-auto mb-5">
                        <div class="absolute inset-0 rounded-full border-4 border-violet-100"></div>
                        <div class="absolute inset-0 rounded-full border-4 border-violet-500 border-t-transparent animate-spin"></div>
                        <div class="absolute inset-3 rounded-full bg-violet-50 flex items-center justify-center">
                            <i data-lucide="scan-eye" class="w-7 h-7 text-violet-600"></i>
                        </div>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900">AI Sedang Membaca Faktur...</h4>
                    <p class="text-sm text-slate-500 mt-1.5 max-w-md mx-auto">Gemini Vision sedang menganalisis teks, tabel, dan struktur faktur Anda. Proses ini membutuhkan waktu 2-5 detik.</p>
                    <div class="mt-6 flex items-center justify-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-violet-400 animate-bounce" style="animation-delay: 0ms"></span>
                        <span class="w-2 h-2 rounded-full bg-violet-400 animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-2 h-2 rounded-full bg-violet-400 animate-bounce" style="animation-delay: 300ms"></span>
                    </div>
                </div>

                {{-- STEP 3: REVIEW RESULTS --}}
                <div x-show="scanStep === 'review'">
                    <div class="bg-gradient-to-r from-violet-50 to-indigo-50 border border-violet-200/60 rounded-xl p-4 mb-5">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-violet-600 uppercase tracking-wider mb-1">Supplier / Distributor</label>
                                <input type="text" x-model="scanResult.supplier_name" class="w-full bg-white border border-violet-200 rounded-lg px-3 py-2 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-violet-600 uppercase tracking-wider mb-1">Nomor Faktur</label>
                                <input type="text" x-model="scanResult.invoice_number" class="w-full bg-white border border-violet-200 rounded-lg px-3 py-2 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-violet-600 uppercase tracking-wider mb-1">Tanggal Faktur</label>
                                <input type="date" x-model="scanResult.invoice_date" class="w-full bg-white border border-violet-200 rounded-lg px-3 py-2 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <h4 class="font-bold text-slate-900 text-sm">Barang Terdeteksi</h4>
                            <span class="bg-violet-100 text-violet-700 text-xs font-bold px-2 py-0.5 rounded-md" x-text="scanResult.items.length + ' item'"></span>
                        </div>
                        <button @click="addManualRow()" type="button" class="text-xs font-semibold text-violet-600 hover:text-violet-800 flex items-center gap-1.5 hover:bg-violet-50 px-3 py-1.5 rounded-lg transition-all">
                            <i data-lucide="plus-circle" class="w-4 h-4"></i>
                            <span>Tambah Baris Manual</span>
                        </button>
                    </div>

                    <div class="border border-slate-200 rounded-xl overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[11px] font-bold">
                                        <th class="py-3 px-3 text-left w-8">#</th>
                                        <th class="py-3 px-3 text-left">Nama di Faktur</th>
                                        <th class="py-3 px-3 text-left">Produk Toko (Cocokkan)</th>
                                        <th class="py-3 px-3 text-center w-24">Qty</th>
                                        <th class="py-3 px-3 text-left w-36">Satuan</th>
                                        <th class="py-3 px-3 text-left">Catatan</th>
                                        <th class="py-3 px-3 text-center w-12">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="(item, index) in scanResult.items" :key="index">
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="py-3 px-3 text-slate-400 font-bold" x-text="index + 1"></td>
                                            <td class="py-3 px-3">
                                                <span class="text-sm font-medium text-slate-700" x-text="item.product_name_on_invoice"></span>
                                            </td>
                                            <td class="py-3 px-3">
                                                <select x-model="item.matched_product_id"
                                                    class="w-full bg-white border rounded-lg px-2 py-1.5 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-violet-500/30"
                                                    :class="item.matched_product_id ? 'border-emerald-300 text-emerald-800 bg-emerald-50/50' : 'border-amber-300 text-amber-800 bg-amber-50/50'">
                                                    <option value="">-- Pilih Produk --</option>
                                                    @foreach($allProducts as $ap)
                                                        <option value="{{ $ap->id }}">{{ $ap->name }} ({{ $ap->sku }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <input type="number" x-model.number="item.quantity" min="1" class="w-20 text-center bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                                            </td>
                                            <td class="py-3 px-3">
                                                <select x-model="item.unit" class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                                                    <option value="Dus (Karton)">Dus (Karton)</option>
                                                    <option value="Bungkus">Bungkus</option>
                                                    <option value="Botol">Botol</option>
                                                    <option value="Pouch">Pouch</option>
                                                    <option value="Renceng">Renceng</option>
                                                    <option value="Slop">Slop</option>
                                                    <option value="Kg">Kg</option>
                                                    <option value="Kotak">Kotak</option>
                                                    <option value="Unit">Unit</option>
                                                </select>
                                            </td>
                                            <td class="py-3 px-3">
                                                <input type="text" x-model="item.notes" placeholder="Opsional..." class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-sm text-slate-600 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <button @click="removeItem(index)" type="button" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <template x-if="unmatchedCount > 0">
                        <div class="mt-3 bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-start gap-2.5">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                            <div>
                                <p class="text-xs font-bold text-amber-800"><span x-text="unmatchedCount"></span> barang belum dicocokkan dengan produk toko.</p>
                                <p class="text-[11px] text-amber-600 mt-0.5">Pilih produk yang sesuai dari dropdown, atau hapus baris jika tidak diperlukan.</p>
                            </div>
                        </div>
                    </template>

                    <template x-if="scanError">
                        <div class="mt-3 bg-red-50 border border-red-200 rounded-xl p-3 flex items-start gap-2.5">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0 mt-0.5"></i>
                            <p class="text-xs font-bold text-red-800" x-text="scanError"></p>
                        </div>
                    </template>
                </div>

                {{-- STEP: ERROR --}}
                <div x-show="scanStep === 'error'" class="py-12 text-center">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-red-100 text-red-600 flex items-center justify-center mb-4">
                        <i data-lucide="alert-circle" class="w-8 h-8"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900">Gagal Memproses Faktur</h4>
                    <p class="text-sm text-slate-500 mt-1.5 max-w-md mx-auto" x-text="scanError"></p>
                    <button @click="resetScan()" type="button" class="mt-5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm px-5 py-2.5 rounded-xl transition-all">Coba Lagi</button>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0" x-show="scanStep === 'review'">
                <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <i data-lucide="info" class="w-4 h-4 text-violet-600"></i>
                    <span>Periksa data sebelum menyimpan. Semua barang akan ditambahkan sebagai stok masuk.</span>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="resetScan()" type="button" class="px-4 py-2.5 text-slate-500 hover:text-slate-800 font-semibold text-xs rounded-xl transition-all">Scan Ulang</button>
                    <button @click="submitBatch()" type="button"
                        :disabled="!canSubmit"
                        class="font-bold text-xs px-5 py-2.5 rounded-xl flex items-center gap-2 shadow-xs transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        :class="canSubmit ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-200' : 'bg-slate-200 text-slate-400'">
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                        <span>Konfirmasi & Tambah ke Stok</span>
                        <span class="bg-white/20 text-[10px] px-1.5 py-0.5 rounded font-bold" x-text="matchedCount + ' item'" x-show="matchedCount > 0"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    </template>

    {{-- Hidden form for batch submission --}}
    <form id="batchForm" method="POST" action="{{ route('stok.batch-store') }}" class="hidden">
        @csrf
        <input type="hidden" name="supplier_name" :value="scanResult.supplier_name">
        <input type="hidden" name="invoice_number" :value="scanResult.invoice_number">
        <template x-for="(item, index) in matchedItems" :key="index">
            <div>
                <input type="hidden" :name="'items['+index+'][product_id]'" :value="item.matched_product_id">
                <input type="hidden" :name="'items['+index+'][quantity]'" :value="item.quantity">
                <input type="hidden" :name="'items['+index+'][unit]'" :value="item.unit">
                <input type="hidden" :name="'items['+index+'][notes]'" :value="item.notes || ''">
            </div>
        </template>
    </form>

</div>

{{-- Alpine.js App --}}
<script>
    window.pageProducts = {!! json_encode(collect($products->items())->keyBy('id')) !!};

    function stokBarangApp() {
        return {
            // Product form state
            addModalOpen: false,
            editModalOpen: false,
            activeItem: {},
            baseUnitName: '',
            
            addUnits: [{
                unit_name: '',
                conversion_factor: 1,
                price_retail: '',
                price_wholesale: '',
                min_wholesale_qty: 1
            }],
            editUnits: [],

            // Stok Masuk state
            quantity: 12,

            // Scan modal state
            scanModalOpen: false,
            scanStep: 'upload',
            isDragging: false,
            selectedFile: null,
            previewUrl: null,
            scanError: null,
            scanResult: {
                supplier_name: '',
                invoice_number: '',
                invoice_date: '',
                items: []
            },

            // Computed getters
            get unmatchedCount() {
                return this.scanResult.items.filter(i => !i.matched_product_id).length;
            },
            get matchedCount() {
                return this.scanResult.items.filter(i => i.matched_product_id && i.quantity > 0).length;
            },
            get matchedItems() {
                return this.scanResult.items.filter(i => i.matched_product_id && i.quantity > 0);
            },
            get canSubmit() {
                return this.matchedCount > 0;
            },

            // Product Modal Methods
            openAddModal() {
                this.baseUnitName = '';
                this.addUnits = [{
                    unit_name: '',
                    conversion_factor: 1,
                    price_retail: '',
                    price_wholesale: '',
                    min_wholesale_qty: 1
                }];
                this.addModalOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },
            
            openEditModal(id) {
                const product = window.pageProducts[id];
                if (!product) return;
                
                this.activeItem = JSON.parse(JSON.stringify(product));
                this.activeItem.expiry_date = product.expiry_date ? product.expiry_date.split('T')[0] : '';
                this.baseUnitName = product.unit;
                
                if (product.units && product.units.length > 0) {
                    this.editUnits = product.units.map(u => ({
                        id: u.id,
                        unit_name: u.unit_name,
                        conversion_factor: u.conversion_factor,
                        price_retail: u.price_tier ? u.price_tier.price_retail : '',
                        price_wholesale: u.price_tier ? u.price_tier.price_wholesale : '',
                        min_wholesale_qty: u.price_tier ? u.price_tier.min_wholesale_qty : 1
                    }));
                } else {
                    this.editUnits = [{
                        id: '',
                        unit_name: product.unit,
                        conversion_factor: 1,
                        price_retail: '',
                        price_wholesale: '',
                        min_wholesale_qty: 1
                    }];
                }
                
                this.editModalOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },
            
            addUnit(target) {
                const emptyRow = { id: '', unit_name: '', conversion_factor: '', price_retail: '', price_wholesale: '', min_wholesale_qty: 1 };
                if (target === 'add') this.addUnits.push({...emptyRow});
                else if (target === 'edit') this.editUnits.push({...emptyRow});
                this.$nextTick(() => lucide.createIcons());
            },
            
            removeUnit(target, index) {
                if (index > 0) {
                    if (target === 'add') this.addUnits.splice(index, 1);
                    else if (target === 'edit') this.editUnits.splice(index, 1);
                }
            },
            
            updateBaseUnit(target) {
                if (target === 'add' && this.addUnits.length > 0) this.addUnits[0].unit_name = this.baseUnitName;
                else if (target === 'edit' && this.editUnits.length > 0) this.editUnits[0].unit_name = this.baseUnitName;
            },

            // Scanner Modal Methods
            openScanModal() {
                this.scanModalOpen = true;
                this.$nextTick(() => lucide.createIcons());
            },
            closeScanModal() {
                this.scanModalOpen = false;
            },
            resetScan() {
                this.scanStep = 'upload';
                this.selectedFile = null;
                this.previewUrl = null;
                this.scanError = null;
                this.scanResult = { supplier_name: '', invoice_number: '', invoice_date: '', items: [] };
            },

            handleFileSelect(event) {
                const file = event.target.files[0];
                if (file) this.setFile(file);
            },
            handleFileDrop(event) {
                const file = event.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) this.setFile(file);
            },
            setFile(file) {
                if (file.size > 10 * 1024 * 1024) {
                    alert('Ukuran file melebihi 10MB. Silakan kompres gambar terlebih dahulu.');
                    return;
                }
                this.selectedFile = file;
                this.previewUrl = URL.createObjectURL(file);
                this.$nextTick(() => lucide.createIcons());
            },

            async startScan() {
                if (!this.selectedFile) return;
                this.scanStep = 'scanning';
                this.scanError = null;

                const formData = new FormData();
                formData.append('invoice_image', this.selectedFile);
                formData.append('_token', '{{ csrf_token() }}');

                try {
                    const response = await fetch('{{ route("stok.scan-invoice") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });
                    const result = await response.json();

                    if (result.success && result.data) {
                        this.scanResult = {
                            supplier_name: result.data.supplier_name || '',
                            invoice_number: result.data.invoice_number || '',
                            invoice_date: result.data.invoice_date || '',
                            items: (result.data.items || []).map(item => ({
                                product_name_on_invoice: item.product_name_on_invoice || '',
                                matched_product_id: item.matched_product_id ? String(item.matched_product_id) : '',
                                matched_product_name: item.matched_product_name || '',
                                quantity: item.quantity || 1,
                                unit: item.unit || 'Dus (Karton)',
                                notes: item.notes || '',
                            }))
                        };
                        this.scanStep = 'review';
                    } else {
                        this.scanError = result.message || 'Terjadi kesalahan yang tidak diketahui.';
                        this.scanStep = 'error';
                    }
                } catch (error) {
                    console.error('Scan error:', error);
                    this.scanError = 'Gagal menghubungi server. Pastikan koneksi internet Anda stabil dan GEMINI_API_KEY sudah dikonfigurasi di file .env';
                    this.scanStep = 'error';
                }
                this.$nextTick(() => lucide.createIcons());
            },

            addManualRow() {
                this.scanResult.items.push({
                    product_name_on_invoice: '(Input Manual)',
                    matched_product_id: '',
                    matched_product_name: '',
                    quantity: 1,
                    unit: 'Dus (Karton)',
                    notes: '',
                });
            },
            removeItem(index) {
                this.scanResult.items.splice(index, 1);
            },

            submitBatch() {
                if (!this.canSubmit) return;
                if (!confirm(`Simpan ${this.matchedCount} barang dari ${this.scanResult.supplier_name} ke stok?`)) return;
                this.$nextTick(() => {
                    document.getElementById('batchForm').submit();
                });
            },
        };
    }
</script>
@endsection
