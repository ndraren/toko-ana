@extends('layouts.app')

@section('content')
<div x-data="cashierApp()" x-init="init()" class="min-h-screen font-sans pb-10">
    <!-- Header -->
    <header class="bg-gradient-to-r from-brand-700 to-brand-800 text-white sticky top-0 z-30 shadow-md">
        <div class="px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-white/15 p-2 rounded-lg backdrop-blur-sm">
                    <i data-lucide="monitor" class="w-6 h-6"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold leading-tight">KASIR POS</h1>
                    <p class="text-sm text-brand-100 flex items-center gap-1" x-text="currentTime"></p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden md:flex items-center gap-2 bg-white/15 px-3 py-1.5 rounded-full backdrop-blur-sm">
                    <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                    <span class="text-sm font-medium text-white/90">Online</span>
                </div>
                <div class="flex items-center gap-2 pl-4 border-l border-white/20">
                    <div class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center font-bold backdrop-blur-sm">
                        {{ substr(Auth::user()->name ?? 'Kasir', 0, 1) }}
                    </div>
                    <div class="text-sm">
                        <p class="font-medium text-white">{{ Auth::user()->name ?? 'Kasir' }}</p>
                        <p class="text-xs text-brand-200">Role: Kasir</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="w-full px-3 py-3">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-start">
            
            <!-- LEFT SIDE: Nota / Keranjang (col-span-5) -->
            <div class="lg:col-span-5 relative order-2 lg:order-1">
                <div class="rounded-xl shadow-md border border-brand-200/50 flex flex-col h-fit sticky top-20 overflow-hidden">
                    
                    <!-- Cart Header - Green themed -->
                    <div class="px-4 py-3 bg-gradient-to-r from-brand-600 to-brand-700 text-white flex justify-between items-center">
                        <h2 class="font-bold flex items-center gap-2">
                            <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                            Pesanan Saat Ini
                            <span class="bg-white/20 text-white text-xs py-0.5 px-2 rounded-full font-semibold backdrop-blur-sm" x-text="totalItems + ' item'"></span>
                        </h2>
                        <button @click="clearCart()" x-show="cart.length > 0" class="text-xs font-medium text-white/80 hover:text-white hover:bg-white/10 px-2 py-1 rounded transition-colors flex items-center gap-1">
                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i> Kosongkan
                        </button>
                    </div>

                    <!-- Customer Search - Light green bg -->
                    <div class="p-3 border-b border-brand-100 bg-brand-50/50 relative">
                        <div class="relative">
                            <i data-lucide="user" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-brand-400"></i>
                            <input type="text" @input.debounce.300ms="searchCustomers($event.target.value)" placeholder="Cari Pelanggan (opsional)..." class="w-full pl-9 pr-8 py-2 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm outline-none transition-all">
                            <button x-show="selectedCustomer" @click="clearCustomer()" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </div>
                        
                        <!-- Customer Autocomplete Results -->
                        <div x-show="customerResults.length > 0" @click.away="customerResults = []" class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg z-50 max-h-48 overflow-y-auto">
                            <template x-for="c in customerResults" :key="c.id">
                                <div @click="selectCustomer(c)" class="px-4 py-2 hover:bg-brand-50 cursor-pointer border-b border-slate-100 last:border-0 flex justify-between items-center">
                                    <div>
                                        <div class="font-medium text-sm text-slate-800" x-text="c.name"></div>
                                        <div class="text-xs text-slate-500" x-text="c.phone || '-'"></div>
                                    </div>
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium" :class="c.is_reseller ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600'" x-text="c.is_reseller ? 'Reseller' : 'Reguler'"></span>
                                </div>
                            </template>
                        </div>
                        
                        <!-- Selected Customer Info -->
                        <div x-show="selectedCustomer" class="mt-2 flex items-center justify-between bg-brand-100 px-3 py-2 rounded-md border border-brand-200">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-brand-200 text-brand-700 flex items-center justify-center text-xs font-bold" x-text="selectedCustomer?.name.charAt(0)"></div>
                                <span class="text-sm font-medium text-brand-900" x-text="selectedCustomer?.name"></span>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-brand-200 text-brand-800 font-medium" x-text="selectedCustomer?.is_reseller ? 'Reseller' : 'Reguler'"></span>
                        </div>
                    </div>

                    <!-- Mode Indicator -->
                    <div x-show="saleMode === 'grosir'" class="bg-purple-600 text-white text-xs font-medium px-4 py-1.5 flex justify-center items-center gap-2 shadow-inner">
                        <i data-lucide="tags" class="w-3.5 h-3.5"></i>
                        MODE HARGA GROSIR AKTIF
                    </div>

                    <!-- Cart Items - Light green background -->
                    <div class="flex-1 overflow-y-auto max-h-[320px] p-2 bg-brand-50/30 custom-scrollbar">
                        <div x-show="cart.length === 0" class="flex flex-col items-center justify-center h-48 text-brand-300">
                            <i data-lucide="shopping-basket" class="w-12 h-12 mb-3"></i>
                            <p class="text-sm font-medium text-brand-400">Keranjang masih kosong</p>
                            <p class="text-xs mt-1 text-brand-300">Pilih produk di sebelah kanan</p>
                        </div>

                        <template x-for="(item, index) in cart" :key="index + '-' + item.selected_unit_id">
                            <div class="bg-white p-3 mb-2 rounded-lg border border-brand-100 shadow-sm relative group">
                                <div class="flex justify-between items-start mb-2 pr-6">
                                    <div>
                                        <h4 class="text-sm font-semibold text-slate-800 leading-tight" x-text="item.name"></h4>
                                        <div class="flex items-center gap-1 mt-1">
                                            <span class="text-[10px] text-slate-500" x-text="item.sku"></span>
                                            <!-- Price badge logic -->
                                            <template x-if="saleMode === 'grosir'">
                                                <span class="text-[9px] bg-purple-100 text-purple-700 px-1.5 py-0.5 rounded border border-purple-200">GROSIR</span>
                                            </template>
                                            <template x-if="saleMode !== 'grosir' && item.quantity >= getSelectedUnit(item)?.price_tier?.min_wholesale_qty && getSelectedUnit(item)?.price_tier?.price_wholesale > 0">
                                                <span class="text-[9px] bg-indigo-100 text-indigo-700 px-1.5 py-0.5 rounded border border-indigo-200">GROSIR AUTO</span>
                                            </template>
                                            <template x-if="saleMode !== 'grosir' && (item.quantity < getSelectedUnit(item)?.price_tier?.min_wholesale_qty || !getSelectedUnit(item)?.price_tier?.price_wholesale)">
                                                <span class="text-[9px] bg-slate-100 text-slate-600 px-1.5 py-0.5 rounded border border-slate-200">ECERAN</span>
                                            </template>
                                        </div>
                                    </div>
                                    <button @click="removeFromCart(index)" class="absolute right-2 top-2 text-slate-400 hover:text-red-500 hover:bg-red-50 p-1 rounded transition-colors">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>

                                <!-- Unit Selector Dropdown -->
                                <div class="mt-2 mb-2" x-show="item.units && item.units.length > 1">
                                    <select x-model.number="item.selected_unit_id" @change="item.quantity = 1" class="text-xs bg-white border border-brand-200 text-slate-800 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 w-full font-bold shadow-sm">
                                        <template x-for="unit in item.units" :key="unit.id">
                                            <option :value="unit.id" x-text="unit.unit_name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="mt-2" x-show="item.units && item.units.length <= 1">
                                    <span class="px-2 py-1 rounded-md border border-brand-100 bg-brand-50 text-brand-600 text-[10px] font-bold" x-text="getSelectedUnit(item)?.unit_name"></span>
                                </div>

                                <!-- Price & Info line -->
                                <div class="flex justify-between items-end mb-2 text-xs">
                                    <div class="text-slate-600">
                                        <span x-text="formatRupiah(getItemPrice(item))"></span>
                                        <span class="text-slate-400" x-text="` / ${getSelectedUnit(item)?.unit_name}`"></span>
                                    </div>
                                    <div x-show="getSelectedUnit(item)?.conversion_factor > 1" class="text-[10px] text-slate-400 italic" x-text="`Isi ${getSelectedUnit(item)?.conversion_factor} btg/pcs`"></div>
                                </div>

                                <!-- Qty & Subtotal -->
                                <div class="flex justify-between items-center mt-3 pt-2 border-t border-brand-100/60">
                                    <div class="flex items-center bg-brand-50 rounded-lg border border-brand-200 p-0.5">
                                        <button @click="updateQty(index, item.quantity - 1)" class="w-7 h-7 flex items-center justify-center text-brand-600 hover:bg-white hover:text-brand-700 hover:shadow-sm rounded transition-all disabled:opacity-50" :disabled="item.quantity <= 1">
                                            <i data-lucide="minus" class="w-3 h-3"></i>
                                        </button>
                                        <input type="number" x-model.number="item.quantity" @change="updateQty(index, $event.target.value)" class="w-10 text-center bg-transparent border-none text-sm font-semibold text-slate-800 focus:ring-0 p-0 appearance-none m-0" min="1">
                                        <button @click="updateQty(index, item.quantity + 1)" class="w-7 h-7 flex items-center justify-center text-brand-600 hover:bg-white hover:text-brand-700 hover:shadow-sm rounded transition-all">
                                            <i data-lucide="plus" class="w-3 h-3"></i>
                                        </button>
                                    </div>
                                    <div class="font-bold text-brand-800 text-sm" x-text="formatRupiah(getItemSubtotal(item))"></div>
                                </div>
                                
                                <!-- Stock Warning -->
                                <div x-show="getStockWarning(item)" class="mt-2 text-[10px] text-red-500 flex items-center gap-1 bg-red-50 px-2 py-1 rounded">
                                    <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                    <span x-text="getStockWarning(item)"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Payment Section - Darker green theme -->
                    <div class="p-4 bg-gradient-to-b from-brand-50 to-brand-100/50 border-t border-brand-200">
                        <div class="flex justify-between items-end mb-4">
                            <div>
                                <span class="text-sm font-medium text-brand-700">Total Pembayaran</span>
                                <div x-show="saleMode === 'grosir'" class="text-[10px] text-purple-600 font-bold bg-purple-100 px-2 py-0.5 rounded inline-block ml-2">HARGA GROSIR</div>
                            </div>
                            <span class="text-2xl font-black text-brand-800" x-text="formatRupiah(totalAmount)"></span>
                        </div>

                        <!-- Payment Method -->
                        <div class="grid grid-cols-4 gap-2 mb-4">
                            <button @click="paymentMethod = 'tunai'" class="py-2 px-1 rounded-lg border text-xs font-semibold flex flex-col items-center gap-1 transition-all" :class="paymentMethod === 'tunai' ? 'bg-brand-600 border-brand-600 text-white shadow-md' : 'bg-white border-brand-200 text-slate-600 hover:bg-brand-50'">
                                <i data-lucide="banknote" class="w-4 h-4"></i> Tunai
                            </button>
                            <button @click="paymentMethod = 'qris'" class="py-2 px-1 rounded-lg border text-xs font-semibold flex flex-col items-center gap-1 transition-all" :class="paymentMethod === 'qris' ? 'bg-brand-600 border-brand-600 text-white shadow-md' : 'bg-white border-brand-200 text-slate-600 hover:bg-brand-50'">
                                <i data-lucide="qr-code" class="w-4 h-4"></i> QRIS
                            </button>
                            <button @click="paymentMethod = 'transfer'" class="py-2 px-1 rounded-lg border text-xs font-semibold flex flex-col items-center gap-1 transition-all" :class="paymentMethod === 'transfer' ? 'bg-brand-600 border-brand-600 text-white shadow-md' : 'bg-white border-brand-200 text-slate-600 hover:bg-brand-50'">
                                <i data-lucide="smartphone" class="w-4 h-4"></i> Trf/EDC
                            </button>
                            <button @click="paymentMethod = 'hutang'" class="py-2 px-1 rounded-lg border text-xs font-semibold flex flex-col items-center gap-1 transition-all" :class="paymentMethod === 'hutang' ? 'bg-orange-500 border-orange-500 text-white shadow-md' : 'bg-white border-brand-200 text-slate-600 hover:bg-brand-50'">
                                <i data-lucide="book" class="w-4 h-4"></i> Piutang
                            </button>
                        </div>

                        <!-- Input Nominal (Only for Tunai/Hutang) -->
                        <div x-show="['tunai', 'hutang'].includes(paymentMethod)" class="mb-4 space-y-2">
                            <div class="flex justify-between items-center">
                                <label class="text-xs font-medium text-brand-700" x-text="paymentMethod === 'hutang' ? 'Uang Muka (DP)' : 'Nominal Diterima'"></label>
                                <span class="text-xs font-bold text-brand-600" x-show="paidAmount - totalAmount > 0" x-text="'Kembali: ' + formatRupiah(paidAmount - totalAmount)"></span>
                                <span class="text-xs font-bold text-orange-500" x-show="paymentMethod === 'hutang' && totalAmount - paidAmount > 0" x-text="'Sisa: ' + formatRupiah(totalAmount - paidAmount)"></span>
                            </div>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-brand-500 font-medium">Rp</span>
                                <input type="number" x-model.number="paidAmount" class="w-full pl-9 pr-3 py-2 bg-white border border-brand-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-lg font-bold text-slate-800 outline-none" min="0">
                            </div>
                            <!-- Quick Amounts -->
                            <div x-show="paymentMethod === 'tunai'" class="flex flex-wrap gap-1.5 mt-2">
                                <button @click="paidAmount = totalAmount" class="text-[10px] bg-white hover:bg-brand-50 text-brand-700 px-2 py-1 rounded font-medium border border-brand-200">Uang Pas</button>
                                <template x-for="amt in quickAmounts" :key="amt">
                                    <button @click="paidAmount = amt" class="text-[10px] bg-white hover:bg-brand-50 text-brand-700 px-2 py-1 rounded font-medium border border-brand-200" x-text="formatRupiahShort(amt)"></button>
                                </template>
                            </div>
                        </div>

                        <!-- Manual Customer Name (If not selected) -->
                        <div x-show="!selectedCustomer" class="mb-3">
                            <input type="text" x-model="customerNameManual" placeholder="Nama Pelanggan Umum (Opsional)..." class="w-full px-3 py-2 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm outline-none">
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <input type="text" x-model="notes" placeholder="Catatan transaksi (Opsional)..." class="w-full px-3 py-2 bg-white border border-brand-200 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 text-sm outline-none">
                        </div>

                        <!-- Submit Button Form - Bold green checkout button -->
                        <form id="checkoutForm" action="{{ route('kasir.checkout') }}" method="POST" @submit.prevent="submitCheckout">
                            @csrf
                            <input type="hidden" name="items" x-ref="itemsInput">
                            <input type="hidden" name="payment_method" x-model="paymentMethod">
                            <input type="hidden" name="paid_amount" x-model="paidAmount">
                            <input type="hidden" name="sale_mode" x-model="saleMode">
                            <input type="hidden" name="customer_id" :value="selectedCustomer ? selectedCustomer.id : ''">
                            <input type="hidden" name="customer_name" x-model="customerNameManual">
                            <input type="hidden" name="notes" x-model="notes">

                            <button type="submit" 
                                class="w-full py-3.5 rounded-xl font-bold text-white flex justify-center items-center gap-2 transition-all shadow-lg hover:shadow-xl active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed disabled:active:scale-100"
                                :class="canCheckout ? 'bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 shadow-brand-600/30' : 'bg-slate-400 shadow-none'"
                                :disabled="!canCheckout || isSubmitting">
                                <template x-if="isSubmitting">
                                    <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                                </template>
                                <template x-if="!isSubmitting">
                                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                                </template>
                                <span x-text="isSubmitting ? 'Memproses...' : 'Proses Pembayaran'"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE: Products (col-span-7) -->
            <div class="lg:col-span-7 flex flex-col gap-3 order-1 lg:order-2">
                
                <!-- Search & Filter - Subtle white card -->
                <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <i data-lucide="search" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" x-model="searchQuery" @input="filterProducts()" placeholder="Cari nama atau SKU produk..." class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all text-sm outline-none">
                    </div>
                    <select x-model="selectedCategory" @change="filterProducts()" class="py-2 pl-3 pr-8 bg-slate-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all text-sm outline-none cursor-pointer">
                        <option value="">Semua Kategori</option>
                        @php
                            $categories = collect($products)->pluck('category')->unique()->filter();
                        @endphp
                        @foreach($categories as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Product Grid - Slate/white themed -->
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 overflow-y-auto max-h-[calc(100vh-180px)] pr-1 custom-scrollbar">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <div @click="addToCart(product)" class="bg-white p-3.5 rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-brand-300 transition-all cursor-pointer group flex flex-col relative overflow-hidden">
                            <!-- Out of stock overlay -->
                            <div x-show="product.stock <= 0" class="absolute inset-0 bg-white/70 backdrop-blur-[1px] z-10 flex items-center justify-center">
                                <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold border border-red-200 shadow-sm">Stok Habis</span>
                            </div>

                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs font-medium px-2 py-0.5 bg-slate-100 text-slate-600 rounded-md border border-slate-200" x-text="product.sku"></span>
                                <div class="flex items-center gap-1" :class="product.stock <= product.min_stock ? 'text-red-500' : 'text-brand-500'">
                                    <i data-lucide="box" class="w-3.5 h-3.5"></i>
                                    <span class="text-xs font-bold" x-text="product.stock"></span>
                                </div>
                            </div>
                            
                            <h3 class="font-semibold text-slate-800 text-sm leading-snug mb-1 group-hover:text-brand-600 transition-colors line-clamp-2" x-text="product.name"></h3>
                            <p class="text-xs text-slate-500 mb-2" x-text="product.category"></p>
                            
                            <div class="mt-auto pt-3 border-t border-slate-100 flex flex-col gap-1">
                                <div class="flex justify-between items-end">
                                    <span class="text-xs text-slate-500" x-text="product.units && product.units.length > 0 ? 'Mulai' : ''"></span>
                                    <span class="font-bold text-brand-700 text-base" x-text="formatRupiah(product.units[0]?.price_tier?.price_retail || 0)"></span>
                                </div>
                                <div x-show="product.units && product.units.length > 1" class="text-[10px] text-right text-slate-400" x-text="`${product.units.length} satuan tersedia`"></div>
                            </div>
                        </div>
                    </template>
                    <div x-show="filteredProducts.length === 0" class="col-span-full py-12 text-center flex flex-col items-center justify-center bg-white rounded-xl border border-dashed border-slate-300">
                        <i data-lucide="package-search" class="w-12 h-12 text-slate-300 mb-3"></i>
                        <h3 class="text-base font-medium text-slate-700">Produk tidak ditemukan</h3>
                        <p class="text-sm text-slate-500">Coba ubah kata kunci atau kategori pencarian.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('cashierApp', () => ({
            products: @json($products),
            filteredProducts: [],
            searchQuery: '',
            selectedCategory: '',
            
            cart: [],
            saleMode: 'eceran', // 'eceran' atau 'grosir'
            
            selectedCustomer: null,
            customerResults: [],
            
            paymentMethod: 'tunai',
            paidAmount: 0,
            customerNameManual: '',
            notes: '',
            
            currentTime: '',
            isSubmitting: false,
            
            init() {
                this.filteredProducts = this.products;
                this.updateTime();
                setInterval(() => this.updateTime(), 1000);
                
                // Watch for total changes to update default paidAmount
                this.$watch('totalAmount', (value) => {
                    if(this.paymentMethod !== 'tunai' && this.paymentMethod !== 'hutang') {
                        this.paidAmount = value;
                    }
                });
                
                this.$watch('paymentMethod', (val) => {
                    if(['qris', 'transfer'].includes(val)) {
                        this.paidAmount = this.totalAmount;
                    } else if (val === 'tunai' && this.paidAmount < this.totalAmount) {
                        this.paidAmount = this.totalAmount;
                    }
                });
            },
            
            updateTime() {
                const now = new Date();
                this.currentTime = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) + ' ' + 
                                   now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            },
            
            filterProducts() {
                let result = this.products;
                
                if (this.selectedCategory) {
                    result = result.filter(p => p.category === this.selectedCategory);
                }
                
                if (this.searchQuery) {
                    const q = this.searchQuery.toLowerCase();
                    result = result.filter(p => 
                        p.name.toLowerCase().includes(q) || 
                        p.sku.toLowerCase().includes(q) ||
                        (p.barcode && p.barcode.toLowerCase().includes(q))
                    );
                }
                
                this.filteredProducts = result;
            },
            
            async searchCustomers(q) {
                if (!q || q.length < 2) {
                    this.customerResults = [];
                    return;
                }
                try {
                    const res = await fetch(`/pelanggan/search?q=${encodeURIComponent(q)}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (res.ok) {
                        const data = await res.json();
                        this.customerResults = data;
                    }
                } catch (e) {
                    console.error('Error fetching customers', e);
                }
            },
            
            selectCustomer(c) {
                this.selectedCustomer = c;
                this.customerResults = [];
                if (c.is_reseller) {
                    this.saleMode = 'grosir';
                } else {
                    this.saleMode = 'eceran';
                }
            },
            
            clearCustomer() {
                this.selectedCustomer = null;
                this.saleMode = 'eceran';
            },
            
            addToCart(product) {
                if (product.stock <= 0) {
                    alert('Stok produk habis!');
                    return;
                }
                
                if (!product.units || product.units.length === 0) {
                    alert('Produk tidak memiliki satuan/harga!');
                    return;
                }

                // Default unit is the one with sort_order = 0, or just the first one
                let defaultUnit = product.units.find(u => u.sort_order === 0) || product.units[0];
                
                // Check if already in cart with same unit
                let existingIndex = this.cart.findIndex(i => i.product_id === product.id && i.selected_unit_id === defaultUnit.id);
                
                if (existingIndex > -1) {
                    let newQty = this.cart[existingIndex].quantity + 1;
                    this.updateQty(existingIndex, newQty);
                } else {
                    this.cart.unshift({
                        product_id: product.id,
                        name: product.name,
                        sku: product.sku,
                        stock: product.stock,
                        units: product.units,
                        selected_unit_id: defaultUnit.id,
                        quantity: 1
                    });
                    
                    // Trigger total watch manually if needed or let Alpine handle it
                    if(this.paymentMethod === 'tunai' && this.paidAmount === this.totalAmount - this.getItemSubtotal(this.cart[0])) {
                        this.paidAmount = this.totalAmount;
                    }
                }
            },
            
            removeFromCart(index) {
                this.cart.splice(index, 1);
            },
            
            changeUnit(cartIndex, unitObj) {
                this.cart[cartIndex].selected_unit_id = unitObj.id;
                this.cart[cartIndex].quantity = 1;
            },
            
            updateQty(index, qty) {
                let parsedQty = parseInt(qty);
                if (isNaN(parsedQty) || parsedQty < 1) {
                    this.cart[index].quantity = 1;
                    return;
                }
                this.cart[index].quantity = parsedQty;
            },
            
            clearCart() {
                if (confirm('Yakin ingin mengosongkan keranjang?')) {
                    this.cart = [];
                    this.paidAmount = 0;
                }
            },
            
            getSelectedUnit(item) {
                if (!item.units) return null;
                return item.units.find(u => u.id === item.selected_unit_id);
            },
            
            getItemPrice(item) {
                let unit = this.getSelectedUnit(item);
                if (!unit || !unit.price_tier) return 0;
                let tier = unit.price_tier;
                
                let p_retail = parseFloat(tier.price_retail) || 0;
                let p_wholesale = parseFloat(tier.price_wholesale) || 0;
                let min_qty = tier.min_wholesale_qty || 999999;
                
                if (this.saleMode === 'grosir') {
                    return p_wholesale > 0 ? p_wholesale : p_retail;
                }
                
                if (item.quantity >= min_qty && p_wholesale > 0) {
                    return p_wholesale;
                }
                
                return p_retail;
            },
            
            getItemSubtotal(item) {
                return this.getItemPrice(item) * item.quantity;
            },
            
            getBaseUnitsInCart(productId) {
                let totalBase = 0;
                this.cart.forEach(item => {
                    if (item.product_id === productId) {
                        let unit = this.getSelectedUnit(item);
                        let cf = unit ? (unit.conversion_factor || 1) : 1;
                        totalBase += item.quantity * cf;
                    }
                });
                return totalBase;
            },
            
            getStockWarning(item) {
                let totalBaseNeeded = this.getBaseUnitsInCart(item.product_id);
                if (totalBaseNeeded > item.stock) {
                    return `Stok tidak cukup! (Minta: ${totalBaseNeeded}, Sisa: ${item.stock})`;
                }
                return null;
            },
            
            get totalAmount() {
                return this.cart.reduce((sum, item) => sum + this.getItemSubtotal(item), 0);
            },
            
            get totalItems() {
                return this.cart.reduce((sum, item) => sum + parseInt(item.quantity || 0), 0);
            },
            
            get canCheckout() {
                if (this.cart.length === 0) return false;
                
                // Check stock violations
                let hasStockIssue = false;
                let processedIds = [];
                for (let item of this.cart) {
                    if (!processedIds.includes(item.product_id)) {
                        if (this.getBaseUnitsInCart(item.product_id) > item.stock) {
                            hasStockIssue = true;
                            break;
                        }
                        processedIds.push(item.product_id);
                    }
                }
                if (hasStockIssue) return false;
                
                // Check payment
                if (this.paymentMethod === 'tunai' && this.paidAmount < this.totalAmount && this.totalAmount > 0) {
                    return false;
                }
                
                return true;
            },
            
            get quickAmounts() {
                let amounts = [];
                let t = this.totalAmount;
                if (t <= 0) return amounts;
                
                const addUnique = (val) => { if(val > t && !amounts.includes(val)) amounts.push(val); };
                
                // Roundings
                let nearest10k = Math.ceil(t / 10000) * 10000;
                let nearest50k = Math.ceil(t / 50000) * 50000;
                let nearest100k = Math.ceil(t / 100000) * 100000;
                
                addUnique(nearest10k);
                addUnique(nearest50k);
                addUnique(nearest100k);
                
                return amounts.sort((a, b) => a - b).slice(0, 3);
            },
            
            formatRupiah(amount) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(amount);
            },
            
            formatRupiahShort(amount) {
                if (amount >= 1000000) {
                    return 'Rp ' + (amount / 1000000) + 'Jt';
                }
                if (amount >= 1000) {
                    return 'Rp ' + (amount / 1000) + 'k';
                }
                return this.formatRupiah(amount);
            },
            
            submitCheckout() {
                if (!this.canCheckout) return;
                
                this.isSubmitting = true;
                
                // Map cart to simple array for backend
                const cartData = this.cart.map(item => ({
                    product_id: item.product_id,
                    selected_unit_id: item.selected_unit_id,
                    quantity: item.quantity
                }));
                
                this.$refs.itemsInput.value = JSON.stringify(cartData);
                
                // Small delay to allow alpine to update DOM bindings before actual submission
                setTimeout(() => {
                    document.getElementById('checkoutForm').submit();
                }, 100);
            }
        }));
    });
</script>
@endpush