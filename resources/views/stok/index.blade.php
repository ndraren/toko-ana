@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="invoiceScanApp()" x-cloak>

    {{-- Success / Error Flash Messages --}}
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

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <span>LOGISTIK TOKO</span>
                <span>•</span>
                <span class="text-brand-600">Pembaruan Realtime</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Catat Stok Masuk</h2>
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

            <!-- Mode Label (Stok Masuk Only) -->
            <div class="bg-emerald-50 border border-emerald-200 p-1 rounded-xl flex items-center gap-1 text-xs font-semibold text-emerald-800">
                <div class="px-4 py-2 rounded-lg flex items-center gap-2 bg-white shadow-xs">
                    <i data-lucide="arrow-down-left" class="w-4 h-4 text-emerald-600"></i>
                    <span>Stok Masuk (Restock)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Input Box Card (Manual - existing) -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs">
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

                {{-- ====== STEP 1: UPLOAD ====== --}}
                <div x-show="scanStep === 'upload'">
                    <div class="border-2 border-dashed rounded-2xl p-8 text-center transition-all"
                         :class="isDragging ? 'border-violet-400 bg-violet-50' : 'border-slate-200 bg-slate-50 hover:border-violet-300 hover:bg-violet-50/50'"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="isDragging = false; handleFileDrop($event)">

                        {{-- Preview image --}}
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

                    {{-- Scan Button --}}
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

                {{-- ====== STEP 2: SCANNING (loading) ====== --}}
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

                {{-- ====== STEP 3: REVIEW RESULTS ====== --}}
                <div x-show="scanStep === 'review'">

                    {{-- Invoice Header Info --}}
                    <div class="bg-gradient-to-r from-violet-50 to-indigo-50 border border-violet-200/60 rounded-xl p-4 mb-5">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-violet-600 uppercase tracking-wider mb-1">Supplier / Distributor</label>
                                <input type="text" x-model="scanResult.supplier_name"
                                    class="w-full bg-white border border-violet-200 rounded-lg px-3 py-2 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-violet-600 uppercase tracking-wider mb-1">Nomor Faktur</label>
                                <input type="text" x-model="scanResult.invoice_number"
                                    class="w-full bg-white border border-violet-200 rounded-lg px-3 py-2 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-violet-600 uppercase tracking-wider mb-1">Tanggal Faktur</label>
                                <input type="date" x-model="scanResult.invoice_date"
                                    class="w-full bg-white border border-violet-200 rounded-lg px-3 py-2 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                            </div>
                        </div>
                    </div>

                    {{-- Detected Items Count --}}
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

                    {{-- Items Review Table --}}
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
                                                    @foreach($allProducts as $p)
                                                        <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->sku }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <input type="number" x-model.number="item.quantity" min="1"
                                                    class="w-20 text-center bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
                                            </td>
                                            <td class="py-3 px-3">
                                                <select x-model="item.unit"
                                                    class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
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
                                                <input type="text" x-model="item.notes" placeholder="Opsional..."
                                                    class="w-full bg-white border border-slate-200 rounded-lg px-2 py-1.5 text-sm text-slate-600 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-violet-500/30">
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

                    {{-- Unmatched Warning --}}
                    <template x-if="unmatchedCount > 0">
                        <div class="mt-3 bg-amber-50 border border-amber-200 rounded-xl p-3 flex items-start gap-2.5">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                            <div>
                                <p class="text-xs font-bold text-amber-800"><span x-text="unmatchedCount"></span> barang belum dicocokkan dengan produk toko.</p>
                                <p class="text-[11px] text-amber-600 mt-0.5">Pilih produk yang sesuai dari dropdown, atau hapus baris jika tidak diperlukan.</p>
                            </div>
                        </div>
                    </template>

                    {{-- Error Message --}}
                    <template x-if="scanError">
                        <div class="mt-3 bg-red-50 border border-red-200 rounded-xl p-3 flex items-start gap-2.5">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-red-600 shrink-0 mt-0.5"></i>
                            <p class="text-xs font-bold text-red-800" x-text="scanError"></p>
                        </div>
                    </template>
                </div>

                {{-- ====== STEP: ERROR ====== --}}
                <div x-show="scanStep === 'error'" class="py-12 text-center">
                    <div class="w-16 h-16 mx-auto rounded-2xl bg-red-100 text-red-600 flex items-center justify-center mb-4">
                        <i data-lucide="alert-circle" class="w-8 h-8"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-900">Gagal Memproses Faktur</h4>
                    <p class="text-sm text-slate-500 mt-1.5 max-w-md mx-auto" x-text="scanError"></p>
                    <button @click="resetScan()" type="button" class="mt-5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm px-5 py-2.5 rounded-xl transition-all">
                        Coba Lagi
                    </button>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between bg-slate-50/50 shrink-0" x-show="scanStep === 'review'">
                <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <i data-lucide="info" class="w-4 h-4 text-violet-600"></i>
                    <span>Periksa data sebelum menyimpan. Semua barang akan ditambahkan sebagai stok masuk.</span>
                </div>
                <div class="flex items-center gap-3">
                    <button @click="resetScan()" type="button" class="px-4 py-2.5 text-slate-500 hover:text-slate-800 font-semibold text-xs rounded-xl transition-all">
                        Scan Ulang
                    </button>
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

{{-- Alpine.js Invoice Scan App --}}
<script>
function invoiceScanApp() {
    return {
        // Manual form
        quantity: 12,

        // Scan modal state
        scanModalOpen: false,
        scanStep: 'upload', // upload | scanning | review | error
        isDragging: false,
        selectedFile: null,
        previewUrl: null,
        scanError: null,

        // Scan result data
        scanResult: {
            supplier_name: '',
            invoice_number: '',
            invoice_date: '',
            items: []
        },

        // Computed-like getters
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

        // Modal controls
        openScanModal() {
            this.scanModalOpen = true;
            this.$nextTick(() => lucide.createIcons());
        },
        closeScanModal() {
            this.scanModalOpen = false;
            // Don't reset if we're in review step, user might want to come back
        },
        resetScan() {
            this.scanStep = 'upload';
            this.selectedFile = null;
            this.previewUrl = null;
            this.scanError = null;
            this.scanResult = { supplier_name: '', invoice_number: '', invoice_date: '', items: [] };
        },

        // File handling
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

        // AI Scan process
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

        // Item management
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

        // Submit batch
        submitBatch() {
            if (!this.canSubmit) return;

            if (!confirm(`Simpan ${this.matchedCount} barang dari ${this.scanResult.supplier_name} ke stok?`)) return;

            // Submit via the hidden form
            this.$nextTick(() => {
                document.getElementById('batchForm').submit();
            });
        },
    };
}
</script>
@endsection
