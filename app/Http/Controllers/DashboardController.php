<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $timeFilter = $request->get('period', 'today');

        // Metric Calculations
        $totalStockUnits = Product::sum('stock');
        
        $inventoryValue = Product::selectRaw('SUM(stock * cost_price) as total_val')->value('total_val') ?? 0;
        
        $criticalProductsCount = Product::whereRaw('stock <= min_stock')->count();
        
        // Real sales data from transactions
        $todaySalesValue = Transaction::where('status', 'success')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');
        $todayTransactionsCount = Transaction::whereDate('created_at', Carbon::today())->count();

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
