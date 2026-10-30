<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\GeminiInvoiceScannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $query = StockMovement::with('product')->latest();

        if ($filter === 'in') {
            $query->where('type', 'in');
        } elseif ($filter === 'out') {
            $query->whereIn('type', ['out', 'damaged', 'return']);
        }

        $movements = $query->take(20)->get();

        // Calculate Daily Summary Metrics
        $todayIn = StockMovement::where('type', 'in')
            ->whereDate('created_at', Carbon::today())
            ->sum('quantity');

        $todayOut = StockMovement::whereIn('type', ['out'])
            ->whereDate('created_at', Carbon::today())
            ->sum('quantity');

        $todayDamaged = StockMovement::whereIn('type', ['damaged', 'return'])
            ->whereDate('created_at', Carbon::today())
            ->count();

        $allProducts = Product::orderBy('name')->get();

        return view('stok.index', compact(
            'movements',
            'filter',
            'todayIn',
            'todayOut',
            'todayDamaged',
            'allProducts'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,damaged,return',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string',
            'source_category' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Update product stock
        if (in_array($request->type, ['in'])) {
            $product->stock += $request->quantity;
        } else {
            $product->stock = max(0, $product->stock - $request->quantity);
        }
        $product->save();

        $validated['user_name'] = 'Budi Santoso';

        StockMovement::create($validated);

        return redirect()->route('stok.index')->with('success', 'Catatan stok berhasil disimpan!');
    }

    /**
     * Scan an invoice image using Gemini Vision AI and return extracted data.
     */
    public function scanInvoice(Request $request)
    {
        $request->validate([
            'invoice_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:10240',
        ], [
            'invoice_image.required' => 'Silakan pilih atau foto faktur terlebih dahulu.',
            'invoice_image.image' => 'File harus berupa gambar (JPG, PNG, WebP).',
            'invoice_image.mimes' => 'Format gambar harus JPG, PNG, atau WebP.',
            'invoice_image.max' => 'Ukuran gambar maksimal 10MB.',
        ]);

        $scanner = new GeminiInvoiceScannerService();
        $result = $scanner->scan($request->file('invoice_image'));

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], 422);
        }

        // Enrich items with full product data for frontend display
        $data = $result['data'];
        if (!empty($data['items'])) {
            $productIds = collect($data['items'])
                ->pluck('matched_product_id')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($data['items'] as &$item) {
                if (!empty($item['matched_product_id']) && $products->has($item['matched_product_id'])) {
                    $product = $products[$item['matched_product_id']];
                    $item['matched_product_sku'] = $product->sku;
                    $item['matched_product_stock'] = $product->stock;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Batch store multiple stock movements from a scanned invoice.
     */
    public function batchStore(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'invoice_number' => 'nullable|string|max:100',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string',
        ], [
            'items.required' => 'Minimal harus ada 1 barang untuk disimpan.',
            'items.min' => 'Minimal harus ada 1 barang untuk disimpan.',
            'items.*.product_id.required' => 'Produk harus dipilih untuk setiap baris.',
            'items.*.product_id.exists' => 'Produk yang dipilih tidak ditemukan di database.',
            'items.*.quantity.required' => 'Kuantitas wajib diisi.',
            'items.*.quantity.min' => 'Kuantitas minimal 1.',
        ]);

        $supplierName = $request->input('supplier_name');
        $invoiceNumber = $request->input('invoice_number', '');
        $items = $request->input('items');
        $savedCount = 0;

        DB::beginTransaction();

        try {
            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Update product stock (always 'in' for invoice scan)
                $product->stock += $item['quantity'];
                $product->save();

                // Build notes from invoice info
                $notes = trim("Faktur: {$invoiceNumber}" . (!empty($item['notes']) ? " • {$item['notes']}" : ''));

                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'type' => 'in',
                    'quantity' => $item['quantity'],
                    'unit' => $item['unit'],
                    'source_category' => 'Supplier: ' . $supplierName,
                    'notes' => $notes ?: null,
                    'user_name' => 'Budi Santoso',
                ]);

                $savedCount++;
            }

            DB::commit();

            return redirect()->route('stok.index')->with('success',
                "✅ Faktur berhasil diproses! {$savedCount} barang dari {$supplierName} telah ditambahkan ke stok."
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('stok.index')->with('error',
                'Gagal menyimpan data faktur: ' . $e->getMessage()
            );
        }
    }
}
