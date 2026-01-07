<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockOutImport implements ToArray, WithHeadingRow
{
    public array $data = [];
    public array $errors = [];

    public function array(array $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;
            
            if (empty($row['ma_sp']) && empty($row['so_luong'])) {
                continue;
            }

            $productCode = trim($row['ma_sp'] ?? '');
            if (empty($productCode)) {
                $this->errors[] = "Dòng {$rowNum}: Mã SP không được để trống";
                continue;
            }

            $product = Product::where('code', $productCode)->first();
            if (!$product) {
                $this->errors[] = "Dòng {$rowNum}: Mã SP '{$productCode}' không tồn tại";
                continue;
            }

            $quantity = intval($row['so_luong'] ?? 0);
            if ($quantity <= 0) {
                $this->errors[] = "Dòng {$rowNum}: Số lượng phải > 0";
                continue;
            }

            $unitPrice = floatval($row['don_gia'] ?? $product->sell_price);
            if ($unitPrice < 0) {
                $this->errors[] = "Dòng {$rowNum}: Đơn giá không hợp lệ";
                continue;
            }

            $this->data[] = [
                'product_id' => $product->id,
                'product_code' => $product->code,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'serial_number' => $row['serial'] ?? null,
            ];
        }
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
