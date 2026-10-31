<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $tab = $request->get('tab', 'semua'); // 'semua', 'pemasukan', 'pengeluaran', 'laporan'
        $period = $request->get('period', 'today'); // 'today', 'week', 'month', 'all', 'custom'
        $status = $request->get('status', 'all');
        $payment = $request->get('payment');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Determine date range based on period
        $startDate = null;
        $endDate = null;

        if ($period === 'today') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($period === 'week') {
            $startDate = Carbon::now()->startOfWeek()->startOfDay();
            $endDate = Carbon::now()->endOfWeek()->endOfDay();
        } elseif ($period === 'month') {
            $startDate = Carbon::now()->startOfMonth()->startOfDay();
            $endDate = Carbon::now()->endOfMonth()->endOfDay();
        } elseif ($period === 'custom') {
            if ($dateFrom) $startDate = Carbon::parse($dateFrom)->startOfDay();
            if ($dateTo) $endDate = Carbon::parse($dateTo)->endOfDay();
        }

        // 1. Calculate Period Totals for Balance Cards
        $incomeQuery = Transaction::where('status', 'success');
        if ($startDate && $endDate) {
            $incomeQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $periodIncome = (float) $incomeQuery->sum('total_amount');
        $periodIncomeCount = $incomeQuery->count();

        // Expenses = StockMovements where type = 'in' (Pembelian Suplier)
        $expenseQuery = StockMovement::where('type', 'in')->with('product');
        if ($startDate && $endDate) {
            $expenseQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $expenseMovements = $expenseQuery->get();
        $periodExpense = (float) $expenseMovements->sum(function($m) {
            return (float) ($m->quantity * ($m->product->cost_price ?? 0));
        });
        $periodExpenseCount = $expenseMovements->count();

        // Balance / Net Cashflow
        $netBalance = $periodIncome - $periodExpense;
        $isSurplus = $netBalance >= 0;
        $marginPercent = $periodIncome > 0 
            ? round(($netBalance / $periodIncome) * 100, 1) 
            : ($periodExpense > 0 ? -100 : 0);

        // All-time totals
        $totalAllIncome = (float) Transaction::where('status', 'success')->sum('total_amount');
        $totalAllExpense = (float) StockMovement::where('type', 'in')->with('product')->get()->sum(function($m) {
            return (float) ($m->quantity * ($m->product->cost_price ?? 0));
        });

        // 2. Supplier Breakdown for the period (Riwayat Transaksi Suplier)
        $supplierBreakdown = $expenseMovements->groupBy(function($item) {
            $name = str_replace('Supplier: ', '', $item->source_category ?: '');
            return $name ?: ($item->product->supplier_name ?: 'Suplier Direct');
        })->map(function($items, $supplierName) {
            $totalBuy = (float) $items->sum(function($m) {
                return (float) ($m->quantity * ($m->product->cost_price ?? 0));
            });
            return (object) [
                'supplier_name' => $supplierName,
                'items_count'   => $items->count(),
                'total_qty'     => $items->sum('quantity'),
                'total_amount'  => $totalBuy,
                'latest_date'   => $items->max('created_at'),
            ];
        })->sortByDesc('total_amount')->values();

        // 3. Periodic Expense Summary for Laporan Tab (Segmen 5: Harian, Mingguan, Bulanan)
        $todayExpense = (float) StockMovement::where('type', 'in')
            ->whereDate('created_at', Carbon::today())
            ->with('product')->get()
            ->sum(fn($m) => (float)($m->quantity * ($m->product->cost_price ?? 0)));

        $weekExpense = (float) StockMovement::where('type', 'in')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek()->startOfDay(), Carbon::now()->endOfWeek()->endOfDay()])
            ->with('product')->get()
            ->sum(fn($m) => (float)($m->quantity * ($m->product->cost_price ?? 0)));

        $monthExpense = (float) StockMovement::where('type', 'in')
            ->whereBetween('created_at', [Carbon::now()->startOfMonth()->startOfDay(), Carbon::now()->endOfMonth()->endOfDay()])
            ->with('product')->get()
            ->sum(fn($m) => (float)($m->quantity * ($m->product->cost_price ?? 0)));

        // 4. Fetch list based on Tab (semua, pemasukan, pengeluaran, laporan)
        $transQuery = Transaction::with(['items', 'customer'])->latest();
        if ($startDate && $endDate) $transQuery->whereBetween('created_at', [$startDate, $endDate]);
        if ($payment) $transQuery->where('payment_method', $payment);
        if ($status && $status !== 'all') $transQuery->where('status', $status);
        if ($search) {
            $transQuery->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('cashier_name', 'like', "%{$search}%");
            });
        }

        $smQuery = StockMovement::where('type', 'in')->with('product')->latest();
        if ($startDate && $endDate) $smQuery->whereBetween('created_at', [$startDate, $endDate]);
        if ($search) {
            $smQuery->where(function($q) use ($search) {
                $q->where('source_category', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhereHas('product', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Build combined items collection
        $combined = collect();

        if (in_array($tab, ['semua', 'pemasukan'])) {
            $transList = $transQuery->get();
            foreach ($transList as $t) {
                $combined->push((object)[
                    'id'             => $t->id,
                    'type'           => 'income',
                    'ref_number'     => $t->invoice_number,
                    'date'           => $t->created_at,
                    'party_name'     => $t->customer_name ?: 'Pelanggan Umum',
                    'party_type'     => $t->customer ? $t->customer->type_label : 'Retail',
                    'description'    => $t->items->pluck('product_name')->take(2)->implode(', ') . ($t->items->count() > 2 ? ' +' . ($t->items->count() - 2) . ' item' : ''),
                    'payment_method' => $t->payment_method,
                    'amount'         => (float)$t->total_amount,
                    'status'         => $t->status_label,
                    'status_class'   => $t->status_badge_class,
                    'user_name'      => $t->cashier_name,
                    'detail_url'     => route('transaksi.show', $t->id),
                ]);
            }
        }

        if (in_array($tab, ['semua', 'pengeluaran', 'laporan'])) {
            $smList = $smQuery->get();
            foreach ($smList as $sm) {
                $cost = (float)($sm->quantity * ($sm->product->cost_price ?? 0));
                
                // Extract invoice number from notes if available
                $refNo = 'FKT-STK-' . str_pad($sm->id, 5, '0', STR_PAD_LEFT);
                if (!empty($sm->notes) && preg_match('/Faktur:\s*([^•\s]+)/i', $sm->notes, $matches)) {
                    $refNo = $matches[1];
                }

                $supplierName = str_replace('Supplier: ', '', $sm->source_category ?: ($sm->product->supplier_name ?: 'Suplier Direct'));

                $combined->push((object)[
                    'id'             => $sm->id,
                    'type'           => 'expense',
                    'ref_number'     => $refNo,
                    'date'           => $sm->created_at,
                    'party_name'     => $supplierName,
                    'party_type'     => 'Suplier',
                    'description'    => ($sm->product->name ?? 'Produk') . " ({$sm->quantity} {$sm->unit})",
                    'payment_method' => 'Faktur Suplier',
                    'amount'         => $cost,
                    'status'         => 'Stok Masuk',
                    'status_class'   => 'bg-rose-100 text-rose-700 font-semibold',
                    'user_name'      => $sm->user_name ?: 'Petugas',
                    'detail_url'     => route('stok.index'),
                ]);
            }
        }

        // Sort descending by date
        $sorted = $combined->sortByDesc('date')->values();

        // Paginate manually
        $perPage = 10;
        $page = (int) $request->get('page', 1);
        $paginatedRecords = new LengthAwarePaginator(
            $sorted->forPage($page, $perPage)->values(),
            $sorted->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Recent stock mutasi for Laporan Tab
        $recentMutations = StockMovement::with('product')->latest()->take(10)->get();

        return view('transaksi.index', compact(
            'paginatedRecords',
            'tab',
            'period',
            'search',
            'status',
            'payment',
            'dateFrom',
            'dateTo',
            'periodIncome',
            'periodIncomeCount',
            'periodExpense',
            'periodExpenseCount',
            'netBalance',
            'isSurplus',
            'marginPercent',
            'totalAllIncome',
            'totalAllExpense',
            'supplierBreakdown',
            'todayExpense',
            'weekExpense',
            'monthExpense',
            'recentMutations'
        ));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('items.product');
        return view('transaksi.show', compact('transaction'));
    }
}
