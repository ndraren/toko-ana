@extends('layouts.app')

@section('content')
<div class="space-y-0 max-w-full mx-auto" x-data="kasirApp()" x-init="init()">
    <!-- Header -->
    <div class="flex items-center justify-between mb-5">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <span>KASIR POS</span>
                <span>&bull;</span>
                <span class="text-brand-600">Transaksi Penjualan</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight mt-1">Kasir Toko Berkah</h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Scan atau pilih barang, pilih satuan kemasan, dan cetak struk otomatis.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 text-xs font-medium text-slate-600 bg-slate-50 border border-slate-200/80 px-3 py-2 rounded-xl">
                <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                <span>Kasir: <strong>Budi Santoso</strong></span>
            </div>
            <div class="flex items-center gap-2 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-2 rounded-xl">
                <i data-lucide="clock" class="w-4 h-4"></i>
                <span x-text="currentTime"></span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-5">
        <!-- LEFT: Product Grid -->
        <div class="col-span-7 space-y-4">
            <!-- Search & Filter -->
            <div class="flex items-center gap-3">
                <div class="relative flex-1">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" x-model="searchQuery" @input="filterProducts()"
                           placeholder="Cari nama barang, SKU, atau barcode..."
                           class="w-full bg-white border border-slate-200 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500">
                </div>
                <select x-model="selectedCategory" @change="filterProducts()"
                        class="bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                    <option value="all">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-3 gap-3 max-h-[calc(100vh-260px)] overflow-y-auto pr-1 pb-2">
                <template x-for="product in filteredProducts" :key="product.id">
                    <button @click="addToCart(product)"
                            class="bg-white border border-slate-200/80 rounded-xl p-3.5 text-left hover:border-brand-400 hover:shadow-md transition-all group relative">
                        <!-- Stock Badge -->
                        <div class="absolute top-2 right-2">
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-md"
                                  :class="product.stock <= product.min_stock ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'"
                                  x-text="'Stok: ' + product.stock"></span>
                        </div>
                        <p class="font-bold text-slate-900 text-sm truncate pr-14" x-text="product.name"></p>
                        <p class="text-[11px] text-slate-400 mt-0.5 truncate" x-text="'SKU: ' + product.sku"></p>

                        <!-- 3-Tier Pricing Display -->
                        <div class="mt-2.5 space-y-1">
                            <!-- Satuan -->
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-semibold text-slate-400" x-text="product.unit"></span>
                                <span class="text-xs font-bold text-slate-900" x-text="formatRupiah(product.selling_price)"></span>
                            </div>
                            <!-- Renceng/Pack -->
                            <template x-if="product.pack_price > 0 && product.pack_qty > 0">
                                <div class="flex items-center justify-between bg-blue-50/70 rounded px-1.5 py-0.5 -mx-0.5">
                                    <span class="text-[10px] font-semibold text-blue-600" x-text="product.pack_name + ' (' + product.pack_qty + ' ' + product.unit + ')'"></span>
                                    <span class="text-[11px] font-bold text-blue-800" x-text="formatRupiah(product.pack_price)"></span>
                                </div>
                            </template>
                            <!-- Dus/Karton -->
                            <template x-if="product.box_price > 0 && product.box_qty > 0">
                                <div class="flex items-center justify-between bg-amber-50/70 rounded px-1.5 py-0.5 -mx-0.5">
                                    <span class="text-[10px] font-semibold text-amber-600" x-text="product.box_name + ' (' + product.box_qty + ' ' + product.unit + ')'"></span>
                                    <span class="text-[11px] font-bold text-amber-800" x-text="formatRupiah(product.box_price)"></span>
                                </div>
                            </template>
                        </div>

                        <div class="mt-2 w-full py-1.5 bg-brand-50 text-brand-700 text-[11px] font-bold rounded-lg text-center opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1">
                            <i data-lucide="plus" class="w-3 h-3"></i>
                            <span>Tambah</span>
                        </div>
                    </button>
                </template>
                <template x-if="filteredProducts.length === 0">
                    <div class="col-span-3 py-16 text-center text-slate-400">
                        <i data-lucide="search-x" class="w-10 h-10 mx-auto mb-3 text-slate-300"></i>
                        <p class="font-semibold text-sm">Produk tidak ditemukan</p>
                    </div>
                </template>
            </div>
        </div>

        <!-- RIGHT: Cart & Payment -->
        <div class="col-span-5">
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs flex flex-col h-[calc(100vh-200px)] sticky top-24">
                <!-- Cart Header -->
                <div class="px-5 py-4 border-b border-slate-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i data-lucide="shopping-cart" class="w-5 h-5 text-brand-700"></i>
                            <h3 class="font-bold text-slate-900 text-base">Keranjang Belanja</h3>
                            <span class="bg-brand-100 text-brand-800 text-[10px] font-bold px-2 py-0.5 rounded-full" x-text="cart.length + ' item'"></span>
                        </div>
                        <button @click="clearCart()" x-show="cart.length > 0" class="text-xs font-semibold text-red-500 hover:text-red-700">Kosongkan</button>
                    </div>
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto px-5 py-3 space-y-2.5">
                    <template x-if="cart.length === 0">
                        <div class="flex flex-col items-center justify-center h-full text-slate-400 py-8">
                            <i data-lucide="shopping-bag" class="w-12 h-12 mb-3 text-slate-300"></i>
                            <p class="font-semibold text-sm">Keranjang kosong</p>
                            <p class="text-xs mt-1">Klik produk untuk menambahkan</p>
                        </div>
                    </template>

                    <template x-for="(item, index) in cart" :key="item.product_id + '-' + item.selected_unit">
                        <div class="bg-slate-50/70 border border-slate-100 rounded-xl p-3">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-900 text-xs truncate" x-text="item.name"></p>
                                </div>
                                <!-- Auto Badge: ECERAN / GROSIR -->
                                <template x-if="item.selected_unit === 'piece'">
                                    <span class="text-[9px] font-bold bg-slate-200 text-slate-600 px-1.5 py-0.5 rounded shrink-0">ECERAN</span>
                                </template>
                                <template x-if="item.selected_unit !== 'piece'">
                                    <span class="text-[9px] font-bold bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded shrink-0">GROSIR</span>
                                </template>
                            </div>

                            <!-- Unit Selector (Satuan / Renceng / Dus) -->
                            <div class="flex items-center gap-1 mt-2">
                                <button @click="changeUnit(index, 'piece')" type="button"
                                        :class="item.selected_unit === 'piece' ? 'bg-brand-100 border-brand-400 text-brand-800' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50'"
                                        class="px-2 py-1 rounded-md border text-[10px] font-bold transition-all"
                                        x-text="item.unit_name_piece"></button>
                                <template x-if="item.pack_price > 0">
                                    <button @click="changeUnit(index, 'pack')" type="button"
                                            :class="item.selected_unit === 'pack' ? 'bg-blue-100 border-blue-400 text-blue-800' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50'"
                                            class="px-2 py-1 rounded-md border text-[10px] font-bold transition-all"
                                            x-text="item.pack_name"></button>
                                </template>
                                <template x-if="item.box_price > 0">
                                    <button @click="changeUnit(index, 'box')" type="button"
                                            :class="item.selected_unit === 'box' ? 'bg-amber-100 border-amber-400 text-amber-800' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50'"
                                            class="px-2 py-1 rounded-md border text-[10px] font-bold transition-all"
                                            x-text="item.box_name"></button>
                                </template>
                            </div>

                            <!-- Price info -->
                            <div class="flex items-center gap-1.5 mt-1.5">
                                <span class="text-[10px] font-semibold text-slate-400" x-text="formatRupiah(getItemPrice(item)) + ' / ' + getUnitLabel(item)"></span>
                                <template x-if="item.selected_unit === 'pack'">
                                    <span class="text-[9px] font-semibold text-blue-500">isi <span x-text="item.pack_qty"></span> <span x-text="item.unit_name_piece"></span></span>
                                </template>
                                <template x-if="item.selected_unit === 'box'">
                                    <span class="text-[9px] font-semibold text-amber-500">isi <span x-text="item.box_qty"></span> <span x-text="item.unit_name_piece"></span></span>
                                </template>
                            </div>

                            <div class="flex items-center justify-between mt-2">
                                <!-- Quantity Controls -->
                                <div class="flex items-center bg-white border border-slate-200 rounded-lg">
                                    <button @click="updateQty(index, item.quantity - 1)" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-100 rounded-l-lg font-bold text-sm">-</button>
                                    <input type="number" x-model.number="item.quantity" @change="updateQty(index, item.quantity)" min="1"
                                           class="w-10 h-7 text-center bg-transparent border-x border-slate-200 text-xs font-bold text-slate-900 focus:outline-none">
                                    <button @click="updateQty(index, item.quantity + 1)" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-100 rounded-r-lg font-bold text-sm">+</button>
                                </div>
                                <span class="font-bold text-sm text-slate-900" x-text="formatRupiah(getItemSubtotal(item))"></span>
                            </div>

                            <!-- DELETE BUTTON (Always Visible) -->
                            <button @click="removeFromCart(index)" 
                                    class="mt-2 w-full flex items-center justify-center gap-1.5 py-1.5 bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 hover:text-red-700 text-[11px] font-bold rounded-lg transition-all">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                <span>Hapus Barang</span>
                            </button>
                        </div>
                    </template>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Cart Footer: Payment -->
                <div class="border-t border-slate-100 px-5 py-4 space-y-3 bg-slate-50/50 rounded-b-2xl">
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 font-medium">Subtotal (<span x-text="totalItems"></span> item)</span>
                            <span class="font-bold text-slate-800" x-text="formatRupiah(totalAmount)"></span>
                        </div>
                        <div class="flex items-center justify-between text-sm pt-1.5 border-t border-slate-200">
                            <span class="font-bold text-slate-900">TOTAL</span>
                            <span class="font-black text-lg text-brand-700" x-text="formatRupiah(totalAmount)"></span>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Metode Pembayaran</label>
                        <div class="grid grid-cols-4 gap-1.5 mt-1.5">
                            <template x-for="method in ['Tunai', 'QRIS', 'Transfer', 'Debit']" :key="method">
                                <button @click="paymentMethod = method" type="button"
                                        :class="paymentMethod === method ? 'bg-brand-100 border-brand-500 text-brand-800' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'"
                                        class="px-2 py-2 rounded-lg border text-[11px] font-bold transition-all text-center"
                                        x-text="method"></button>
                            </template>
                        </div>
                    </div>

                    <!-- Paid Amount (Tunai) -->
                    <template x-if="paymentMethod === 'Tunai'">
                        <div>
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Uang Diterima</label>
                            <input type="number" x-model.number="paidAmount" min="0"
                                   class="w-full mt-1.5 bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-500/30"
                                   placeholder="Masukkan jumlah uang...">
                            <template x-if="paidAmount > 0 && paidAmount >= totalAmount">
                                <div class="flex items-center justify-between mt-2 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2">
                                    <span class="text-xs font-semibold text-emerald-700">Kembalian</span>
                                    <span class="text-sm font-black text-emerald-800" x-text="formatRupiah(paidAmount - totalAmount)"></span>
                                </div>
                            </template>
                            <template x-if="paidAmount > 0 && paidAmount < totalAmount">
                                <div class="flex items-center justify-between mt-2 bg-red-50 border border-red-200 rounded-lg px-3 py-2">
                                    <span class="text-xs font-semibold text-red-700">Kurang</span>
                                    <span class="text-sm font-black text-red-800" x-text="formatRupiah(totalAmount - paidAmount)"></span>
                                </div>
                            </template>
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <template x-for="amount in quickAmounts" :key="amount">
                                    <button @click="paidAmount = amount" type="button"
                                            class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold rounded-md transition-all"
                                            x-text="formatRupiahShort(amount)"></button>
                                </template>
                                <button @click="paidAmount = totalAmount" type="button"
                                        class="px-2.5 py-1 bg-brand-100 hover:bg-brand-200 text-brand-800 text-[10px] font-bold rounded-md transition-all">Uang Pas</button>
                            </div>
                        </div>
                    </template>

                    <input type="text" x-model="customerName" placeholder="Nama pelanggan (opsional)"
                           class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/30">

                    <!-- Checkout -->
                    <form action="{{ route('kasir.checkout') }}" method="POST" x-ref="checkoutForm">
                        @csrf
                        <input type="hidden" name="items" :value="JSON.stringify(cart)">
                        <input type="hidden" name="payment_method" :value="paymentMethod">
                        <input type="hidden" name="paid_amount" :value="paymentMethod === 'Tunai' ? paidAmount : totalAmount">
                        <input type="hidden" name="customer_name" :value="customerName">
                        <button type="submit" :disabled="!canCheckout"
                                :class="canCheckout ? 'bg-brand-dark hover:bg-brand-800 shadow-lg shadow-brand-500/20' : 'bg-slate-300 cursor-not-allowed'"
                                class="w-full text-white font-bold text-sm py-3 rounded-xl flex items-center justify-center gap-2 transition-all">
                            <i data-lucide="receipt" class="w-5 h-5"></i>
                            <span>Bayar &amp; Cetak Struk</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function kasirApp() {
    return {
        products: @json($products),
        filteredProducts: [],
        searchQuery: '',
        selectedCategory: 'all',
        cart: [],
        paymentMethod: 'Tunai',
        paidAmount: 0,
        customerName: '',
        currentTime: '',

        init() {
            this.filteredProducts = [...this.products];
            this.updateTime();
            setInterval(() => this.updateTime(), 1000);
        },

        updateTime() {
            const now = new Date();
            const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
            this.currentTime = days[now.getDay()] + ', ' + now.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
        },

        filterProducts() {
            let result = [...this.products];
            if (this.selectedCategory !== 'all') {
                result = result.filter(p => p.category === this.selectedCategory);
            }
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                result = result.filter(p =>
                    p.name.toLowerCase().includes(q) ||
                    p.sku.toLowerCase().includes(q) ||
                    (p.barcode && p.barcode.toLowerCase().includes(q))
                );
            }
            this.filteredProducts = result;
        },

        addToCart(product) {
            // Default: add as piece (satuan)
            const existing = this.cart.find(item => item.product_id === product.id && item.selected_unit === 'piece');
            if (existing) {
                existing.quantity++;
            } else {
                this.cart.push({
                    product_id: product.id,
                    name: product.name,
                    sku: product.sku,
                    selected_unit: 'piece',
                    unit_name_piece: product.unit,
                    selling_price: parseFloat(product.selling_price),
                    pack_name: product.pack_name || 'Pack',
                    pack_price: parseFloat(product.pack_price) || 0,
                    pack_qty: parseInt(product.pack_qty) || 0,
                    box_name: product.box_name || 'Dus',
                    box_price: parseFloat(product.box_price) || 0,
                    box_qty: parseInt(product.box_qty) || 0,
                    stock: product.stock,
                    quantity: 1
                });
            }
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
        },

        changeUnit(index, unit) {
            this.cart[index].selected_unit = unit;
            this.cart[index].quantity = 1;
        },

        updateQty(index, qty) {
            if (qty < 1) {
                this.removeFromCart(index);
                return;
            }
            this.cart[index].quantity = qty;
        },

        clearCart() {
            this.cart = [];
            this.paidAmount = 0;
        },

        getItemPrice(item) {
            switch (item.selected_unit) {
                case 'pack': return item.pack_price;
                case 'box': return item.box_price;
                default: return item.selling_price;
            }
        },

        getUnitLabel(item) {
            switch (item.selected_unit) {
                case 'pack': return item.pack_name;
                case 'box': return item.box_name;
                default: return item.unit_name_piece;
            }
        },

        getItemSubtotal(item) {
            return this.getItemPrice(item) * item.quantity;
        },

        get totalAmount() {
            return this.cart.reduce((sum, item) => sum + this.getItemSubtotal(item), 0);
        },

        get totalItems() {
            return this.cart.reduce((sum, item) => sum + item.quantity, 0);
        },

        get canCheckout() {
            if (this.cart.length === 0) return false;
            if (this.paymentMethod === 'Tunai') {
                return this.paidAmount >= this.totalAmount;
            }
            return true;
        },

        get quickAmounts() {
            const t = this.totalAmount;
            if (t <= 0) return [];
            const amounts = [];
            const roundUp = [1000, 5000, 10000, 20000, 50000, 100000];
            for (const r of roundUp) {
                const rounded = Math.ceil(t / r) * r;
                if (rounded >= t && !amounts.includes(rounded)) amounts.push(rounded);
                if (amounts.length >= 4) break;
            }
            return [...new Set(amounts)].sort((a,b) => a-b).slice(0, 4);
        },

        formatRupiah(val) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
        },

        formatRupiahShort(val) {
            if (val >= 1000000) return 'Rp ' + (val/1000000).toFixed(val % 1000000 === 0 ? 0 : 1) + ' Jt';
            if (val >= 1000) return 'Rp ' + (val/1000).toFixed(0) + 'rb';
            return 'Rp ' + val;
        }
    }
}
</script>
@endpush
@endsection
