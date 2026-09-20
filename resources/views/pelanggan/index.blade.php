@extends('layouts.app')

@section('content')
<div x-data="{ showModal: false, deleteId: null, deleteName: '' }">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <span>DATA</span><span>&bull;</span>
                <span class="text-brand-600">Pelanggan</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Data Pelanggan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola pelanggan retail dan reseller toko.</p>
        </div>
        <a href="{{ route('pelanggan.create') }}"
           class="bg-brand-dark hover:bg-brand-800 text-white font-semibold text-sm px-4 py-2.5 rounded-xl flex items-center gap-2 shadow transition-all">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah Pelanggan</span>
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-slate-200 rounded-2xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center">
                <i data-lucide="users" class="w-5 h-5 text-slate-600"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Total Pelanggan</p>
                <p class="text-xl font-black text-slate-900">{{ $customers->total() }}</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                <i data-lucide="user" class="w-5 h-5 text-blue-600"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Retail</p>
                <p class="text-xl font-black text-slate-900">{{ $customers->getCollection()->where('type','retail')->count() }}</p>
            </div>
        </div>
        <div class="bg-white border border-slate-200 rounded-2xl p-4 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                <i data-lucide="store" class="w-5 h-5 text-purple-600"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-medium">Reseller</p>
                <p class="text-xl font-black text-slate-900">{{ $customers->getCollection()->where('type','reseller')->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama</th>
                    <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">No. HP</th>
                    <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe</th>
                    <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan</th>
                    <th class="text-left px-5 py-3.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Terdaftar</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($customers as $customer)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                {{ $customer->type === 'reseller' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                            </div>
                            <span class="font-semibold text-slate-900">{{ $customer->name }}</span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-slate-600">{{ $customer->phone ?? '-' }}</td>
                    <td class="px-5 py-3.5">
                        <span class="text-xs font-bold px-2.5 py-1 rounded-full
                            {{ $customer->type === 'reseller' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $customer->type === 'reseller' ? 'Reseller' : 'Retail' }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-slate-500 text-xs max-w-xs truncate">{{ $customer->notes ?? '-' }}</td>
                    <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $customer->created_at->format('d/m/Y') }}</td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('pelanggan.edit', $customer) }}"
                               class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition-all">
                                Edit
                            </a>
                            <button @click="showModal = true; deleteId = {{ $customer->id }}; deleteName = '{{ $customer->name }}'"
                                    class="px-3 py-1.5 text-xs font-semibold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 rounded-lg transition-all">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-16 text-center text-slate-400">
                        <i data-lucide="users" class="w-10 h-10 mx-auto mb-3 text-slate-300"></i>
                        <p class="font-semibold">Belum ada pelanggan terdaftar</p>
                        <a href="{{ route('pelanggan.create') }}" class="mt-2 inline-block text-sm font-semibold text-brand-600 hover:underline">Tambah pelanggan pertama</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($customers->hasPages())
        <div class="px-5 py-4 border-t border-slate-100">
            {{ $customers->links() }}
        </div>
        @endif
    </div>

    <!-- Delete Modal -->
    <div x-show="showModal" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40" style="display:none">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                    <i data-lucide="trash-2" class="w-5 h-5 text-red-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Hapus Pelanggan?</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Data <strong x-text="deleteName"></strong> akan dihapus permanen.</p>
                </div>
            </div>
            <div class="flex gap-2 mt-4">
                <button @click="showModal = false" class="flex-1 py-2 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl">Batal</button>
                <form :action="'/pelanggan/' + deleteId" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full py-2 text-sm font-bold text-white bg-red-600 hover:bg-red-700 rounded-xl">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
