# Design Document: Excel Import/Export Compatibility

## Overview

Tính năng này cải thiện khả năng tương thích giữa chức năng Export và Import Excel trong hệ thống quản lý kho. Mục tiêu chính là cho phép người dùng xuất file Excel và nhập lại file đó mà không cần chỉnh sửa thủ công (round-trip compatibility).

### Vấn đề hiện tại

1. **Cột STT**: Export có cột STT nhưng Import không biết bỏ qua
2. **Format số**: Export format số có dấu chấm (1.000.000) nhưng Import không parse được
3. **Header không khớp**: Export dùng tiếng Việt (Mã SP) nhưng Import expect snake_case (ma_sp)

### Giải pháp

1. Cập nhật Export để sử dụng header snake_case tương thích với Import
2. Cập nhật Import để bỏ qua cột STT và các cột không cần thiết
3. Export số nguyên thay vì số đã format
4. Import hỗ trợ parse số có format (dấu chấm, dấu phẩy)

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Excel File Format                         │
│  ┌─────┬───────┬──────────┬─────────┬─────────┬─────────┐  │
│  │ stt │ ma_sp │ ten_sp   │ so_luong│ don_gia │ ...     │  │
│  └─────┴───────┴──────────┴─────────┴─────────┴─────────┘  │
└─────────────────────────────────────────────────────────────┘
           │                              ▲
           │ Import                       │ Export
           ▼                              │
┌─────────────────────────────────────────────────────────────┐
│                    Laravel Application                       │
│  ┌──────────────────┐      ┌──────────────────┐            │
│  │  StockInImport   │      │  StockInExport   │            │
│  │  - Ignore STT    │      │  - snake_case    │            │
│  │  - Parse formats │      │  - Raw numbers   │            │
│  │  - Multi-header  │      │                  │            │
│  └──────────────────┘      └──────────────────┘            │
│  ┌──────────────────┐      ┌──────────────────┐            │
│  │  StockOutImport  │      │  StockOutExport  │            │
│  │  - Ignore STT    │      │  - snake_case    │            │
│  │  - Parse formats │      │  - Raw numbers   │            │
│  │  - Multi-header  │      │                  │            │
│  └──────────────────┘      └──────────────────┘            │
└─────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### 1. Export Classes

#### StockInExport
```php
class StockInExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function headings(): array
    {
        return [
            'stt',           // Giữ STT cho hiển thị
            'ma_sp',         // snake_case để tương thích Import
            'ten_sp',
            'so_luong',
            'don_gia',       // Raw number, không format
            'thanh_tien',
            'so_lo',
            'han_sd',
            'serial',
        ];
    }
    
    public function map($detail): array
    {
        // Return raw numbers instead of formatted strings
        return [
            $index,
            $detail->product->code,
            $detail->product->name,
            $detail->quantity,           // int
            $detail->unit_price,         // float, không format
            $detail->quantity * $detail->unit_price,  // float
            $detail->batch_number ?? '',
            $detail->expiry_date?->format('Y-m-d') ?? '',
            $detail->serial_number ?? '',
        ];
    }
}
```

#### StockOutExport
```php
class StockOutExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    public function headings(): array
    {
        return [
            'stt',
            'ma_sp',
            'ten_sp', 
            'so_luong',
            'don_gia',
            'thanh_tien',
            'serial',
        ];
    }
}
```

### 2. Import Classes

#### StockInImport
```php
class StockInImport implements ToArray, WithHeadingRow
{
    // Header mapping: Vietnamese -> snake_case
    private const HEADER_MAP = [
        'mã sp' => 'ma_sp',
        'ma sp' => 'ma_sp',
        'tên sản phẩm' => 'ten_sp',
        'ten san pham' => 'ten_sp',
        'số lượng' => 'so_luong',
        'so luong' => 'so_luong',
        'đơn giá' => 'don_gia',
        'don gia' => 'don_gia',
        'số lô' => 'so_lo',
        'so lo' => 'so_lo',
        'hạn sd' => 'han_sd',
        'han sd' => 'han_sd',
    ];
    
    // Columns to ignore
    private const IGNORED_COLUMNS = ['stt', 'thanh_tien', 'thành tiền', 'ten_sp', 'tên sản phẩm'];
    
    private function parseNumber($value): float
    {
        if (is_numeric($value)) return floatval($value);
        // Remove thousand separators (dots) and convert comma to dot
        $cleaned = str_replace(['.', ','], ['', '.'], $value);
        return floatval($cleaned);
    }
}
```

#### StockOutImport
```php
class StockOutImport implements ToArray, WithHeadingRow
{
    // Similar structure to StockInImport
    private const HEADER_MAP = [...];
    private const IGNORED_COLUMNS = ['stt', 'thanh_tien', 'thành tiền', 'ten_sp'];
}
```

