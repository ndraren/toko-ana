<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $transaction->invoice_number }} - SIKOL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .receipt-container { box-shadow: none; border: none; max-width: 80mm; margin: 0 auto; padding: 10px; }
            .receipt-container * { font-size: 11px !important; }
            .receipt-container h1 { font-size: 16px !important; }
            .receipt-container .total-row { font-size: 14px !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-6">

    <!-- Action Buttons (No Print) -->
    <div class="no-print fixed top-6 right-6 flex items-center gap-3 z-50">
        <button onclick="window.print()" class="bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm px-5 py-2.5 rounded-xl flex items-center gap-2 shadow-lg transition-all"
                style="background-color: #059669;">
            <i data-lucide="printer" class="w-4 h-4"></i>
            <span>Cetak Struk</span>
        </button>
        <a href="{{ route('kasir.index') }}" class="bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm px-5 py-2.5 rounded-xl flex items-center gap-2 shadow border border-slate-200 transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Kasir</span>
        </a>
    </div>

    <!-- Receipt Card -->
    <div class="receipt-container bg-white rounded-2xl shadow-xl border border-slate-200 w-full max-w-sm p-6 space-y-4">
        <!-- Store Header -->
        <div class="text-center border-b border-dashed border-slate-300 pb-4">
            <h1 class="text-xl font-black text-slate-900 tracking-tight">TOKO BERKAH</h1>
            <p class="text-[11px] text-slate-500 mt-1 font-medium">Jl. Contoh Alamat No. 123, Kota</p>
            <p class="text-[11px] text-slate-500 font-medium">Telp: 021-12345678</p>
        </div>

        <!-- Transaction Info -->
        <div class="border-b border-dashed border-slate-300 pb-3 space-y-1">
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">No. Struk</span>
                <span class="font-bold text-slate-900">{{ $transaction->invoice_number }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Tanggal</span>
                <span class="font-semibold text-slate-700">{{ $transaction->created_at->format('d/m/Y H:i') }} WIB</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Kasir</span>
                <span class="font-semibold text-slate-700">{{ $transaction->cashier_name }}</span>
            </div>
            @if($transaction->customer_name)
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Pelanggan</span>
                <span class="font-semibold text-slate-700">{{ $transaction->customer_name }}</span>
            </div>
            @endif
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Pembayaran</span>
                <span class="font-semibold text-slate-700">{{ $transaction->payment_method }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Mode Jual</span>
                <span class="font-bold {{ $transaction->sale_mode === 'grosir' ? 'text-purple-700' : 'text-slate-700' }}">
                    {{ $transaction->sale_mode === 'grosir' ? 'HARGA GROSIR' : 'Eceran' }}
                </span>
            </div>
        </div>

        <!-- Items -->
        <div class="border-b border-dashed border-slate-300 pb-3 space-y-2">
            <div class="flex justify-between text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                <span>Item</span>
                <span>Subtotal</span>
            </div>
            @foreach($transaction->items as $item)
            <div class="space-y-0.5">
                <p class="text-xs font-bold text-slate-900">{{ $item->product_name }}</p>
                <div class="flex justify-between text-xs text-slate-600">
                    <span>{{ $item->quantity }} {{ $item->unit }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($item->product && $item->unit !== $item->product->unit)
                @php
                    $pu = $item->product->units->firstWhere('unit_name', $item->unit);
                @endphp
                @if($pu)
                <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded inline-block">
                    {{ $pu->unit_name }} (isi {{ $pu->conversion_factor }} {{ $item->product->unit }})
                </span>
                @endif
                @endif
            </div>
            @endforeach
        </div>

        <!-- Totals -->
        <div class="space-y-1.5">
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Subtotal</span>
                <span class="font-semibold text-slate-700">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm font-black text-slate-900 total-row pt-1.5 border-t border-slate-200">
                <span>TOTAL</span>
                <span>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Bayar ({{ $transaction->payment_method }})</span>
                <span class="font-semibold text-slate-700">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
            </div>
            @if($transaction->change_amount > 0)
            <div class="flex justify-between text-xs bg-emerald-50 rounded-lg px-2 py-1.5 -mx-1">
                <span class="font-semibold text-emerald-700">Kembalian</span>
                <span class="font-black text-emerald-800">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="text-center border-t border-dashed border-slate-300 pt-4 space-y-1.5">
            <p class="text-xs font-bold text-slate-700">Terima Kasih!</p>
            <p class="text-[10px] text-slate-400">Barang yang sudah dibeli tidak dapat dikembalikan</p>
            <p class="text-[10px] text-slate-400 font-semibold">SIKOL - Sistem Inventaris Kelontong</p>
        </div>
    </div>

    <script>
        lucide.createIcons();
        // Auto print after page loads
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
