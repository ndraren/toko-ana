@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    <div>
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
            <span>ANALISIS TOKO</span>
            <span>•</span>
            <span class="text-slate-500">SIKOL REPORTS</span>
        </div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Laporan Persediaan & Pergerakan Stok</h2>
        <p class="text-xs text-slate-500 font-medium mt-0.5">Analisis mutasi barang, audit persediaan, dan estimasi keharusan restock.</p>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
            <span class="text-xs font-semibold text-slate-500">Est. Total Persediaan</span>
            <h3 class="text-2xl font-black text-slate-900 mt-1">Rp 84.600.000</h3>
            <p class="text-xs text-emerald-600 font-medium mt-1">✓ Berdasarkan 12 kategori produk</p>
        </div>
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
            <span class="text-xs font-semibold text-slate-500">Perputaran Stok Shift Pagi</span>
            <h3 class="text-2xl font-black text-slate-900 mt-1">+148 Dus / -312 Pcs</h3>
            <p class="text-xs text-slate-500 font-medium mt-1">Pergerakan tercatat otomatis</p>
        </div>
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs">
            <span class="text-xs font-semibold text-slate-500">Margin Rata-rata</span>
            <h3 class="text-2xl font-black text-slate-900 mt-1">21.5%</h3>
            <p class="text-xs text-brand-600 font-medium mt-1">Keuntungan bersih produk retail</p>
        </div>
    </div>

    <!-- Report Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs space-y-4">
        <h3 class="font-bold text-slate-900 text-base">Ringkasan Mutasi Barang Terakhir</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[11px] font-bold">
                        <th class="py-3">WAKTU</th>
                        <th class="py-3">NAMA BARANG</th>
                        <th class="py-3">KATEGORI / SUMBER</th>
                        <th class="py-3 text-center">PERUBAHAN STOK</th>
                        <th class="py-3 text-right">PETUGAS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($movements as $m)
                    <tr>
                        <td class="py-3 text-slate-500">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 font-bold text-slate-900">{{ $m->product->name ?? 'Produk' }}</td>
                        <td class="py-3 text-slate-600">{{ $m->source_category }}</td>
                        <td class="py-3 text-center font-bold">{{ $m->formatted_change }}</td>
                        <td class="py-3 text-right text-slate-700">{{ $m->user_name }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
