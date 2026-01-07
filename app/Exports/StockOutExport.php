<?php

namespace App\Exports;

use App\Models\StockOut;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class StockOutExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $stockOut;

    public function __construct(StockOut $stockOut)
    {
        $this->stockOut = $stockOut;
    }

    public function collection()
    {
        return $this->stockOut->details;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mã SP',
            'Tên sản phẩm',
            'Số lượng',
            'Đơn giá',
            'Thành tiền',
            'Serial',
        ];
    }

    public function map($detail): array
    {
        static $index = 0;
        $index++;
        return [
            $index,
            $detail->product->code,
            $detail->product->name,
            $detail->quantity,
            $detail->unit_price,
            $detail->quantity * $detail->unit_price,
            $detail->serial_number ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Phiếu xuất ' . $this->stockOut->code;
    }
}
