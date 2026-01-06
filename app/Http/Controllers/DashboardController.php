<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\StockIn;
use App\Models\StockOut;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'totalProducts' => Product::count(),
            'totalCategories' => Category::count(),
            'totalSuppliers' => Supplier::count(),
            'totalInventoryValue' => Inventory::getTotalValue(),
            'lowStockProducts' => Inventory::getLowStockProducts(),
            'topProducts' => StockOut::getTopProducts(5),
            'stockInStats' => StockIn::getMonthlyStats(),
            'stockOutStats' => StockOut::getMonthlyStats(),
        ];

        return view('dashboard', $data);
    }
}