## Data Models

### Excel Column Mapping

#### StockIn Export/Import Columns
| Export Header | Import Key | Required | Description |
|--------------|------------|----------|-------------|
| stt | (ignored) | No | Số thứ tự - chỉ để hiển thị |
| ma_sp | ma_sp | Yes | Mã sản phẩm |
| ten_sp | (ignored) | No | Tên sản phẩm - lấy từ DB |
| so_luong | so_luong | Yes | Số lượng nhập |
| don_gia | don_gia | No | Đơn giá (default: cost_price) |
| thanh_tien | (ignored) | No | Thành tiền - tính tự động |
| so_lo | so_lo | No | Số lô |
| han_sd | han_sd | No | Hạn sử dụng |
| serial | serial | No | Serial number |

#### StockOut Export/Import Columns
| Export Header | Import Key | Required | Description |
|--------------|------------|----------|-------------|
| stt | (ignored) | No | Số thứ tự |
| ma_sp | ma_sp | Yes | Mã sản phẩm |
| ten_sp | (ignored) | No | Tên sản phẩm |
| so_luong | so_luong | Yes | Số lượng xuất |
| don_gia | don_gia | No | Đơn giá (default: sell_price) |
| thanh_tien | (ignored) | No | Thành tiền |
| serial | serial | No | Serial number |

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Round-trip StockIn data preservation
*For any* valid StockIn detail data, exporting to Excel and then importing back SHALL produce equivalent data (same product_id, quantity, unit_price, batch_number, expiry_date, serial_number).

**Validates: Requirements 1.3**

### Property 2: Round-trip StockOut data preservation  
*For any* valid StockOut detail data, exporting to Excel and then importing back SHALL produce equivalent data (same product_id, quantity, unit_price, serial_number).

**Validates: Requirements 2.3**

### Property 3: STT column is ignored during import
*For any* Excel data with an STT column containing any value (numeric, text, empty), the import process SHALL ignore the STT column and correctly process the remaining columns.

**Validates: Requirements 1.2, 2.2**

### Property 4: Formatted numbers are parsed correctly
*For any* numeric value formatted with thousand separators (dots) or decimal separators (commas), the import process SHALL parse it to the correct numeric value.

**Validates: Requirements 1.5, 2.5**

### Property 5: Unknown columns are ignored without error
*For any* Excel file containing columns not in the expected column list, the import process SHALL skip those columns and process known columns without error.

**Validates: Requirements 4.1**

### Property 6: Import row count matches input
*For any* valid Excel file with N data rows (excluding header), the import process SHALL report exactly N rows processed (or N minus invalid rows with specific error messages).

**Validates: Requirements 4.3**

## Error Handling

### Import Errors

| Error Type | Condition | Message Format |
|------------|-----------|----------------|
| Missing Required Column | ma_sp or so_luong column not found | "Thiếu cột bắt buộc: {column_name}" |
| Invalid Product Code | Product code not in database | "Dòng {row}: Mã SP '{code}' không tồn tại" |
| Invalid Quantity | Quantity <= 0 or not numeric | "Dòng {row}: Số lượng phải > 0" |
| Invalid Price | Price < 0 | "Dòng {row}: Đơn giá không hợp lệ" |
| Invalid Date | Date format not parseable | (Silently skip, use null) |

### Export Behavior

- Export always succeeds if data exists
- Empty collections export with header row only
- Null values exported as empty string

## Testing Strategy

### Property-Based Testing

Sử dụng **PHPUnit** với data providers để thực hiện property-based testing.

#### Test Structure
```
tests/
├── Feature/
│   └── ExcelImportExport/
│       ├── StockInRoundTripTest.php      # Property 1
│       ├── StockOutRoundTripTest.php     # Property 2
│       ├── SttColumnIgnoredTest.php      # Property 3
│       ├── NumberParsingTest.php         # Property 4
│       ├── UnknownColumnsTest.php        # Property 5
│       └── RowCountTest.php              # Property 6
```

#### Property Test Format
```php
/**
 * **Feature: excel-import-export-compatibility, Property 1: Round-trip StockIn data preservation**
 * @dataProvider stockInDataProvider
 */
public function test_stockin_roundtrip_preserves_data($productCode, $quantity, $unitPrice, $batchNumber, $expiryDate, $serial)
{
    // Generate export data
    // Import the exported data
    // Assert imported data matches original
}

public static function stockInDataProvider(): array
{
    // Generate 100+ random test cases
    return [...];
}
```

### Unit Tests

- Test individual helper methods (parseNumber, normalizeHeader)
- Test edge cases (empty file, single row, max rows)
- Test error message formatting
