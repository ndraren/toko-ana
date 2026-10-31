<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\PriceTier;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $status = $request->get('status', 'all');
        $tab = $request->get('tab', 'data-barang'); // 'data-barang' or 'stok-masuk'

        $query = Product::with('units.priceTier');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($category && $category !== 'Semua Kategori') {
            $query->where('category', $category);
        }

        if ($status === 'aman') {
            $query->whereRaw('stock > min_stock');
        } elseif ($status === 'menipis') {
            $query->whereRaw('stock > 0 AND stock <= min_stock');
        } elseif ($status === 'habis') {
            $query->where('stock', 0);
        }

        $products = $query->orderBy('name')->paginate(10);

        // Counts for status pills
        $totalCount = Product::count();
        $amanCount = Product::whereRaw('stock > min_stock')->count();
        $menipisCount = Product::whereRaw('stock > 0 AND stock <= min_stock')->count();
        $habisCount = Product::where('stock', 0)->count();
        $criticalCount = $menipisCount + $habisCount;

        $categories = Product::select('category')->distinct()->pluck('category');

        // Stok Masuk tab data (Segmen 6: merge Stok Masuk into this page)
        $movements = collect();
        $filter = $request->get('filter', 'all');
        $todayIn = 0;
        $todayOut = 0;
        $todayDamaged = 0;
        $allProducts = collect();

        if ($tab === 'stok-masuk') {
            $smQuery = StockMovement::with('product')->latest();
            if ($filter === 'in') {
                $smQuery->where('type', 'in');
            } elseif ($filter === 'out') {
                $smQuery->whereIn('type', ['out', 'damaged', 'return']);
            }
            $movements = $smQuery->take(20)->get();

            $todayIn = StockMovement::where('type', 'in')
                ->whereDate('created_at', Carbon::today())
                ->sum('quantity');
            $todayOut = StockMovement::whereIn('type', ['out'])
                ->whereDate('created_at', Carbon::today())
                ->sum('quantity');
            $todayDamaged = StockMovement::whereIn('type', ['damaged', 'return'])
                ->whereDate('created_at', Carbon::today())
                ->count();
        }

        // Always need allProducts for stok masuk form
        $allProducts = Product::orderBy('name')->get();

        // Expiry count for badge
        $nearExpiryCount = Product::whereNotNull('expiry_date')
            ->where('expiry_date', '<=', Carbon::now()->addDays(30))
            ->count();

        return view('products.index', compact(
            'products',
            'search',
            'category',
            'status',
            'totalCount',
            'amanCount',
            'menipisCount',
            'habisCount',
            'criticalCount',
            'categories',
            'tab',
            'movements',
            'filter',
            'todayIn',
            'todayOut',
            'todayDamaged',
            'allProducts',
            'nearExpiryCount'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku'      => 'required|string|unique:products,sku',
            'barcode'  => 'nullable|string',
            'name'     => 'required|string',
            'category' => 'required|string',
            'stock'    => 'required|integer|min:0',
            'min_stock'=> 'required|integer|min:0',
            'unit'     => 'required|string',
            'cost_price'    => 'required|numeric|min:0',
            'expiry_date'   => 'nullable|date',
            'supplier_name' => 'nullable|string',
            // Dynamic units array
            'units'                    => 'required|array|min:1',
            'units.*.unit_name'        => 'required|string',
            'units.*.conversion_factor'=> 'required|integer|min:1',
            'units.*.price_retail'     => 'required|numeric|min:0',
            'units.*.price_wholesale'  => 'nullable|numeric|min:0',
            'units.*.min_wholesale_qty'=> 'nullable|integer|min:1',
        ]);

        $product = Product::create([
            'sku'           => $validated['sku'],
            'barcode'       => $validated['barcode'] ?? null,
            'name'          => $validated['name'],
            'category'      => $validated['category'],
            'stock'         => $validated['stock'],
            'min_stock'     => $validated['min_stock'],
            'expiry_date'   => $validated['expiry_date'] ?? null,
            'unit'          => $validated['unit'],
            'cost_price'    => $validated['cost_price'],
            'supplier_name' => $validated['supplier_name'] ?? null,
        ]);

        // Create units + price tiers
        foreach ($validated['units'] as $index => $unitData) {
            $unit = $product->units()->create([
                'unit_name'         => $unitData['unit_name'],
                'conversion_factor' => $unitData['conversion_factor'],
                'sort_order'        => $index,
            ]);

            $unit->priceTier()->create([
                'price_retail'      => $unitData['price_retail'],
                'price_wholesale'   => $unitData['price_wholesale'] ?? 0,
                'min_wholesale_qty' => $unitData['min_wholesale_qty'] ?? 1,
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku'      => 'required|string|unique:products,sku,' . $product->id,
            'barcode'  => 'nullable|string',
            'name'     => 'required|string',
            'category' => 'required|string',
            'stock'    => 'required|integer|min:0',
            'min_stock'=> 'required|integer|min:0',
            'unit'     => 'required|string',
            'cost_price'    => 'required|numeric|min:0',
            'expiry_date'   => 'nullable|date',
            'supplier_name' => 'nullable|string',
            // Dynamic units array
            'units'                    => 'required|array|min:1',
            'units.*.id'               => 'nullable|integer',
            'units.*.unit_name'        => 'required|string',
            'units.*.conversion_factor'=> 'required|integer|min:1',
            'units.*.price_retail'     => 'required|numeric|min:0',
            'units.*.price_wholesale'  => 'nullable|numeric|min:0',
            'units.*.min_wholesale_qty'=> 'nullable|integer|min:1',
        ]);

        $product->update([
            'sku'           => $validated['sku'],
            'barcode'       => $validated['barcode'] ?? null,
            'name'          => $validated['name'],
            'category'      => $validated['category'],
            'stock'         => $validated['stock'],
            'min_stock'     => $validated['min_stock'],
            'expiry_date'   => $validated['expiry_date'] ?? null,
            'unit'          => $validated['unit'],
            'cost_price'    => $validated['cost_price'],
            'supplier_name' => $validated['supplier_name'] ?? null,
        ]);

        // Sync units: collect IDs that should remain
        $keepIds = [];

        foreach ($validated['units'] as $index => $unitData) {
            if (!empty($unitData['id'])) {
                // Update existing unit
                $unit = ProductUnit::find($unitData['id']);
                if ($unit && (int) $unit->product_id === (int) $product->id) {
                    $unit->update([
                        'unit_name'         => $unitData['unit_name'],
                        'conversion_factor' => $unitData['conversion_factor'],
                        'sort_order'        => $index,
                    ]);
                    $unit->priceTier()->updateOrCreate([], [
                        'price_retail'      => $unitData['price_retail'],
                        'price_wholesale'   => $unitData['price_wholesale'] ?? 0,
                        'min_wholesale_qty' => $unitData['min_wholesale_qty'] ?? 1,
                    ]);
                    $keepIds[] = $unit->id;
                }
            } else {
                // Create new unit
                $unit = $product->units()->create([
                    'unit_name'         => $unitData['unit_name'],
                    'conversion_factor' => $unitData['conversion_factor'],
                    'sort_order'        => $index,
                ]);
                $unit->priceTier()->create([
                    'price_retail'      => $unitData['price_retail'],
                    'price_wholesale'   => $unitData['price_wholesale'] ?? 0,
                    'min_wholesale_qty' => $unitData['min_wholesale_qty'] ?? 1,
                ]);
                $keepIds[] = $unit->id;
            }
        }

        // Delete units that were removed by user
        $product->units()->whereNotIn('id', $keepIds)->delete();

        return redirect()->route('products.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Barang berhasil dihapus.');
    }
}
