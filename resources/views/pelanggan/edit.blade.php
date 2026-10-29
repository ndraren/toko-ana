@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('pelanggan.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700 flex items-center gap-1 mb-3">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali
        </a>
        <h2 class="text-2xl font-bold text-slate-900">Edit Pelanggan</h2>
        <p class="text-xs text-slate-500 mt-1">Perbarui data pelanggan <strong>{{ $pelanggan->name }}</strong>.</p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <form action="{{ route('pelanggan.update', $pelanggan) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Pelanggan <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $pelanggan->name) }}" required
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">No. HP / WhatsApp</label>
                <input type="text" name="phone" value="{{ old('phone', $pelanggan->phone) }}"
                       class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Tipe Pelanggan <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="retail" {{ old('type', $pelanggan->type) === 'retail' ? 'checked' : '' }} class="peer sr-only">
                        <div class="border-2 border-slate-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 rounded-xl p-4 transition-all">
                            <i data-lucide="user" class="w-5 h-5 text-blue-600 mb-1.5"></i>
                            <p class="font-bold text-slate-900 text-sm">Retail</p>
                            <p class="text-xs text-slate-500 mt-0.5">Pembeli biasa, harga eceran</p>
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="reseller" {{ old('type', $pelanggan->type) === 'reseller' ? 'checked' : '' }} class="peer sr-only">
                        <div class="border-2 border-slate-200 peer-checked:border-purple-500 peer-checked:bg-purple-50 rounded-xl p-4 transition-all">
                            <i data-lucide="store" class="w-5 h-5 text-purple-600 mb-1.5"></i>
                            <p class="font-bold text-slate-900 text-sm">Reseller</p>
                            <p class="text-xs text-slate-500 mt-0.5">Toko kecil, harga grosir otomatis</p>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan</label>
                <textarea name="notes" rows="3"
                          class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 resize-none">{{ old('notes', $pelanggan->notes) }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('pelanggan.index') }}" class="flex-1 py-2.5 text-center text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">Batal</a>
                <button type="submit" class="flex-1 py-2.5 text-sm font-bold text-white bg-brand-dark hover:bg-brand-800 rounded-xl shadow transition-all">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
