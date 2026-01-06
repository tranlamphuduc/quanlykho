<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Category;
use App\Exports\InventoryExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        $categoryId = $request->category_id;
        $status = $request->status;
        $search = $request->search;
        
        $warehouses = Warehouse::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        
        $query = Inventory::with(['product.category', 'warehouse']);
        
        // Lọc theo kho
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
        
        // Lọc theo danh mục
        if ($categoryId) {
            $query->whereHas('product', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }
        
        // Tìm kiếm theo mã SP hoặc tên
        if ($search) {
            $query->whereHas('product', function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        $inventory = $query->get();
        
        // Lọc theo trạng thái (sau khi lấy dữ liệu)
        if ($status) {
            $inventory = $inventory->filter(function($item) use ($status) {
                if ($status == 'low' && $item->quantity <= $item->product->min_stock) return true;
                if ($status == 'normal' && $item->quantity > $item->product->min_stock && $item->quantity < $item->product->max_stock) return true;
                if ($status == 'over' && $item->quantity >= $item->product->max_stock) return true;
                return false;
            });
        }
        
        // Tính toán thống kê
        $allInventory = Inventory::with('product')->get();
        $lowStockCount = $allInventory->filter(fn($i) => $i->quantity <= $i->product->min_stock)->count();
        $overStockCount = $allInventory->filter(fn($i) => $i->quantity >= $i->product->max_stock)->count();
        $normalStockCount = $allInventory->count() - $lowStockCount - $overStockCount;
        
        $totalValue = Inventory::getTotalValue($warehouseId);

        return view('inventory.index', compact(
            'inventory', 'warehouses', 'categories', 'warehouseId', 'totalValue',
            'lowStockCount', 'normalStockCount', 'overStockCount'
        ));
    }

    public function exportExcel(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        $categoryId = $request->category_id;
        $status = $request->status;
        $search = $request->search;
        
        $filename = 'ton_kho_' . date('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new InventoryExport($warehouseId, $categoryId, $status, $search), $filename);
    }

    public function exportPdf(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        $categoryId = $request->category_id;
        $status = $request->status;
        $search = $request->search;
        
        $query = Inventory::with(['product.category', 'warehouse']);
        
        if ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        }
        
        if ($categoryId) {
            $query->whereHas('product', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }
        
        if ($search) {
            $query->whereHas('product', function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        $inventory = $query->get();
        
        if ($status) {
            $inventory = $inventory->filter(function($item) use ($status) {
                if ($status == 'low' && $item->quantity <= $item->product->min_stock) return true;
                if ($status == 'normal' && $item->quantity > $item->product->min_stock && $item->quantity < $item->product->max_stock) return true;
                if ($status == 'over' && $item->quantity >= $item->product->max_stock) return true;
                return false;
            });
        }
        
        $totalValue = Inventory::getTotalValue($warehouseId);
        $warehouse = $warehouseId ? Warehouse::find($warehouseId) : null;

        $pdf = Pdf::loadView('inventory.pdf', compact('inventory', 'totalValue', 'warehouse'));
        return $pdf->download('ton_kho_' . date('Y-m-d_H-i-s') . '.pdf');
    }
}
