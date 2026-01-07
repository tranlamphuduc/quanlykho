<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StockInImport implements ToArray, WithHeadingRow
{
    public array $data = [];
    public array $errors = [];

    public function array(array $rows): void
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // +2 vì có header row và index bắt đầu từ 0
            
            // Bỏ qua dòng trống
            if (empty($row['ma_sp']) && empty($row['so_luong'])) {
                continue;
            }

            // Validate mã SP
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

            // Validate số lượng
            $quantity = intval($row['so_luong'] ?? 0);
            if ($quantity <= 0) {
                $this->errors[] = "Dòng {$rowNum}: Số lượng phải > 0";
                continue;
            }

            // Validate đơn giá
            $unitPrice = floatval($row['don_gia'] ?? $product->cost_price);
            if ($unitPrice < 0) {
                $this->errors[] = "Dòng {$rowNum}: Đơn giá không hợp lệ";
                continue;
            }

            // Parse ngày hết hạn
            $expiryDate = null;
            if (!empty($row['han_sd'])) {
                try {
                    $expiryDate = \Carbon\Carbon::parse($row['han_sd'])->format('Y-m-d');
                } catch (\Exception $e) {
                    // Bỏ qua nếu không parse được
                }
            }

            $this->data[] = [
                'product_id' => $product->id,
                'product_code' => $product->code,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'batch_number' => $row['so_lo'] ?? null,
                'expiry_date' => $expiryDate,
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
