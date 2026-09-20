<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIKOL - Inventaris Kelontong</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            dark: '#0d6e4a',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans" x-data="{ sidebarOpen: false, quickModalOpen: false, barcodeScannerOpen: false }">

    <div class="flex min-h-screen">
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden" @click="sidebarOpen = false" style="display: none;"></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 transition-transform duration-300 lg:static lg:translate-x-0 h-screen overflow-y-auto">
            <div class="p-5">
                <!-- Logo & Brand Header -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-brand-500/10 border border-brand-500/20 flex items-center justify-center text-brand-700 font-bold text-xl">
                            <i data-lucide="store" class="w-5 h-5 text-brand-700"></i>
                        </div>
                        <div>
                            <h1 class="font-extrabold text-lg text-slate-900 leading-tight tracking-tight">SIKOL</h1>
                            <p class="text-xs text-slate-500 font-medium">Inventaris Kelontong</p>
                        </div>
                    </div>
                    <!-- Close Sidebar Button (Mobile) -->
                    <button @click="sidebarOpen = false" class="lg:hidden p-2 text-slate-400 hover:bg-slate-100 rounded-lg">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Navigation Section -->
                <div class="mb-3">
                    <span class="text-[11px] font-bold text-slate-400 tracking-wider uppercase px-3">MENU UTAMA</span>
                </div>

                <nav class="space-y-1.5">
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-brand-100/70 text-brand-800' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="layout-grid" class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-brand-700' : 'text-slate-400' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('kasir.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('kasir.*') ? 'bg-brand-100/70 text-brand-800' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="monitor" class="w-5 h-5 {{ request()->routeIs('kasir.*') ? 'text-brand-700' : 'text-slate-400' }}"></i>
                        <span>Kasir</span>
                    </a>

                    <a href="{{ route('products.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('products.*') ? 'bg-brand-100/70 text-brand-800' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="package" class="w-5 h-5 {{ request()->routeIs('products.*') ? 'text-brand-700' : 'text-slate-400' }}"></i>
                        <span>Data Barang</span>
                    </a>

                    <a href="{{ route('stok.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('stok.*') ? 'bg-brand-100/70 text-brand-800' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="package-plus" class="w-5 h-5 {{ request()->routeIs('stok.*') ? 'text-brand-700' : 'text-slate-400' }}"></i>
                        <span>Stok Masuk</span>
                    </a>

                    <a href="{{ route('transaksi.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('transaksi.*') ? 'bg-brand-100/70 text-brand-800' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="receipt" class="w-5 h-5 {{ request()->routeIs('transaksi.*') ? 'text-brand-700' : 'text-slate-400' }}"></i>
                        <span>Riwayat Transaksi</span>
                    </a>

                    <a href="{{ route('laporan.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('laporan.*') ? 'bg-brand-100/70 text-brand-800' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="line-chart" class="w-5 h-5 {{ request()->routeIs('laporan.*') ? 'text-brand-700' : 'text-slate-400' }}"></i>
                        <span>Laporan</span>
                    </a>

                    <a href="{{ route('pengaturan.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('pengaturan.*') ? 'bg-brand-100/70 text-brand-800' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        <i data-lucide="sliders" class="w-5 h-5 {{ request()->routeIs('pengaturan.*') ? 'text-brand-700' : 'text-slate-400' }}"></i>
                        <span>Pengaturan</span>
                    </a>
                </nav>
            </div>

            <!-- Sidebar Bottom Profile Section -->
            <div class="p-4 border-t border-slate-100 space-y-3">
                <!-- Toko Status Pill -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-2.5 flex items-center justify-between text-xs font-semibold">
                    <div class="flex items-center gap-2 text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Toko Berkah</span>
                    </div>
                    <span class="text-brand-700 font-bold bg-brand-50 px-2 py-0.5 rounded-md border border-brand-200">Buka</span>
                </div>

                <!-- User Profile Card -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=120" 
                             alt="Budi Santoso" class="w-9 h-9 rounded-full object-cover border border-slate-200">
                        <div class="overflow-hidden">
                            <p class="text-xs font-bold text-slate-900 truncate">Budi Santoso</p>
                            <p class="text-[11px] text-slate-400 truncate">Kasir Shift 1</p>
                        </div>
                    </div>
                    <button class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Navigation Header -->
            <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-3.5 flex items-center justify-between sticky top-0 z-30 shadow-xs">
                
                <div class="flex items-center gap-4">
                    <!-- Mobile Menu Button -->
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 text-slate-500 hover:bg-slate-100 rounded-lg">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>

                    <!-- Search Bar -->
                    <div class="relative w-full max-w-md hidden md:block">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="text" 
                               placeholder="Cari barang, SKU, atau barcode..." 
                               class="w-full bg-slate-50/80 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500 transition-all">
                    </div>
                </div>

                <!-- Right Header Actions -->
                <div class="flex items-center gap-3 md:gap-4">
                    <!-- Date & Shift Info -->
                    <div class="hidden lg:flex items-center gap-2 text-xs font-medium text-slate-600 bg-slate-50 border border-slate-200/80 px-3 py-2 rounded-xl">
                        <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                        <span>Kamis, 24 Okt • Shift Pagi</span>
                    </div>

                    <!-- Primary Action Button: + Catat Stok -->
                    <a href="{{ route('stok.index') }}" 
                       class="bg-brand-dark hover:bg-brand-800 text-white font-semibold text-sm px-3 md:px-4 py-2 rounded-xl flex items-center gap-2 shadow-sm transition-all hover:shadow">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span class="hidden md:inline">Catat Stok</span>
                    </a>

                    <!-- Notification Bell -->
                    <button class="relative p-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 transition-all">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                    </button>

                    <!-- Avatar Dropdown -->
                    <div class="flex items-center gap-1 cursor-pointer">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=120" 
                             alt="Profile" class="w-9 h-9 rounded-full object-cover border border-brand-500/40">
                        <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 hidden sm:block"></i>
                    </div>
                </div>
            </header>

            <!-- Main Page View Content -->
            <main class="flex-1 p-4 md:p-8">
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();
    </script>
    @stack('scripts')
</body>
</html>
