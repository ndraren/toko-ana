<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\PurchaseOrder;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Truncate tables
        Product::query()->delete();
        StockMovement::query()->delete();
        PurchaseOrder::query()->delete();

        $productsData = [
            [
                'sku' => 'BRG-001',
                'barcode' => '089686010015',
                'name' => 'Indomie Mi Goreng 85g',
                'category' => 'Makanan',
                'stock' => 120,
                'min_stock' => 20,
                'unit' => 'Bungkus',
                'cost_price' => 2750,
                'selling_price' => 3500,
                'supplier_name' => 'PT Indomarco Adi Prima',
            ],
            [
                'sku' => 'BRG-014',
                'barcode' => '899277511201',
                'name' => 'Bimoli Minyak Goreng Pouch 2L',
                'category' => 'Sembako',
                'stock' => 3,
                'min_stock' => 12,
                'unit' => 'Pouch',
                'cost_price' => 33200,
                'selling_price' => 36500,
                'supplier_name' => 'PT Salim Jaya',
            ],
            [
                'sku' => 'BRG-082',
                'barcode' => '899307711019',
                'name' => 'Teh Pucuk Harum 350ml',
                'category' => 'Minuman',
                'stock' => 4,
                'min_stock' => 24,
                'unit' => 'Botol',
                'cost_price' => 2900,
                'selling_price' => 3500,
                'supplier_name' => 'Mayora Direct',
            ],
            [
                'sku' => 'BRG-105',
                'barcode' => '899269920108',
                'name' => 'Gulaku Premium Kuning 1Kg',
                'category' => 'Sembako',
                'stock' => 0,
                'min_stock' => 15,
                'unit' => 'Kg',
                'cost_price' => 16000,
                'selling_price' => 18000,
                'supplier_name' => 'Agen Beras Sentosa',
            ],
            [
                'sku' => 'BRG-045',
                'barcode' => '899999901502',
                'name' => 'Royco Bumbu Kaldu Ayam 230g',
                'category' => 'Bumbu',
                'stock' => 45,
                'min_stock' => 10,
                'unit' => 'Pouch',
                'cost_price' => 9200,
                'selling_price' => 10500,
                'supplier_name' => 'PT Unilever Indonesia',
            ],
            [
                'sku' => 'BRG-090',
                'barcode' => '899100110332',
                'name' => 'Kapal Api Special Mix 10 x 24g',
                'category' => 'Minuman',
                'stock' => 4,
                'min_stock' => 10,
                'unit' => 'Renceng',
                'cost_price' => 12800,
                'selling_price' => 15000,
                'supplier_name' => 'Distributor Santos',
            ],
            [
                'sku' => 'AQU-0600',
                'barcode' => '899100220110',
                'name' => 'Aqua Botol 600ml',
                'category' => 'Minuman',
                'stock' => 8,
                'min_stock' => 48,
                'unit' => 'Botol',
                'cost_price' => 2500,
                'selling_price' => 3500,
                'supplier_name' => 'Tirta Investama',
            ],
            [
                'sku' => 'AQU-19L',
                'barcode' => '899100220999',
                'name' => 'Aqua Galon 19L',
                'category' => 'Minuman',
                'stock' => 18,
                'min_stock' => 10,
                'unit' => 'Galon',
                'cost_price' => 16000,
                'selling_price' => 20000,
                'supplier_name' => 'Tirta Investama',
            ],
            [
                'sku' => 'SAM-SLP',
                'barcode' => '899288810001',
                'name' => 'Rokok Sampoerna Mild',
                'category' => 'Lainnya',
                'stock' => 25,
                'min_stock' => 5,
                'unit' => 'Slop',
                'cost_price' => 280000,
                'selling_price' => 295000,
                'supplier_name' => 'PT HM Sampoerna',
            ],
            [
                'sku' => 'SOS-TEH-200ML',
                'barcode' => '899277700112',
                'name' => 'Teh Botol Sosro Kotak',
                'category' => 'Minuman',
                'stock' => 30,
                'min_stock' => 12,
                'unit' => 'Dus',
                'cost_price' => 45000,
                'selling_price' => 52000,
                'supplier_name' => 'Retur Supplier',
            ],
            [
                'sku' => 'ULT-CKL-250ML',
                'barcode' => '899300110055',
                'name' => 'Susu UHT Ultra Milk Cokelat',
                'category' => 'Minuman',
                'stock' => 28,
                'min_stock' => 10,
                'unit' => 'Kotak',
                'cost_price' => 5500,
                'selling_price' => 6500,
                'supplier_name' => 'PT Ultrajaya',
            ],
            [
                'sku' => 'TLR-AYM-1KG',
                'barcode' => '899555110099',
                'name' => 'Telur Ayam Negeri',
                'category' => 'Sembako',
                'stock' => 42,
                'min_stock' => 15,
                'unit' => 'Kg',
                'cost_price' => 24000,
                'selling_price' => 27000,
                'supplier_name' => 'Peternak Lokal',
            ]
        ];

        $createdProducts = [];
        foreach ($productsData as $data) {
            $createdProducts[$data['sku']] = Product::create($data);
        }

        // Seed stock movements (timeline activities)
        $now = Carbon::now();

        $movements = [
            [
                'product_sku' => 'BRG-001',
                'type' => 'in',
                'quantity' => 20,
                'unit' => 'Dus',
                'source_category' => 'Supplier • Indomarco',
                'notes' => 'Dus isi 40 bks',
                'user_name' => 'Budi Santoso',
                'created_at' => $now->copy()->subMinutes(12),
            ],
            [
                'product_sku' => 'BRG-105',
                'type' => 'in',
                'quantity' => 50,
                'unit' => 'Kg',
                'source_category' => 'Grosir Pasar Induk',
                'notes' => 'Kemasan 1 Kg',
                'user_name' => 'Budi Santoso',
                'created_at' => $now->copy()->subMinutes(55),
            ],
            [
                'product_sku' => 'ULT-CKL-250ML',
                'type' => 'damaged',
                'quantity' => 2,
                'unit' => 'Kotak',
                'source_category' => 'Barang Rusak / Pecah',
                'notes' => 'Bocor saat bongkar muat',
                'user_name' => 'Siti Rahma',
                'created_at' => $now->copy()->subMinutes(88),
            ],
            [
                'product_sku' => 'BRG-090',
                'type' => 'in',
                'quantity' => 15,
                'unit' => 'Renceng',
                'source_category' => 'Agen Wings Surya',
                'notes' => 'Renceng isi 10 sachet',
                'user_name' => 'Budi Santoso',
                'created_at' => $now->copy()->subHours(2)->subMinutes(10),
            ],
            [
                'product_sku' => 'SOS-TEH-200ML',
                'type' => 'return',
                'quantity' => 1,
                'unit' => 'Dus',
                'source_category' => 'Retur Supplier',
                'notes' => 'Kemasan penyok ditolak pembeli',
                'user_name' => 'Budi Santoso',
                'created_at' => $now->copy()->subHours(2)->subMinutes(45),
            ],
            [
                'product_sku' => 'BRG-014',
                'type' => 'out',
                'quantity' => 3,
                'unit' => 'Pouch',
                'source_category' => 'Kasir POS 1 • Struk #00142',
                'notes' => 'Penjualan Tunai',
                'user_name' => 'Kasir POS 1',
                'created_at' => $now->copy()->subMinutes(30),
            ],
            [
                'product_sku' => 'TLR-AYM-1KG',
                'type' => 'damaged',
                'quantity' => 1,
                'unit' => 'Kg',
                'source_category' => 'BAP Retur Toko',
                'notes' => 'Koreksi: Pecah saat penataan rak',
                'user_name' => 'Budi Santoso',
                'created_at' => $now->copy()->subMinutes(45),
            ],
            [
                'product_sku' => 'AQU-19L',
                'type' => 'out',
                'quantity' => 4,
                'unit' => 'Galon',
                'source_category' => 'Kasir POS 1 • Langganan Wartek',
                'notes' => 'QRIS BCA',
                'user_name' => 'Kasir POS 1',
                'created_at' => $now->copy()->subHours(1)->subMinutes(30),
            ],
            [
                'product_sku' => 'SAM-SLP',
                'type' => 'in',
                'quantity' => 10,
                'unit' => 'Slop',
                'source_category' => 'Sales Kanvas • PT HM Sampoerna',
                'notes' => 'Pembelian langsung sales',
                'user_name' => 'Budi Santoso',
                'created_at' => $now->copy()->subHours(2)->subMinutes(15),
            ],
        ];

        foreach ($movements as $m) {
            $product = $createdProducts[$m['product_sku']] ?? null;
            if ($product) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => $m['type'],
                    'quantity' => $m['quantity'],
                    'unit' => $m['unit'],
                    'source_category' => $m['source_category'],
                    'notes' => $m['notes'],
                    'user_name' => $m['user_name'],
                    'created_at' => $m['created_at'],
                ]);
            }
        }

        // === Seed Sample Transactions ===
        Transaction::query()->delete();
        TransactionItem::query()->delete();

        $transactionsData = [
            [
                'invoice_number' => 'STR-20241024-001',
                'payment_method' => 'Tunai',
                'total_amount' => 45500,
                'paid_amount' => 50000,
                'change_amount' => 4500,
                'cashier_name' => 'Budi Santoso',
                'customer_name' => null,
                'status' => 'success',
                'notes' => null,
                'created_at' => $now->copy()->subMinutes(15),
                'items' => [
                    ['sku' => 'BRG-001', 'qty' => 5, 'price' => 3500],
                    ['sku' => 'BRG-082', 'qty' => 3, 'price' => 3500],
                    ['sku' => 'BRG-045', 'qty' => 1, 'price' => 10500],
                ],
            ],
            [
                'invoice_number' => 'STR-20241024-002',
                'payment_method' => 'QRIS',
                'total_amount' => 73000,
                'paid_amount' => 73000,
                'change_amount' => 0,
                'cashier_name' => 'Budi Santoso',
                'customer_name' => 'Ibu Sari',
                'status' => 'success',
                'notes' => 'QRIS BCA',
                'created_at' => $now->copy()->subMinutes(35),
                'items' => [
                    ['sku' => 'BRG-014', 'qty' => 1, 'price' => 36500],
                    ['sku' => 'BRG-105', 'qty' => 2, 'price' => 18000],
                ],
            ],
            [
                'invoice_number' => 'STR-20241024-003',
                'payment_method' => 'Tunai',
                'total_amount' => 80000,
                'paid_amount' => 100000,
                'change_amount' => 20000,
                'cashier_name' => 'Siti Rahma',
                'customer_name' => null,
                'status' => 'success',
                'notes' => null,
                'created_at' => $now->copy()->subMinutes(52),
                'items' => [
                    ['sku' => 'AQU-19L', 'qty' => 4, 'price' => 20000],
                ],
            ],
            [
                'invoice_number' => 'STR-20241024-004',
                'payment_method' => 'Transfer',
                'total_amount' => 590000,
                'paid_amount' => 590000,
                'change_amount' => 0,
                'cashier_name' => 'Budi Santoso',
                'customer_name' => 'Warung Pak Tejo',
                'status' => 'success',
                'notes' => 'Transfer BRI - Langganan mingguan',
                'created_at' => $now->copy()->subMinutes(70),
                'items' => [
                    ['sku' => 'SAM-SLP', 'qty' => 2, 'price' => 295000],
                ],
            ],
            [
                'invoice_number' => 'STR-20241024-005',
                'payment_method' => 'Tunai',
                'total_amount' => 24500,
                'paid_amount' => 25000,
                'change_amount' => 500,
                'cashier_name' => 'Budi Santoso',
                'customer_name' => null,
                'status' => 'refunded',
                'notes' => 'Refund: Produk salah ambil',
                'created_at' => $now->copy()->subMinutes(90),
                'items' => [
                    ['sku' => 'BRG-001', 'qty' => 3, 'price' => 3500],
                    ['sku' => 'BRG-090', 'qty' => 1, 'price' => 15000],
                ],
            ],
            [
                'invoice_number' => 'STR-20241024-006',
                'payment_method' => 'QRIS',
                'total_amount' => 42000,
                'paid_amount' => 42000,
                'change_amount' => 0,
                'cashier_name' => 'Siti Rahma',
                'customer_name' => 'Mas Adi',
                'status' => 'success',
                'notes' => 'QRIS Mandiri',
                'created_at' => $now->copy()->subMinutes(110),
                'items' => [
                    ['sku' => 'BRG-001', 'qty' => 2, 'price' => 3500],
                    ['sku' => 'AQU-0600', 'qty' => 2, 'price' => 3500],
                    ['sku' => 'BRG-082', 'qty' => 2, 'price' => 3500],
                    ['sku' => 'TLR-AYM-1KG', 'qty' => 1, 'price' => 27000],
                ],
            ],
            [
                'invoice_number' => 'STR-20241024-007',
                'payment_method' => 'Debit',
                'total_amount' => 156500,
                'paid_amount' => 156500,
                'change_amount' => 0,
                'cashier_name' => 'Budi Santoso',
                'customer_name' => 'Bu Diana',
                'status' => 'success',
                'notes' => 'Debit BNI',
                'created_at' => $now->copy()->subHours(2),
                'items' => [
                    ['sku' => 'BRG-014', 'qty' => 2, 'price' => 36500],
                    ['sku' => 'BRG-105', 'qty' => 3, 'price' => 18000],
                    ['sku' => 'BRG-001', 'qty' => 8, 'price' => 3500],
                ],
            ],
            [
                'invoice_number' => 'STR-20241024-008',
                'payment_method' => 'Tunai',
                'total_amount' => 7000,
                'paid_amount' => 10000,
                'change_amount' => 3000,
                'cashier_name' => 'Budi Santoso',
                'customer_name' => null,
                'status' => 'void',
                'notes' => 'Dibatalkan: Pelanggan berubah pikiran',
                'created_at' => $now->copy()->subHours(2)->subMinutes(20),
                'items' => [
                    ['sku' => 'BRG-082', 'qty' => 2, 'price' => 3500],
                ],
            ],
            [
                'invoice_number' => 'STR-20241023-042',
                'payment_method' => 'Tunai',
                'total_amount' => 31500,
                'paid_amount' => 50000,
                'change_amount' => 18500,
                'cashier_name' => 'Siti Rahma',
                'customer_name' => null,
                'status' => 'success',
                'notes' => null,
                'created_at' => $now->copy()->subDay()->subHours(3),
                'items' => [
                    ['sku' => 'BRG-001', 'qty' => 3, 'price' => 3500],
                    ['sku' => 'AQU-0600', 'qty' => 2, 'price' => 3500],
                    ['sku' => 'BRG-090', 'qty' => 1, 'price' => 15000],
                ],
            ],
            [
                'invoice_number' => 'STR-20241023-041',
                'payment_method' => 'QRIS',
                'total_amount' => 54000,
                'paid_amount' => 54000,
                'change_amount' => 0,
                'cashier_name' => 'Budi Santoso',
                'customer_name' => 'Pak Hendra',
                'status' => 'success',
                'notes' => 'QRIS BCA',
                'created_at' => $now->copy()->subDay()->subHours(5),
                'items' => [
                    ['sku' => 'TLR-AYM-1KG', 'qty' => 2, 'price' => 27000],
                ],
            ],
        ];

        foreach ($transactionsData as $td) {
            $items = $td['items'];
            unset($td['items']);

            $transaction = Transaction::create($td);

            foreach ($items as $itemData) {
                $product = $createdProducts[$itemData['sku']] ?? null;
                if ($product) {
                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $itemData['qty'],
                        'unit' => $product->unit,
                        'unit_price' => $itemData['price'],
                        'subtotal' => $itemData['qty'] * $itemData['price'],
                    ]);
                }
            }
        }
    }
}
