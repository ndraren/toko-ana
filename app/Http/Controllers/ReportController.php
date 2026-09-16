<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $movements = StockMovement::with('product')->latest()->take(10)->get();
        return view('laporan.index', compact('products', 'movements'));
    }
}
