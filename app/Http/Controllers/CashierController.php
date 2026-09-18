<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashierController extends Controller
{
    public function index()
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();
        $categories = Product::select('category')->distinct()->pluck('category');

        return view('kasir.index', compact('products', 'categories'));
    }

    public function searchProduct(Request $request)
    {
        $search = $request->get('q', '');
        $products = Product::where('stock', '>', 0)
            ->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->take(20)
            ->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|string',
            'payment_method' => 'required|string|in:Tunai,QRIS,Transfer,Debit',
            'paid_amount' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $cartItems = json_decode($request->items, true);

        if (empty($cartItems)) {
            return back()->with('error', 'Keranjang belanja kosong.');
        }

        // Generate invoice number
        $todayCount = Transaction::whereDate('created_at', Carbon::today())->count() + 1;
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($todayCount, 4, '0', STR_PAD_LEFT);

        // Calculate total
        $totalAmount = 0;
        $itemsToCreate = [];

        foreach ($cartItems as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) continue;

            $qty = (int) $item['quantity'];
            $selectedUnit = $item['selected_unit'] ?? 'piece';

            // Determine price & unit name based on selected tier
            switch ($selectedUnit) {
                case 'pack':
                    $unitPrice = $product->pack_price;
                    $unitName = $product->pack_name ?? 'Pack';
                    $stockDeduct = $qty * ($product->pack_qty ?: 1);
                    break;
                case 'box':
                    $unitPrice = $product->box_price;
                    $unitName = $product->box_name ?? 'Dus';
                    $stockDeduct = $qty * ($product->box_qty ?: 1);
                    break;
                default: // piece
                    $unitPrice = $product->selling_price;
                    $unitName = $product->unit;
                    $stockDeduct = $qty;
                    break;
            }

            $subtotal = $unitPrice * $qty;
            $totalAmount += $subtotal;

            $itemsToCreate[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $qty,
                'unit' => $unitName,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ];

            // Reduce stock (always in base unit / satuan)
            $product->stock = max(0, $product->stock - $stockDeduct);
            $product->save();

            // Record stock movement as sale
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'sale',
                'quantity' => $stockDeduct,
                'unit' => $product->unit,
                'source_category' => 'Kasir POS 1 - Penjualan',
                'notes' => 'Struk ' . $invoiceNumber . ' (' . $qty . ' ' . $unitName . ')',
                'user_name' => 'Budi Santoso',
            ]);
        }

        $paidAmount = (float) $request->paid_amount;
        $changeAmount = max(0, $paidAmount - $totalAmount);

        // Create transaction
        $transaction = Transaction::create([
            'invoice_number' => $invoiceNumber,
            'payment_method' => $request->payment_method,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'change_amount' => $changeAmount,
            'cashier_name' => 'Budi Santoso',
            'customer_name' => $request->customer_name,
            'status' => 'success',
            'notes' => $request->notes,
        ]);

        // Create transaction items
        foreach ($itemsToCreate as $itemData) {
            $transaction->items()->create($itemData);
        }

        return redirect()->route('kasir.receipt', $transaction->id);
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load('items.product');
        return view('kasir.receipt', compact('transaction'));
    }
}
