<?php

namespace App\Http\Controllers;

use App\Models\StockOut;
use App\Models\Product;
use App\Models\Warehouse;
use App\Exports\StockOutExport;
use App\Exports\StockOutTemplateExport;
use App\Imports\StockOutImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        $query = StockOut::with(['warehouse', 'user', 'approver']);
        
        // Lọc theo kho
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
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
            return redirect()->route('stock-out.index')->with('success', 'Tạo phiếu xuất kho thành công! Chờ Admin duyệt.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function show(StockOut $stockOut)
    {
        $stockOut->load(['warehouse', 'user', 'approver', 'details.product']);
        return view('stock-out.show', compact('stockOut'));
    }

    public function edit(StockOut $stockOut)
    {
        if ($stockOut->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể sửa phiếu đang chờ duyệt!');
        }

        if (auth()->user()->role !== 'admin' && $stockOut->user_id !== auth()->id()) {
            return back()->with('error', 'Bạn không có quyền sửa phiếu này!');
        }

        $stockOut->load(['details.product']);
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('stock-out.edit', compact('stockOut', 'products', 'warehouses'));
    }

    public function update(Request $request, StockOut $stockOut)
    {
        if ($stockOut->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể sửa phiếu đang chờ duyệt!');
        }

        if (auth()->user()->role !== 'admin' && $stockOut->user_id !== auth()->id()) {
            return back()->with('error', 'Bạn không có quyền sửa phiếu này!');
        }

        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|exists:products,id',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
            'unit_price' => 'required|array',
            'unit_price.*' => 'required|numeric|min:0',
        ]);

        $data = [
            'warehouse_id' => $request->warehouse_id,
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
            $stockOut->updateWithDetails($data, $details);
            return redirect()->route('stock-out.index')->with('success', 'Cập nhật phiếu xuất kho thành công!');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(StockOut $stockOut)
    {
        if ($stockOut->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể xóa phiếu đang chờ duyệt!');
        }

        if (auth()->user()->role !== 'admin' && $stockOut->user_id !== auth()->id()) {
            return back()->with('error', 'Bạn không có quyền xóa phiếu này!');
        }

        $stockOut->details()->delete();
        $stockOut->delete();

        return redirect()->route('stock-out.index')->with('success', 'Xóa phiếu xuất kho thành công!');
    }

    public function approve(StockOut $stockOut)
    {
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'Chỉ Admin mới có quyền duyệt phiếu!');
        }

        try {
            $stockOut->approve(auth()->id());
            return back()->with('success', 'Duyệt phiếu xuất kho thành công! Tồn kho đã được cập nhật.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function cancel(StockOut $stockOut)
    {
        if (auth()->user()->role !== 'admin') {
            return back()->with('error', 'Chỉ Admin mới có quyền hủy phiếu!');
        }

        try {
            $stockOut->cancel(auth()->id());
            return back()->with('success', 'Hủy phiếu xuất kho thành công!' . ($stockOut->status === 'completed' ? ' Tồn kho đã được hoàn trả.' : ''));
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function exportExcel(StockOut $stockOut)
    {
        $stockOut->load(['details.product']);
        return Excel::download(new StockOutExport($stockOut), 'phieu-xuat-' . $stockOut->code . '.xlsx');
    }

    public function exportPdf(StockOut $stockOut)
    {
        $stockOut->load(['warehouse', 'user', 'approver', 'details.product']);
        $pdf = Pdf::loadView('stock-out.pdf', compact('stockOut'));
        return $pdf->download('phieu-xuat-' . $stockOut->code . '.pdf');
    }

    public function downloadTemplate()
    {
        return Excel::download(new StockOutTemplateExport, 'mau-xuat-kho.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        try {
            $import = new StockOutImport;
            Excel::import($import, $request->file('file'));

            if ($import->hasErrors()) {
                return back()->with('error', 'Lỗi import: ' . implode(', ', $import->getErrors()))->withInput();
            }

            if (empty($import->getData())) {
                return back()->with('error', 'File không có dữ liệu hợp lệ!')->withInput();
            }

            $data = [
                'code' => StockOut::generateCode(),
                'warehouse_id' => $request->warehouse_id,
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'note' => $request->note ?? 'Import từ Excel',
            ];

            $details = [];
            foreach ($import->getData() as $row) {
                $details[] = [
                    'product_id' => $row['product_id'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'serial_number' => $row['serial_number'],
                ];
            }

            StockOut::createWithDetails($data, $details);
            return redirect()->route('stock-out.index')->with('success', 'Import thành công! Đã tạo phiếu xuất với ' . count($details) . ' sản phẩm.');
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage())->withInput();
        }
    }
}
