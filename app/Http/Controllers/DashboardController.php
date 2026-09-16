<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $timeFilter = $request->get('period', 'today');

        // Metric Calculations
        $totalStockUnits = Product::sum('stock');
        
        $inventoryValue = Product::selectRaw('SUM(stock * cost_price) as total_val')->value('total_val') ?? 0;
        
        $criticalProductsCount = Product::whereRaw('stock <= min_stock')->count();
        
        $todaySalesValue = 4120000; // Rp 4,12 Jt as per mockup design
        $todayTransactionsCount = 142;

        // Barang Prioritas Restock (stok <= min_stock)
        $restockProducts = Product::whereRaw('stock <= min_stock')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        $totalCriticalCount = $criticalProductsCount;

        // Aktivitas Terakhir (Timeline pergerakan stok shift pagi)
        $recentMovements = StockMovement::with('product')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'timeFilter',
            'totalStockUnits',
            'inventoryValue',
            'criticalProductsCount',
            'todaySalesValue',
            'todayTransactionsCount',
            'restockProducts',
            'totalCriticalCount',
            'recentMovements'
        ));
    }
}
