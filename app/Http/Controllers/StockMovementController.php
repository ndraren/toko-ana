<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
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
}
