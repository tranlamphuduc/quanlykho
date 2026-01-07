<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Exports\StockInExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        $query = StockIn::with(['warehouse', 'supplier', 'user']);
        
        // Lọc theo kho
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        
        // Lọc theo nhà cung cấp
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        // Lọc theo ngày
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }
        
        // Tìm kiếm theo mã phiếu
        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }
        
        $stockIns = $query->latest()->get();
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $newCode = StockIn::generateCode();

        return view('stock-in.index', compact('stockIns', 'products', 'warehouses', 'suppliers', 'newCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:stock_ins,code',
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
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
            'supplier_id' => $request->supplier_id,
            'user_id' => auth()->id(),
            'note' => $request->note,
        ];

        $details = [];
        foreach ($request->product_id as $i => $productId) {
            if ($productId && $request->quantity[$i] > 0) {
                $details[] = [
                    'product_id' => $productId,
                    'quantity' => $request->quantity[$i],
                    'unit_price' => $request->unit_price[$i],
                    'batch_number' => $request->batch_number[$i] ?? null,
                    'expiry_date' => $request->expiry_date[$i] ?? null,
                    'serial_number' => $request->serial_number[$i] ?? null,
                ];
            }
        }

        try {
            StockIn::createWithDetails($data, $details);
            return redirect()->route('stock-in.index')->with('success', 'Tạo phiếu nhập kho thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function show(StockIn $stockIn)
    {
        $stockIn->load(['warehouse', 'supplier', 'user', 'details.product']);
        return view('stock-in.show', compact('stockIn'));
    }

    public function exportExcel(StockIn $stockIn)
    {
        $stockIn->load(['details.product']);
        return Excel::download(new StockInExport($stockIn), 'phieu-nhap-' . $stockIn->code . '.xlsx');
    }

    public function exportPdf(StockIn $stockIn)
    {
        $stockIn->load(['warehouse', 'supplier', 'user', 'details.product']);
        $pdf = Pdf::loadView('stock-in.pdf', compact('stockIn'));
        return $pdf->download('phieu-nhap-' . $stockIn->code . '.pdf');
    }
}
