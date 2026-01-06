<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $query = Warehouse::with('manager');
        
        // Tìm kiếm theo tên kho
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Lọc theo người quản lý
        if ($request->filled('manager_id')) {
            $query->where('manager_id', $request->manager_id);
        }
        
        $warehouses = $query->orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('warehouses.index', compact('warehouses', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'address' => 'nullable',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        Warehouse::create($validated);

        return redirect()->route('warehouses.index')->with('success', 'Thêm kho thành công!');
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'address' => 'nullable',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        $warehouse->update($validated);

        return redirect()->route('warehouses.index')->with('success', 'Cập nhật kho thành công!');
    }

    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->route('warehouses.index')->with('success', 'Xóa kho thành công!');
    }
}
