@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div>
        <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
            <span>SISTEM</span>
            <span>•</span>
            <span class="text-slate-500">KONFIGURASI</span>
        </div>
        <h2 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Pengaturan Toko & Kasir</h2>
        <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola informasi toko, shift kerja, dan koneksi barcode scanner.</p>
    </div>

    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs space-y-6">
        <h3 class="font-bold text-slate-900 text-base pb-3 border-b border-slate-100">Informasi Toko</h3>
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Toko</label>
                <input type="text" value="Toko Berkah Kelontong" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Status Operasional</label>
                <select class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold">
                    <option selected>Buka (Operasional Normal)</option>
                    <option>Tutup Shift</option>
                    <option>Maintenance</option>
                </select>
            </div>
        </div>

        <h3 class="font-bold text-slate-900 text-base pb-3 border-b border-slate-100 pt-4">Petugas & Shift Current</h3>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kasir Active</label>
                <input type="text" value="Budi Santoso" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Shift Kerja</label>
                <input type="text" value="Kasir Shift 1 (Pagi)" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold">
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="button" class="bg-brand-dark hover:bg-brand-800 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition-all">
                Simpan Pengaturan
            </button>
        </div>
    </div>
</div>
@endsection
