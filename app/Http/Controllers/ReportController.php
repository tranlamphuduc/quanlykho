<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Inventory;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? date('Y');
        
        $stockInStats = StockIn::getMonthlyStats($year);
        $stockOutStats = StockOut::getMonthlyStats($year);
        $topProducts = StockOut::getTopProducts(10, $year);
        
        $lowStockProducts = Inventory::with(['product', 'warehouse'])
            ->whereHas('product', function ($q) {
                $q->whereColumn('inventory.quantity', '<=', 'products.min_stock');
            })->get();
            
        $overStockProducts = Inventory::with(['product', 'warehouse'])
            ->whereHas('product', function ($q) {
                $q->whereColumn('inventory.quantity', '>=', 'products.max_stock');
            })->get();

        $totalStockIn = $stockInStats->sum('total');
        $totalStockOut = $stockOutStats->sum('total');

        return view('reports.index', compact(
            'year', 'stockInStats', 'stockOutStats', 'topProducts',
            'lowStockProducts', 'overStockProducts', 'totalStockIn', 'totalStockOut'
        ));
    }
}
