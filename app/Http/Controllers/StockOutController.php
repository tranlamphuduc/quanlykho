<?php

namespace App\Http\Controllers;

use App\Models\StockOut;
use App\Models\Product;
use App\Models\Warehouse;
use App\Exports\StockOutExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        $query = StockOut::with(['warehouse', 'user']);
        
        // Lọc theo kho
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        
        // Lọc theo ngày
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        // Tìm kiếm theo mã phiếu hoặc khách hàng
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }
        
        $stockOuts = $query->latest()->get();
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $newCode = StockOut::generateCode();

        return view('stock-out.index', compact('stockOuts', 'products', 'warehouses', 'newCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:stock_outs,code',
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
            'unit_price' => 'required|array',
            'unit_price.*' => 'required|numeric|min:0',
        ]);

        $data = [
            'code' => $request->code,
            'warehouse_id' => $request->warehouse_id,
            'user_id' => auth()->id(),
            'customer_name' => $request->customer_name,
            'note' => $request->note,
        ];

        $details = [];
        foreach ($request->product_id as $i => $productId) {
            if ($productId && $request->quantity[$i] > 0) {
                $details[] = [
                    'product_id' => $productId,
                    'quantity' => $request->quantity[$i],
                    'unit_price' => $request->unit_price[$i],
                    'serial_number' => $request->serial_number[$i] ?? null,
                ];
            }
        }

        try {
            StockOut::createWithDetails($data, $details);
            return redirect()->route('stock-out.index')->with('success', 'Tạo phiếu xuất kho thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function show(StockOut $stockOut)
    {
        $stockOut->load(['warehouse', 'user', 'details.product']);
        return view('stock-out.show', compact('stockOut'));
    }

    public function exportExcel(StockOut $stockOut)
    {
        $stockOut->load(['details.product']);
        return Excel::download(new StockOutExport($stockOut), 'phieu-xuat-' . $stockOut->code . '.xlsx');
    }

    public function exportPdf(StockOut $stockOut)
    {
        $stockOut->load(['warehouse', 'user', 'details.product']);
        $pdf = Pdf::loadView('stock-out.pdf', compact('stockOut'));
        return $pdf->download('phieu-xuat-' . $stockOut->code . '.pdf');
    }
}
