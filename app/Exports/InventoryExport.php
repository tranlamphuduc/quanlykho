<?php

namespace App\Exports;

use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $warehouseId;
    protected $categoryId;
    protected $status;
    protected $search;

    public function __construct($warehouseId = null, $categoryId = null, $status = null, $search = null)
    {
        $this->warehouseId = $warehouseId;
        $this->categoryId = $categoryId;
        $this->status = $status;
        $this->search = $search;
    }

    public function collection()
    {
        $query = Inventory::with(['product.category', 'warehouse']);
        
        if ($this->warehouseId) {
            $query->where('warehouse_id', $this->warehouseId);
        }
        
        if ($this->categoryId) {
            $query->whereHas('product', function($q) {
                $q->where('category_id', $this->categoryId);
            });
        }
        
        if ($this->search) {
            $search = $this->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        $inventory = $query->get();
        
        if ($this->status) {
            $status = $this->status;
            $inventory = $inventory->filter(function($item) use ($status) {
                if ($status == 'low' && $item->quantity <= $item->product->min_stock) return true;
                if ($status == 'normal' && $item->quantity > $item->product->min_stock && $item->quantity < $item->product->max_stock) return true;
                if ($status == 'over' && $item->quantity >= $item->product->max_stock) return true;
                return false;
            });
        }
        
        return $inventory;
    }

    public function headings(): array
    {
        return [
            'Mã SP',
            'Tên sản phẩm',
            'Danh mục',
            'Kho',
            'Tồn kho',
            'Đơn vị',
            'Min',
            'Max',
            'Trạng thái',
            'Giá vốn',
            'Giá trị tồn',
        ];
    }

    public function map($item): array
    {
        $status = 'Bình thường';
        if ($item->quantity <= $item->product->min_stock) {
            $status = 'Tồn thấp';
        } elseif ($item->quantity >= $item->product->max_stock) {
            $status = 'Tồn cao';
        }

        return [
            $item->product->code,
            $item->product->name,
            $item->product->category->name ?? 'N/A',
            $item->warehouse->name,
            $item->quantity,
            $item->product->unit,
            $item->product->min_stock,
            $item->product->max_stock,
            $status,
            number_format($item->product->cost_price, 0, ',', '.') . 'đ',
            number_format($item->quantity * $item->product->cost_price, 0, ',', '.') . 'đ',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'Báo cáo tồn kho';
    }
}
