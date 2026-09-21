@extends('layouts.app')

@section('title', 'Data Barang')

@section('content')
<div x-data="productForm()" class="p-6">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Data Barang</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola data inventaris, stok, dan harga barang.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="bg-white rounded-xl border border-slate-200 p-3 flex items-center gap-3 shadow-xs">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                    <i data-lucide="package" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Total SKU</p>
                    <p class="text-lg font-bold text-slate-800">{{ number_format($totalCount, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-3 flex items-center gap-3 shadow-xs">
                <div class="w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500">Perlu Restock</p>
                    <p class="text-lg font-bold text-slate-800">{{ number_format($menipisCount + $habisCount + $criticalCount, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Controls bar -->
    <div class="bg-white p-4 rounded-xl shadow-xs border border-slate-200 mb-6">
        <form method="GET" action="{{ route('products.index') }}" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                <!-- Search -->
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau SKU..." class="pl-10 w-full rounded-lg border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500 py-2">
                </div>
                
                <!-- Category Dropdown -->
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
    </div>

    <!-- Status Filter Pills -->
    <div class="flex flex-wrap gap-2 mb-6">
        <a href="{{ route('products.index', ['status' => '']) }}" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors border {{ empty($status) ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Semua ({{ number_format($totalCount, 0, ',', '.') }})
        </a>
        <a href="{{ route('products.index', ['status' => 'Aman']) }}" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors border {{ $status == 'Aman' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-emerald-700 border-slate-200 hover:bg-emerald-50' }}">
            Stok Aman ({{ number_format($amanCount, 0, ',', '.') }})
        </a>
        <a href="{{ route('products.index', ['status' => 'Menipis']) }}" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors border {{ $status == 'Menipis' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-amber-700 border-slate-200 hover:bg-amber-50' }}">
            Menipis ({{ number_format($menipisCount, 0, ',', '.') }})
        </a>
        <a href="{{ route('products.index', ['status' => 'Habis']) }}" class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors border {{ $status == 'Habis' ? 'bg-rose-500 text-white border-rose-500' : 'bg-white text-rose-700 border-slate-200 hover:bg-rose-50' }}">
            Habis ({{ number_format($habisCount, 0, ',', '.') }})
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-xs border border-slate-200 overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
                <thead class="bg-slate-50/80 border-b border-slate-200 text-xs uppercase font-medium text-slate-500">
                    <tr>
                        <th class="px-4 py-3 w-10 text-center"><input type="checkbox" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"></th>
                        <th class="px-4 py-3">NAMA BARANG & SKU</th>
                        <th class="px-4 py-3">KATEGORI</th>
                        <th class="px-4 py-3">STOK</th>
                        <th class="px-4 py-3">SATUAN</th>
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
                            <button @click="openEditModal({{ htmlspecialchars(json_encode($p->load('units.priceTier')), ENT_QUOTES, 'UTF-8') }})" class="p-1.5 text-slate-400 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition-colors" title="Edit">
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
                        <td colspan="10" class="px-4 py-8 text-center text-slate-500">
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
            {{ $products->links() }}
        </div>
        @endif
    </div>

    <!-- Bottom Shortcut Bar -->
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-slate-900/90 backdrop-blur-sm text-white px-6 py-3 rounded-full shadow-lg flex items-center gap-6 z-40">
        <button type="button" @click="openAddModal()" class="flex items-center gap-2 hover:text-brand-300 transition-colors">
            <span class="w-6 h-6 rounded bg-white/20 flex items-center justify-center text-xs font-bold font-mono">N</span>
            <span class="text-sm font-medium">Barang Baru</span>
        </button>
        <div class="w-px h-4 bg-slate-700"></div>
        <button type="button" onclick="document.querySelector('input[name=search]').focus()" class="flex items-center gap-2 hover:text-brand-300 transition-colors">
            <span class="w-6 h-6 rounded bg-white/20 flex items-center justify-center text-xs font-bold font-mono">/</span>
            <span class="text-sm font-medium">Cari</span>
        </button>
    </div>

    <!-- ADD MODAL -->
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
                                        
                                        <!-- Header of repeater item -->
                                        <div class="flex justify-between items-center mb-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold px-2 py-0.5 rounded-full" :class="index === 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-700'" x-text="index === 0 ? 'Satuan Dasar' : 'Satuan Kemasan #' + index"></span>
                                            </div>
                                            <button type="button" x-show="index > 0" @click="removeUnit('add', index)" class="text-rose-400 hover:text-rose-600 p-1 rounded-md hover:bg-rose-50 transition-colors" title="Hapus Satuan">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                            <!-- Unit Name -->
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Nama Satuan <span class="text-rose-500">*</span></label>
                                                <input type="text" x-model="unit.unit_name" :name="`units[${index}][unit_name]`" required class="w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500" :readonly="index === 0" :class="index === 0 ? 'bg-slate-100 text-slate-500' : ''">
                                            </div>
                                            
                                            <!-- Conversion Factor -->
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Konversi (Jml Sat. Dasar) <span class="text-rose-500">*</span></label>
                                                <div class="relative">
                                                    <input type="number" x-model="unit.conversion_factor" :name="`units[${index}][conversion_factor]`" required min="1" step="0.01" class="w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500" :readonly="index === 0" :class="index === 0 ? 'bg-slate-100 text-slate-500' : ''">
                                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none" x-show="index > 0">
                                                        <span class="text-slate-400 sm:text-xs" x-text="baseUnitName || 'Pcs'"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Retail Price -->
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Harga Eceran <span class="text-rose-500">*</span></label>
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                        <span class="text-slate-500 sm:text-xs">Rp</span>
                                                    </div>
                                                    <input type="number" x-model="unit.price_retail" :name="`units[${index}][price_retail]`" required min="0" class="pl-8 w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                                </div>
                                            </div>

                                            <!-- Wholesale Price -->
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Harga Grosir</label>
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                        <span class="text-slate-500 sm:text-xs">Rp</span>
                                                    </div>
                                                    <input type="number" x-model="unit.price_wholesale" :name="`units[${index}][price_wholesale]`" min="0" class="pl-8 w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                                </div>
                                            </div>

                                            <!-- Min Wholesale Qty -->
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
                        <button type="button" @click="addModalOpen = false" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-brand-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-brand-700 transition-colors">
                            Simpan Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT MODAL -->
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
                                <label class="block text-sm font-medium text-slate-700 mb-1">Harga Modal (HPP) <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-slate-500 sm:text-sm">Rp</span>
                                    </div>
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
                                        
                                        <!-- Hidden ID for existing units -->
                                        <template x-if="unit.id">
                                            <input type="hidden" :name="`units[${index}][id]`" x-model="unit.id">
                                        </template>

                                        <!-- Header of repeater item -->
                                        <div class="flex justify-between items-center mb-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold px-2 py-0.5 rounded-full" :class="index === 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-200 text-slate-700'" x-text="index === 0 ? 'Satuan Dasar' : 'Satuan Kemasan #' + index"></span>
                                            </div>
                                            <button type="button" x-show="index > 0" @click="removeUnit('edit', index)" class="text-rose-400 hover:text-rose-600 p-1 rounded-md hover:bg-rose-50 transition-colors" title="Hapus Satuan">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                            <!-- Unit Name -->
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Nama Satuan <span class="text-rose-500">*</span></label>
                                                <input type="text" x-model="unit.unit_name" :name="`units[${index}][unit_name]`" required class="w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500" :readonly="index === 0" :class="index === 0 ? 'bg-slate-100 text-slate-500' : ''">
                                            </div>
                                            
                                            <!-- Conversion Factor -->
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Konversi (Jml Sat. Dasar) <span class="text-rose-500">*</span></label>
                                                <div class="relative">
                                                    <input type="number" x-model="unit.conversion_factor" :name="`units[${index}][conversion_factor]`" required min="1" step="0.01" class="w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500" :readonly="index === 0" :class="index === 0 ? 'bg-slate-100 text-slate-500' : ''">
                                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none" x-show="index > 0">
                                                        <span class="text-slate-400 sm:text-xs" x-text="baseUnitName || 'Pcs'"></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Retail Price -->
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Harga Eceran <span class="text-rose-500">*</span></label>
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                        <span class="text-slate-500 sm:text-xs">Rp</span>
                                                    </div>
                                                    <input type="number" x-model="unit.price_retail" :name="`units[${index}][price_retail]`" required min="0" class="pl-8 w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                                </div>
                                            </div>

                                            <!-- Wholesale Price -->
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Harga Grosir</label>
                                                <div class="relative">
                                                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                        <span class="text-slate-500 sm:text-xs">Rp</span>
                                                    </div>
                                                    <input type="number" x-model="unit.price_wholesale" :name="`units[${index}][price_wholesale]`" min="0" class="pl-8 w-full rounded-md border-slate-200 text-sm focus:border-brand-500 focus:ring-brand-500">
                                                </div>
                                            </div>

                                            <!-- Min Wholesale Qty -->
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
                        <button type="button" @click="editModalOpen = false" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-brand-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-brand-700 transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function productForm() {
        return {
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
            },
            
            openEditModal(product) {
                this.activeItem = product;
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
            },
            
            addUnit(target) {
                const emptyRow = {
                    id: '',
                    unit_name: '',
                    conversion_factor: '',
                    price_retail: '',
                    price_wholesale: '',
                    min_wholesale_qty: 1
                };
                
                if (target === 'add') {
                    this.addUnits.push({...emptyRow});
                } else if (target === 'edit') {
                    this.editUnits.push({...emptyRow});
                }
            },
            
            removeUnit(target, index) {
                if (index > 0) {
                    if (target === 'add') {
                        this.addUnits.splice(index, 1);
                    } else if (target === 'edit') {
                        this.editUnits.splice(index, 1);
                    }
                }
            },
            
            updateBaseUnit(target) {
                if (target === 'add' && this.addUnits.length > 0) {
                    this.addUnits[0].unit_name = this.baseUnitName;
                } else if (target === 'edit' && this.editUnits.length > 0) {
                    this.editUnits[0].unit_name = this.baseUnitName;
                }
            }
        }
    }
</script>
@endpush
