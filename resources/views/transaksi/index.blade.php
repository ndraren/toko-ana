@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <span>KEUANGAN & OPERASIONAL</span>
                <span>•</span>
                <span class="text-brand-700 font-semibold">SIKOL SYSTEM</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Riwayat Transaksi & Arus Kas</h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">
                Pencatatan terpadu penjualan kasir (pemasukan), pembelian suplier (pengeluaran), serta neraca balance operasional.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Period Toggle Buttons -->
            <div class="bg-slate-100 p-1 rounded-xl flex items-center overflow-x-auto whitespace-nowrap gap-1 text-xs font-semibold text-slate-600">
                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['period' => 'today'])) }}" 
                   class="px-3 py-1.5 rounded-lg {{ $period == 'today' ? 'bg-white text-brand-800 shadow-xs border border-slate-200' : 'hover:text-slate-900' }}">
                   Hari Ini
                </a>
                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['period' => 'week'])) }}" 
                   class="px-3 py-1.5 rounded-lg {{ $period == 'week' ? 'bg-white text-brand-800 shadow-xs border border-slate-200' : 'hover:text-slate-900' }}">
                   Pekan Ini
                </a>
                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['period' => 'month'])) }}" 
                   class="px-3 py-1.5 rounded-lg {{ $period == 'month' ? 'bg-white text-brand-800 shadow-xs border border-slate-200' : 'hover:text-slate-900' }}">
                   Bulan Ini
                </a>
                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['period' => 'all'])) }}" 
                   class="px-3 py-1.5 rounded-lg {{ $period == 'all' ? 'bg-white text-brand-800 shadow-xs border border-slate-200' : 'hover:text-slate-900' }}">
                   Semua
                </a>
            </div>

            <button onclick="window.print()" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl flex items-center gap-1.5 transition-all border border-slate-200 shadow-xs">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Cetak</span>
            </button>
        </div>
    </div>

    <!-- 4 Summary Balance & Cashflow Cards (Segmen 4: Komparasi Balance & Transaksi Suplier) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Pemasukan (Omzet Kasir) -->
        <div class="bg-white border border-emerald-200/80 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500">Pemasukan (Kasir)</span>
                    <p class="text-[10px] text-slate-400 capitalize mt-0.5">Periode {{ $period == 'today' ? 'Hari Ini' : ($period == 'week' ? 'Pekan Ini' : ($period == 'month' ? 'Bulan Ini' : 'Semua')) }}</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-200/60 flex items-center justify-center text-emerald-600">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($periodIncome, 0, ',', '.') }}</span>
                <div class="mt-1 flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                    <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                    <span>{{ $periodIncomeCount }} transaksi penjualan</span>
                </div>
            </div>
        </div>

        <!-- 2. Pengeluaran (Beli Suplier) -->
        <div class="bg-white border border-rose-200/80 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-500">Pengeluaran (Suplier)</span>
                    <p class="text-[10px] text-slate-400 capitalize mt-0.5">Periode {{ $period == 'today' ? 'Hari Ini' : ($period == 'week' ? 'Pekan Ini' : ($period == 'month' ? 'Bulan Ini' : 'Semua')) }}</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-rose-50 border border-rose-200/60 flex items-center justify-center text-rose-600">
                    <i data-lucide="truck" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($periodExpense, 0, ',', '.') }}</span>
                <div class="mt-1 flex items-center gap-1 text-[11px] font-semibold text-rose-600">
                    <i data-lucide="package-plus" class="w-3.5 h-3.5"></i>
                    <span>{{ $periodExpenseCount }} mutasi stok suplier</span>
                </div>
            </div>
        </div>

        <!-- 3. Net Balance Operasional (HIGHLIGHTED) -->
        <div class="rounded-2xl p-5 shadow-md flex flex-col justify-between text-white relative overflow-hidden {{ $isSurplus ? 'bg-gradient-to-br from-emerald-600 to-teal-800' : 'bg-gradient-to-br from-rose-600 to-red-800' }}">
            <div class="flex items-start justify-between relative z-10">
                <span class="text-xs font-semibold text-white/90">Net Operasional (Balance)</span>
                <div class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center">
                    <i data-lucide="{{ $isSurplus ? 'scale' : 'alert-circle' }}" class="w-4 h-4 text-white"></i>
                </div>
            </div>
            <div class="mt-3 relative z-10">
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black tracking-tight">
                        {{ $isSurplus ? '+ ' : '- ' }}Rp {{ number_format(abs($netBalance), 0, ',', '.') }}
                    </span>
                </div>
                <div class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-white/20 backdrop-blur-sm text-[11px] font-bold">
                    <i data-lucide="{{ $isSurplus ? 'check-circle' : 'alert-triangle' }}" class="w-3 h-3"></i>
                    <span>{{ $isSurplus ? 'Surplus Operasional' : 'Defisit Belanja Suplier' }}</span>
                </div>
            </div>
        </div>

        <!-- 4. Ringkasan Status & Margin Kas -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="text-xs font-semibold text-slate-500">Kondisi Kas Toko</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black {{ $isSurplus ? 'text-emerald-700' : 'text-rose-600' }}">
                        {{ $marginPercent }}%
                    </span>
                    <span class="text-xs font-medium text-slate-500">kas bersih</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1 font-medium">
                    @if($periodIncome > $periodExpense)
                        Pemasukan kasir melampaui belanja suplier.
                    @elseif($periodExpense > 0)
                        Pengeluaran suplier lebih tinggi dari omzet saat ini.
                    @else
                        Belum ada aktivitas transaksi pada periode ini.
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi Suplier (Komparasi Operasional Balance) -->
    @if($supplierBreakdown->count() > 0)
    <div class="bg-gradient-to-r from-slate-50 to-brand-50/30 border border-slate-200/80 rounded-2xl p-5 shadow-xs">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="p-1.5 rounded-lg bg-brand-100 text-brand-800">
                    <i data-lucide="truck" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Riwayat Belanja ke Suplier (Periode Ini)</h3>
                    <p class="text-[11px] text-slate-500">Perbandingan total pembelian per suplier terhadap omzet toko</p>
                </div>
            </div>
            <span class="text-xs font-semibold text-slate-500">{{ $supplierBreakdown->count() }} Suplier Aktif</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($supplierBreakdown->take(4) as $sup)
            <div class="bg-white border border-slate-200/90 rounded-xl p-3.5 shadow-2xs flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between">
                        <h4 class="font-bold text-slate-900 text-xs truncate max-w-[150px]">{{ $sup->supplier_name }}</h4>
                        <span class="text-[10px] font-semibold text-slate-400">{{ $sup->items_count }} item</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Total volume: {{ $sup->total_qty }} unit</p>
                </div>
                <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-xs font-black text-rose-600">Rp {{ number_format($sup->total_amount, 0, ',', '.') }}</span>
                    @php
                        $share = $periodExpense > 0 ? round(($sup->total_amount / $periodExpense) * 100) : 0;
                    @endphp
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">{{ $share }}% belanja</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Main Navigation Tabs: Arus Kas & Laporan (Segmen 4 & 5) -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs space-y-5">
        <!-- Tabs Bar -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
            <div class="flex items-center gap-2 overflow-x-auto">
                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['tab' => 'semua'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab == 'semua' ? 'bg-brand-700 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                    <span>Semua Arus Kas</span>
                </a>

                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['tab' => 'pemasukan'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab == 'pemasukan' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <i data-lucide="arrow-down-left" class="w-3.5 h-3.5"></i>
                    <span>Pemasukan (Penjualan)</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab == 'pemasukan' ? 'bg-emerald-700 text-white' : 'bg-slate-200 text-slate-700' }}">
                        {{ $periodIncomeCount }}
                    </span>
                </a>

                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['tab' => 'pengeluaran'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab == 'pengeluaran' ? 'bg-rose-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                    <span>Pengeluaran (Suplier)</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab == 'pengeluaran' ? 'bg-rose-700 text-white' : 'bg-slate-200 text-slate-700' }}">
                        {{ $periodExpenseCount }}
                    </span>
                </a>

                <!-- Segmen 5: Laporan Pengeluaran & Mutasi (Pengganti Menu Laporan) -->
                <a href="{{ route('transaksi.index', array_merge(request()->query(), ['tab' => 'laporan'])) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 {{ $tab == 'laporan' ? 'bg-brand-dark text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <i data-lucide="line-chart" class="w-3.5 h-3.5"></i>
                    <span>Laporan Pengeluaran & Mutasi</span>
                </a>
            </div>

            <!-- Search Form -->
            <form action="{{ route('transaksi.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <input type="hidden" name="period" value="{{ $period }}">
                
                <div class="relative w-full md:w-64">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Cari struk, suplier, pelanggan..." 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-1.5 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                </div>

                <button type="submit" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all">
                    Cari
                </button>
            </form>
        </div>

        <!-- TAB KONTEN 1, 2, 3: TABEL ARUS KAS GABUNGAN -->
        @if($tab !== 'laporan')
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[11px] font-bold">
                        <th class="py-3 font-semibold">TANGGAL & NO. REF</th>
                        <th class="py-3 font-semibold">JENIS ARUS KAS</th>
                        <th class="py-3 font-semibold">PIHAK TERKAIT</th>
                        <th class="py-3 font-semibold">RINCIAN / CATATAN</th>
                        <th class="py-3 font-semibold">METODE</th>
                        <th class="py-3 text-right font-semibold">NOMINAL</th>
                        <th class="py-3 font-semibold">PETUGAS</th>
                        <th class="py-3 text-right font-semibold">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($paginatedRecords as $r)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <!-- 1. Tanggal & Ref -->
                        <td class="py-3.5 pr-2">
                            <span class="font-bold text-slate-900 font-mono text-xs">{{ $r->ref_number }}</span>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($r->date)->format('d M Y, H:i') }} WIB
                            </p>
                        </td>

                        <!-- 2. Jenis Badge -->
                        <td class="py-3.5 px-2">
                            @if($r->type == 'income')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200/80 font-bold text-[11px]">
                                    <i data-lucide="arrow-down-left" class="w-3.5 h-3.5 text-emerald-600"></i>
                                    Pemasukan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200/80 font-bold text-[11px]">
                                    <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-rose-600"></i>
                                    Pengeluaran
                                </span>
                            @endif
                        </td>

                        <!-- 3. Pihak Terkait (Pelanggan / Suplier) -->
                        <td class="py-3.5 px-2">
                            <p class="font-bold text-slate-900 text-xs">{{ $r->party_name }}</p>
                            <span class="text-[10px] px-1.5 py-0.2 rounded font-medium {{ $r->party_type == 'Suplier' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $r->party_type }}
                            </span>
                        </td>

                        <!-- 4. Rincian / Barang -->
                        <td class="py-3.5 px-2 max-w-[220px]">
                            <p class="text-slate-700 font-medium truncate text-xs">{{ $r->description }}</p>
                        </td>

                        <!-- 5. Metode -->
                        <td class="py-3.5 px-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-medium">
                                {{ $r->payment_method }}
                            </span>
                        </td>

                        <!-- 6. Nominal -->
                        <td class="py-3.5 px-2 text-right">
                            <span class="font-black text-sm {{ $r->type == 'income' ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $r->type == 'income' ? '+ ' : '- ' }}Rp {{ number_format($r->amount, 0, ',', '.') }}
                            </span>
                        </td>

                        <!-- 7. Petugas / Status -->
                        <td class="py-3.5 px-2">
                            <span class="text-slate-600 text-xs font-medium">{{ $r->user_name }}</span>
                            <span class="block text-[10px] text-slate-400">{{ $r->status }}</span>
                        </td>

                        <!-- 8. Aksi -->
                        <td class="py-3.5 pl-2 text-right">
                            <a href="{{ $r->detail_url }}" 
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-brand-700 hover:bg-brand-50 font-bold text-xs transition-colors">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                <span>Detail</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400">
                                    <i data-lucide="inbox" class="w-6 h-6"></i>
                                </div>
                                <p class="text-slate-500 font-bold text-xs">Belum ada riwayat transaksi pada filter ini.</p>
                                <p class="text-slate-400 text-[11px]">Coba ubah periode atau kata kunci pencarian.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
            <span>Menampilkan {{ $paginatedRecords->firstItem() ?? 0 }}-{{ $paginatedRecords->lastItem() ?? 0 }} dari {{ $paginatedRecords->total() }} entri</span>
            <div>
                {{ $paginatedRecords->links() }}
            </div>
        </div>

        @else
        <!-- TAB KONTEN 4: LAPORAN PENGELUARAN & MUTASI (SEGMEN 5: REKAP HARIAN, MINGGUAN, BULANAN) -->
        <div class="space-y-6">
            <!-- 3 Periodic Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Harian -->
                <div class="bg-gradient-to-br from-rose-50 to-orange-50 border border-rose-200/80 rounded-2xl p-5 shadow-2xs">
                    <div class="flex items-start justify-between">
                        <span class="text-xs font-bold text-rose-700 uppercase tracking-wider">PENGELUARAN HARI INI</span>
                        <div class="w-7 h-7 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-rose-900 mt-2">Rp {{ number_format($todayExpense, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1 font-medium">Beli stok masuk suplier hari ini</p>
                </div>

                <!-- Mingguan -->
                <div class="bg-gradient-to-br from-purple-50 to-indigo-50 border border-purple-200/80 rounded-2xl p-5 shadow-2xs">
                    <div class="flex items-start justify-between">
                        <span class="text-xs font-bold text-purple-700 uppercase tracking-wider">PENGELUARAN PEKAN INI</span>
                        <div class="w-7 h-7 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center">
                            <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-purple-900 mt-2">Rp {{ number_format($weekExpense, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1 font-medium">Total belanja suplier pekan berjalan</p>
                </div>

                <!-- Bulanan -->
                <div class="bg-gradient-to-br from-blue-50 to-sky-50 border border-blue-200/80 rounded-2xl p-5 shadow-2xs">
                    <div class="flex items-start justify-between">
                        <span class="text-xs font-bold text-blue-700 uppercase tracking-wider">PENGELUARAN BULAN INI</span>
                        <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                            <i data-lucide="pie-chart" class="w-4 h-4"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-blue-900 mt-2">Rp {{ number_format($monthExpense, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-slate-500 mt-1 font-medium">Akumulasi pengeluaran suplier bulan ini</p>
                </div>
            </div>

            <!-- Tabel Mutasi Stok Terakhir (Isi Eks Laporan) -->
            <div class="border border-slate-200/80 rounded-xl overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-200/80 flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-slate-800 text-xs">Ringkasan Mutasi Barang Terakhir</h4>
                        <p class="text-[11px] text-slate-400">Log mutasi stok masuk dan keluar dari gudang / toko</p>
                    </div>
                    <a href="{{ route('stok.index') }}" class="text-xs font-bold text-brand-700 hover:underline">
                        Lihat Semua Stok
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[11px] font-bold bg-white">
                                <th class="py-3 px-4 font-semibold">WAKTU</th>
                                <th class="py-3 px-4 font-semibold">NAMA BARANG</th>
                                <th class="py-3 px-4 font-semibold">KATEGORI / SUMBER</th>
                                <th class="py-3 px-4 text-center font-semibold">PERUBAHAN STOK</th>
                                <th class="py-3 px-4 text-right font-semibold">PETUGAS</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($recentMutations as $m)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3 px-4 font-bold text-slate-900">{{ $m->product->name ?? 'Produk' }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $m->source_category }}</td>
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold {{ $m->type == 'in' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $m->formatted_change }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right text-slate-700 font-medium">{{ $m->user_name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
