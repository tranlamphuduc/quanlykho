<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $categoryId;
    protected $supplierId;
    protected $search;

    public function __construct($categoryId = null, $supplierId = null, $search = null)
    {
        $this->categoryId = $categoryId;
        $this->supplierId = $supplierId;
        $this->search = $search;
    }

    public function collection()
    {
        $query = Product::with(['category', 'supplier']);
        
        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }
        
        if ($this->supplierId) {
            $query->where('supplier_id', $this->supplierId);
        }
        
        if ($this->search) {
            $search = $this->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        
        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Mã SP',
            'Mã vạch',
            'Tên sản phẩm',
            'Danh mục',
            'Nhà cung cấp',
            'Đơn vị',
            'Giá vốn',
            'Giá bán',
            'Tồn min',
            'Tồn max',
            'Mô tả',
        ];
    }

    public function map($product): array
    {
        return [
            $product->code,
            $product->barcode ?? '',
            $product->name,
            $product->category->name ?? 'N/A',
            $product->supplier->name ?? 'N/A',
            $product->unit,
            number_format($product->cost_price, 0, ',', '.') . 'đ',
            number_format($product->sell_price, 0, ',', '.') . 'đ',
            $product->min_stock,
            $product->max_stock,
            $product->description ?? '',
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
        return 'Danh sách sản phẩm';
    }
}
