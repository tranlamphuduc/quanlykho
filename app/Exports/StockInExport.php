<?php

namespace App\Exports;

use App\Models\StockIn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class StockInExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $stockIn;

    public function __construct(StockIn $stockIn)
    {
        $this->stockIn = $stockIn;
    }

    public function collection()
    {
        return $this->stockIn->details;
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
            'Số lô',
            'Hạn SD',
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
            number_format($detail->unit_price, 0, ',', '.'),
            number_format($detail->quantity * $detail->unit_price, 0, ',', '.'),
            $detail->batch_number ?? '-',
            $detail->expiry_date ? $detail->expiry_date->format('d/m/Y') : '-',
            $detail->serial_number ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Phiếu nhập ' . $this->stockIn->code;
    }
}
