<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');
        $payment = $request->get('payment');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = Transaction::with('items')->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('cashier_name', 'like', "%{$search}%");
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($payment) {
            $query->where('payment_method', $payment);
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $transactions = $query->paginate(10);

        // Summary stats
        $todayTransactions = Transaction::whereDate('created_at', Carbon::today());
        $totalTransToday = $todayTransactions->count();
        $totalRevenueToday = $todayTransactions->sum('total_amount');
        $successCount = Transaction::where('status', 'success')->count();
        $refundedCount = Transaction::where('status', 'refunded')->count();
        $voidCount = Transaction::where('status', 'void')->count();
        $totalAll = Transaction::count();

        $avgTransValue = $totalAll > 0
            ? Transaction::where('status', 'success')->avg('total_amount') ?? 0
            : 0;

        return view('transaksi.index', compact(
            'transactions',
            'search',
            'status',
            'payment',
            'dateFrom',
            'dateTo',
            'totalTransToday',
            'totalRevenueToday',
            'successCount',
            'refundedCount',
            'voidCount',
            'totalAll',
            'avgTransValue'
        ));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load('items.product');
        return view('transaksi.show', compact('transaction'));
    }
}
