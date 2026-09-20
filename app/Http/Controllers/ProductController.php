<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');
        $status = $request->get('status', 'all');

        $query = Product::query();

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
            'categories'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:products,sku',
            'barcode' => 'nullable|string',
            'name' => 'required|string',
            'category' => 'required|string',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'wholesale_min_qty' => 'nullable|integer|min:0',
            'pack_name' => 'nullable|string',
            'pack_price' => 'nullable|numeric|min:0',
            'pack_qty' => 'nullable|integer|min:0',
            'box_name' => 'nullable|string',
            'box_price' => 'nullable|numeric|min:0',
            'box_qty' => 'nullable|integer|min:0',
            'supplier_name' => 'nullable|string',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'required|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string',
            'name' => 'required|string',
            'category' => 'required|string',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'wholesale_min_qty' => 'nullable|integer|min:0',
            'pack_name' => 'nullable|string',
            'pack_price' => 'nullable|numeric|min:0',
            'pack_qty' => 'nullable|integer|min:0',
            'box_name' => 'nullable|string',
            'box_price' => 'nullable|numeric|min:0',
            'box_qty' => 'nullable|integer|min:0',
            'supplier_name' => 'nullable|string',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Barang berhasil dihapus.');
    }
}
