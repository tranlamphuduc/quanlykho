# Requirements Document

## Introduction

Tính năng này giải quyết vấn đề tương thích giữa file Excel xuất ra và file Excel nhập vào trong hệ thống quản lý kho. Hiện tại, khi xuất file Excel có cột STT (số thứ tự), người dùng không thể nhập lại bằng chính file đó vì hệ thống Import không xử lý được cột STT. Mục tiêu là cho phép người dùng xuất file Excel và có thể nhập lại file đó mà không cần chỉnh sửa thủ công.

## Glossary

- **STT**: Số thứ tự - cột đánh số thứ tự các dòng trong bảng Excel
- **Export**: Chức năng xuất dữ liệu ra file Excel
- **Import**: Chức năng nhập dữ liệu từ file Excel vào hệ thống
- **StockIn**: Phiếu nhập kho
- **StockOut**: Phiếu xuất kho
- **Heading Row**: Dòng tiêu đề trong file Excel chứa tên các cột
- **Round-trip**: Quá trình xuất dữ liệu ra file rồi nhập lại vào hệ thống

## Requirements

### Requirement 1

**User Story:** As a warehouse staff, I want to export stock-in data to Excel and import it back without manual editing, so that I can easily duplicate or modify existing stock-in records.

#### Acceptance Criteria

1. WHEN the system exports StockIn data to Excel THEN the system SHALL include column headers that match the expected Import format
2. WHEN the system imports an Excel file with STT column THEN the system SHALL ignore the STT column and process remaining columns correctly
3. WHEN the system imports an Excel file exported from the system THEN the system SHALL successfully parse all product data without errors
4. WHEN the system exports StockIn data THEN the system SHALL format numeric values (đơn giá, thành tiền) as raw numbers without thousand separators for import compatibility
5. WHEN the system imports an Excel file with formatted numbers (containing dots or commas) THEN the system SHALL parse them correctly as numeric values

### Requirement 2

**User Story:** As a warehouse staff, I want to export stock-out data to Excel and import it back without manual editing, so that I can easily duplicate or modify existing stock-out records.

#### Acceptance Criteria

1. WHEN the system exports StockOut data to Excel THEN the system SHALL include column headers that match the expected Import format
2. WHEN the system imports an Excel file with STT column THEN the system SHALL ignore the STT column and process remaining columns correctly
3. WHEN the system imports an Excel file exported from the system THEN the system SHALL successfully parse all product data without errors
4. WHEN the system exports StockOut data THEN the system SHALL format numeric values as raw numbers without thousand separators for import compatibility
5. WHEN the system imports an Excel file with formatted numbers THEN the system SHALL parse them correctly as numeric values

### Requirement 3

**User Story:** As a developer, I want the Export and Import to use consistent column naming, so that round-trip data flow works seamlessly.

#### Acceptance Criteria

1. WHEN the system exports data THEN the system SHALL use snake_case column headers (ma_sp, so_luong, don_gia) that match Import expectations
2. WHEN the system imports data THEN the system SHALL support both Vietnamese headers (Mã SP) and snake_case headers (ma_sp) for flexibility
3. WHEN the system exports StockIn data THEN the system SHALL include all importable fields: ma_sp, so_luong, don_gia, so_lo, han_sd, serial
4. WHEN the system exports StockOut data THEN the system SHALL include all importable fields: ma_sp, so_luong, don_gia, serial

### Requirement 4

**User Story:** As a warehouse staff, I want clear feedback when importing fails, so that I can fix issues in the Excel file.

#### Acceptance Criteria

1. WHEN the system encounters an unrecognized column during import THEN the system SHALL skip that column without error
2. WHEN the system imports a file with missing required columns THEN the system SHALL report which columns are missing
3. WHEN the system successfully imports data THEN the system SHALL report the number of rows processed
