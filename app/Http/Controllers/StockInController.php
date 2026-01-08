<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Exports\StockInExport;
use App\Exports\StockInTemplateExport;
use App\Imports\StockInImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        $query = StockIn::with(['warehouse', 'supplier', 'user', 'approver']);
        
        // Lọc theo kho
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }
        
        // Lọc theo nhà cung cấp
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
        
        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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
        
        $stockIns = $query->orderBy('id')->get();
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
            return redirect()->route('stock-in.index')->with('success', 'Tạo phiếu nhập kho thành công! Chờ Admin duyệt.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function show(StockIn $stockIn)
    {
        $stockIn->load(['warehouse', 'supplier', 'user', 'approver', 'details.product']);
        return view('stock-in.show', compact('stockIn'));
    }

    public function edit(StockIn $stockIn)
    {
        // Chỉ cho sửa phiếu pending
        if ($stockIn->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể sửa phiếu đang chờ duyệt!');
        }

        // Kiểm tra quyền: Admin hoặc người tạo phiếu
        if (auth()->user()->role !== 'admin' && $stockIn->user_id !== auth()->id()) {
            return back()->with('error', 'Bạn không có quyền sửa phiếu này!');
        }

        $stockIn->load(['details.product']);
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('stock-in.edit', compact('stockIn', 'products', 'warehouses', 'suppliers'));
    }

    public function update(Request $request, StockIn $stockIn)
    {
        if ($stockIn->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể sửa phiếu đang chờ duyệt!');
        }

        if (auth()->user()->role !== 'admin' && $stockIn->user_id !== auth()->id()) {
            return back()->with('error', 'Bạn không có quyền sửa phiếu này!');
        }

        $request->validate([
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
            'warehouse_id' => $request->warehouse_id,
            'supplier_id' => $request->supplier_id,
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
            $stockIn->updateWithDetails($data, $details);
            return redirect()->route('stock-in.index')->with('success', 'Cập nhật phiếu nhập kho thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(StockIn $stockIn)
    {
        if ($stockIn->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể xóa phiếu đang chờ duyệt!');
        }

        if (auth()->user()->role !== 'admin' && $stockIn->user_id !== auth()->id()) {
            return back()->with('error', 'Bạn không có quyền xóa phiếu này!');
        }

        $stockIn->details()->delete();
        $stockIn->delete();

        return redirect()->route('stock-in.index')->with('success', 'Xóa phiếu nhập kho thành công!');
    }

    public function approve(StockIn $stockIn)
    {
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'Chỉ Admin mới có quyền duyệt phiếu!');
        }

        try {
            $stockIn->approve(auth()->id());
            return back()->with('success', 'Duyệt phiếu nhập kho thành công! Tồn kho đã được cập nhật.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function cancel(StockIn $stockIn)
    {
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'Chỉ Admin mới có quyền hủy phiếu!');
        }

        try {
            $stockIn->cancel(auth()->id());
            return back()->with('success', 'Hủy phiếu nhập kho thành công!' . ($stockIn->status === 'completed' ? ' Tồn kho đã được hoàn trả.' : ''));
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function exportExcel(StockIn $stockIn)
    {
        $stockIn->load(['details.product']);
        return Excel::download(new StockInExport($stockIn), 'phieu-nhap-' . $stockIn->code . '.xlsx');
    }

    public function exportPdf(StockIn $stockIn)
    {
        $stockIn->load(['warehouse', 'supplier', 'user', 'approver', 'details.product']);
        $pdf = Pdf::loadView('stock-in.pdf', compact('stockIn'));
        return $pdf->download('phieu-nhap-' . $stockIn->code . '.pdf');
    }

    public function downloadTemplate()
    {
        return Excel::download(new StockInTemplateExport, 'mau-nhap-kho.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'warehouse_id' => 'required|exists:warehouses,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        try {
            $import = new StockInImport;
            Excel::import($import, $request->file('file'));

            if ($import->hasErrors()) {
                return back()->with('error', 'Lỗi import: ' . implode(', ', $import->getErrors()))->withInput();
            }

            if (empty($import->getData())) {
                return back()->with('error', 'File không có dữ liệu hợp lệ!')->withInput();
            }

            $data = [
                'code' => StockIn::generateCode(),
                'warehouse_id' => $request->warehouse_id,
                'supplier_id' => $request->supplier_id,
                'user_id' => auth()->id(),
                'note' => $request->note ?? 'Import từ Excel',
            ];

            $details = [];
            foreach ($import->getData() as $row) {
                $details[] = [
                    'product_id' => $row['product_id'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'batch_number' => $row['batch_number'],
                    'expiry_date' => $row['expiry_date'],
                    'serial_number' => $row['serial_number'],
                ];
            }

            StockIn::createWithDetails($data, $details);
            return redirect()->route('stock-in.index')->with('success', 'Import thành công! Đã tạo phiếu nhập với ' . count($details) . ' sản phẩm.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }
}
