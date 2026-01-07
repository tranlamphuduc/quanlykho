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
            
            // Lấy giá trị an toàn
            $maSp = $this->getValue($row, 'ma_sp');
            $soLuong = $this->getValue($row, 'so_luong');
            $donGia = $this->getValue($row, 'don_gia');
            $serial = $this->getValue($row, 'serial');
            
            // Bỏ qua dòng trống hoàn toàn
            if (empty($maSp) && empty($soLuong)) {
                continue;
            }

            // Validate mã SP
            $productCode = trim($maSp ?? '');
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
            $quantity = intval($soLuong);
            if ($quantity <= 0) {
                $this->errors[] = "Dòng {$rowNum}: Số lượng phải > 0 (hiện tại: '{$soLuong}')";
                continue;
            }

            // Validate đơn giá - nếu trống thì dùng giá bán của sản phẩm
            $unitPrice = $donGia !== '' && $donGia !== null ? $this->parseNumber($donGia) : $product->sell_price;
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
                'serial_number' => !empty($serial) ? $serial : null,
            ];
        }
    }

    /**
     * Lấy giá trị an toàn từ row, tránh lỗi nếu key không tồn tại
     */
    private function getValue(array $row, string $key): mixed
    {
        if (array_key_exists($key, $row)) {
            return $row[$key];
        }
        
        // Thử tìm key tương tự (lowercase, trim)
        foreach ($row as $k => $v) {
            if (strtolower(trim($k)) === strtolower($key)) {
                return $v;
            }
        }
        
        return null;
    }

    /**
     * Parse số từ chuỗi, xử lý cả số có dấu chấm/phẩy phân cách hàng nghìn
     * VD: "450.000" hoặc "450,000" -> 450000
     */
    private function parseNumber($value): float
    {
        if (is_numeric($value)) {
            return floatval($value);
        }
        
        $value = trim((string) $value);
        
        // Xóa dấu chấm/phẩy phân cách hàng nghìn (giữ lại số)
        $cleaned = preg_replace('/[.,](?=\d{3}(?:[.,]|$))/', '', $value);
        
        return floatval($cleaned);
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
