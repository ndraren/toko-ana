@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ addModalOpen: false, editModalOpen: false, activeItem: {} }">
    <!-- Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <span>KATALOG UTAMA</span>
                <span>•</span>
                <span class="text-slate-500">SIKOL SYSTEM</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Data Barang</h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola ketersediaan produk, pergerakan stok, dan standardisasi SKU toko.</p>
        </div>

        <!-- Stat Badges Top Right -->
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="flex-1 md:flex-none bg-white border border-slate-200/80 rounded-xl px-4 py-2 text-right shadow-xs">
                <span class="text-[11px] font-semibold text-slate-400 block">Total SKU</span>
                <span class="text-sm font-black text-slate-900 flex items-center gap-1.5 justify-end">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    {{ $totalCount }} Item
                </span>
            </div>
            <div class="flex-1 md:flex-none bg-white border border-slate-200/80 rounded-xl px-4 py-2 text-right shadow-xs">
                <span class="text-[11px] font-semibold text-slate-400 block">Perlu Restock</span>
                <span class="text-sm font-black text-red-600 flex items-center gap-1.5 justify-end">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    {{ $criticalCount }} SKU
                </span>
            </div>
        </div>
    </div>

    <!-- Controls Bar & Filter Pills Container -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Top Control Bar (Search, Category Filter, Actions) -->
        <form action="{{ route('products.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-4">
            <input type="hidden" name="status" value="{{ $status }}">

            <!-- Search & Category -->
            <div class="flex items-center gap-3 flex-1 min-w-[300px]">
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ $search }}"
                           placeholder="Cari nama barang atau scan barcode..." 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-10 pr-9 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                    <i data-lucide="scan-barcode" class="w-4 h-4 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2"></i>
                </div>

                <select name="category" onchange="this.form.submit()" 
                        class="bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-sm font-semibold text-slate-700 focus:outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <button type="button" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl flex items-center gap-1.5 transition-all">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Ekspor</span>
                </button>
                <button type="button" @click="addModalOpen = true" 
                        class="px-4 py-2 bg-brand-dark hover:bg-brand-800 text-white text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-xs transition-all">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span>Tambah Barang</span>
                </button>
            </div>
        </form>

        <!-- Status Filter Pills -->
        <div class="flex items-center overflow-x-auto whitespace-nowrap gap-2 pt-2 border-t border-slate-100 text-xs font-semibold pb-1">
            <a href="{{ route('products.index', ['status' => 'all', 'search' => $search, 'category' => $category]) }}" 
               class="px-4 py-1.5 rounded-full transition-all {{ $status == 'all' ? 'bg-brand-100/80 text-brand-800 font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua <span class="ml-1 text-[11px] font-extrabold opacity-75">{{ $totalCount }}</span>
            </a>
            <a href="{{ route('products.index', ['status' => 'aman', 'search' => $search, 'category' => $category]) }}" 
               class="px-4 py-1.5 rounded-full transition-all {{ $status == 'aman' ? 'bg-emerald-100 text-emerald-800 font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Stok Aman <span class="ml-1 text-[11px] font-extrabold opacity-75">{{ $amanCount }}</span>
            </a>
            <a href="{{ route('products.index', ['status' => 'menipis', 'search' => $search, 'category' => $category]) }}" 
               class="px-4 py-1.5 rounded-full transition-all {{ $status == 'menipis' ? 'bg-amber-100 text-amber-800 font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Menipis <span class="ml-1 px-1.5 py-0.2 rounded-full bg-amber-200 text-amber-900 text-[10px] font-extrabold">{{ $menipisCount }}</span>
            </a>
            <a href="{{ route('products.index', ['status' => 'habis', 'search' => $search, 'category' => $category]) }}" 
               class="px-4 py-1.5 rounded-full transition-all {{ $status == 'habis' ? 'bg-red-100 text-red-800 font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Habis <span class="ml-1 px-1.5 py-0.2 rounded-full bg-red-200 text-red-900 text-[10px] font-extrabold">{{ $habisCount }}</span>
            </a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[11px] font-bold">
                        <th class="py-3 w-10 text-center"><input type="checkbox" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500"></th>
                        <th class="py-3 font-semibold min-w-[200px]">NAMA BARANG & SKU</th>
                        <th class="py-3 font-semibold">KATEGORI</th>
                        <th class="py-3 text-center font-semibold">STOK</th>
                        <th class="py-3 text-center font-semibold">SATUAN</th>
                        <th class="py-3 text-right font-semibold">HARGA MODAL</th>
                        <th class="py-3 text-right font-semibold">HARGA JUAL</th>
                        <th class="py-3 text-center font-semibold">STATUS</th>
                        <th class="py-3 text-right font-semibold">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($products as $p)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 text-center"><input type="checkbox" class="rounded border-slate-300 text-brand-600"></td>
                        <td class="py-3.5 min-w-[200px]">
                            <p class="font-bold text-slate-900 text-sm">{{ $p->name }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">SKU: {{ $p->sku }} • {{ $p->barcode }}</p>
                        </td>
                        <td class="py-3.5">
                            <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-600 whitespace-nowrap">
                                {{ $p->category }}
                            </span>
                        </td>
                        <td class="py-3.5 text-center font-bold text-slate-900 text-sm">
                            {{ $p->stock }}
                        </td>
                        <td class="py-3.5 text-center font-medium text-slate-500">
                            {{ $p->unit }}
                        </td>
                        <td class="py-3.5 text-right font-medium text-slate-600 whitespace-nowrap">
                            Rp {{ number_format($p->cost_price, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 text-right font-extrabold text-slate-900 whitespace-nowrap">
                            Rp {{ number_format($p->selling_price, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 text-center">
                            <span class="{{ $p->status_badge_class }} whitespace-nowrap">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                {{ $p->status }}
                            </span>
                        </td>
                        <td class="py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button type="button" @click="activeItem = {{ json_encode($p) }}; editModalOpen = true" 
                                        class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition-all">
                                    <i data-lucide="pencil" class="w-4 h-4"></i>
                                </button>
                                <form action="{{ route('products.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus barang ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 rounded-lg hover:bg-slate-100 transition-all">
                                        <i data-lucide="more-vertical" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400 font-medium text-sm">
                            Tidak ada barang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer / Pagination -->
        <div class="pt-4 border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500 font-medium">
            <span>Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} barang</span>
            <div class="w-full md:w-auto overflow-x-auto">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <!-- Bottom Shortcut Bar -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-semibold text-slate-500">
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-1.5">
                <span class="bg-slate-200 text-slate-800 font-bold px-1.5 py-0.5 rounded text-[10px]">F2</span>
                <span>Tambah Item Baru</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="bg-slate-200 text-slate-800 font-bold px-1.5 py-0.5 rounded text-[10px]">/</span>
                <span>Fokus Pencarian</span>
            </div>
        </div>
        <div class="flex items-center gap-1.5 text-emerald-600 font-bold text-center">
            <i data-lucide="check-circle-2" class="w-4 h-4"></i>
            <span>Katalog tersinkronisasi otomatis dengan mesin kasir</span>
        </div>
    </div>

    <!-- Modal Tambah Barang -->
    <div x-show="addModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl space-y-4" @click.away="addModalOpen = false">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-slate-900 text-lg">Tambah Barang Baru</h3>
                <button @click="addModalOpen = false" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form action="{{ route('products.store') }}" method="POST" class="space-y-4">
                @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">SKU</label>
                        <input type="text" name="sku" required placeholder="BRG-099" class="w-full bg-slate-50 border rounded-xl px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Barcode</label>
                        <input type="text" name="barcode" placeholder="89912345678" class="w-full bg-slate-50 border rounded-xl px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Barang</label>
                    <input type="text" name="name" required placeholder="Contoh: Indomie Goreng" class="w-full bg-slate-50 border rounded-xl px-3 py-2 text-sm">
                </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
                        <input type="text" name="category" required placeholder="Makanan / Minuman" class="w-full bg-slate-50 border rounded-xl px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Satuan</label>
                        <input type="text" name="unit" required placeholder="Bungkus / Pouch / Botol" class="w-full bg-slate-50 border rounded-xl px-3 py-2 text-sm">
                    </div>
                </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Stok Awal</label>
                        <input type="number" name="stock" value="10" required class="w-full bg-slate-50 border rounded-xl px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Batas Min. Stok</label>
                        <input type="number" name="min_stock" value="5" required class="w-full bg-slate-50 border rounded-xl px-3 py-2 text-sm">
                    </div>
                </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga Modal (Rp)</label>
                        <input type="number" name="cost_price" required placeholder="2500" class="w-full bg-slate-50 border rounded-xl px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga Jual (Rp)</label>
                        <input type="number" name="selling_price" required placeholder="3500" class="w-full bg-slate-50 border rounded-xl px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="pt-3 flex justify-end gap-2">
                    <button type="button" @click="addModalOpen = false" class="px-4 py-2 text-slate-600 font-semibold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-brand-dark hover:bg-brand-800 text-white font-bold text-xs rounded-xl">Simpan Barang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
