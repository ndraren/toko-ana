@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('transaksi.index') }}" class="text-xs text-brand-700 font-bold hover:underline flex items-center gap-1 mb-2">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Riwayat Transaksi</span>
            </a>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Detail Transaksi</h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Informasi lengkap struk penjualan {{ $transaction->invoice_number }}</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl flex items-center gap-1.5 transition-all border border-slate-200">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak Struk</span>
            </button>
        </div>
    </div>

    <!-- Transaction Info Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs space-y-5">
        <!-- Top Info Row -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="font-black text-xl text-brand-700">{{ $transaction->invoice_number }}</h3>
                <p class="text-xs text-slate-500 mt-1 font-medium">
                    {{ $transaction->created_at->isoFormat('dddd, D MMMM YYYY') }} • {{ $transaction->created_at->format('H:i') }} WIB
                </p>
            </div>
            <span class="{{ $transaction->status_badge_class }} font-bold px-3.5 py-1.5 rounded-full text-xs inline-flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-current"></span>
                {{ $transaction->status_label }}
            </span>
        </div>

        <!-- Detail Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="text-xs font-semibold text-slate-400 block">Kasir / Petugas</span>
                <span class="font-bold text-slate-900 mt-0.5 block">{{ $transaction->cashier_name }}</span>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block">Metode Pembayaran</span>
                <span class="font-bold text-slate-900 mt-0.5 inline-flex items-center gap-1.5">
                    <i data-lucide="{{ $transaction->payment_icon }}" class="w-4 h-4 text-brand-600"></i>
                    {{ $transaction->payment_method }}
                </span>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block">Pelanggan</span>
                <span class="font-bold text-slate-900 mt-0.5 block">{{ $transaction->customer_name ?? 'Umum (Walk-in)' }}</span>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 block">Catatan</span>
                <span class="font-medium text-slate-600 mt-0.5 block">{{ $transaction->notes ?? '-' }}</span>
            </div>
        </div>
    </div>

    <!-- Item List Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-xs">
        <h3 class="font-bold text-slate-900 text-base mb-4">Daftar Barang ({{ $transaction->items->count() }} item)</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 uppercase tracking-wider text-[11px] font-bold">
                        <th class="py-3 w-10 text-center font-semibold">#</th>
                        <th class="py-3 font-semibold">NAMA BARANG</th>
                        <th class="py-3 text-center font-semibold">QTY</th>
                        <th class="py-3 text-center font-semibold">SATUAN</th>
                        <th class="py-3 text-right font-semibold">HARGA SATUAN</th>
                        <th class="py-3 text-right font-semibold">SUBTOTAL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($transaction->items as $index => $item)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3 text-center font-medium text-slate-400">{{ $index + 1 }}</td>
                        <td class="py-3">
                            <p class="font-bold text-slate-900 text-sm">{{ $item->product_name }}</p>
                            @if($item->product)
                            <p class="text-[11px] text-slate-400 mt-0.5">SKU: {{ $item->product->sku }}</p>
                            @endif
                        </td>
                        <td class="py-3 text-center font-bold text-slate-900">{{ $item->quantity }}</td>
                        <td class="py-3 text-center font-medium text-slate-500">{{ $item->unit }}</td>
                        <td class="py-3 text-right font-medium text-slate-600">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="py-3 text-right font-bold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total Summary -->
        <div class="mt-4 pt-4 border-t border-slate-200 space-y-2">
            <div class="flex items-center justify-between text-sm">
                <span class="font-semibold text-slate-500">Total Belanja</span>
                <span class="font-extrabold text-xl text-slate-900">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="font-medium text-slate-400">Jumlah Bayar</span>
                <span class="font-bold text-slate-700">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="font-medium text-slate-400">Kembalian</span>
                <span class="font-bold text-emerald-600">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
