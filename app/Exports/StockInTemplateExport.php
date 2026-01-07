<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockInTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        // Dữ liệu mẫu
        return [
            ['SP000001', 100, 50000, 'LOT001', '2027-12-31', 'SN001'],
            ['SP000002', 50, 30000, '', '', ''],
        ];
    }

    public function headings(): array
    {
        return ['ma_sp', 'so_luong', 'don_gia', 'so_lo', 'han_sd', 'serial'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]],
        ];
    }
}
