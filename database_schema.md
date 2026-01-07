# Thiết kế Cơ sở dữ liệu - Hệ thống Quản lý Kho

## 1. SƠ ĐỒ QUAN HỆ (ERD)

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│     USERS       │       │   CATEGORIES    │       │   SUPPLIERS     │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ PK id           │       │ PK id           │       │ PK id           │
│    name         │       │    name         │       │    name         │
│    email        │       │    description  │       │    phone        │
│    password     │       │    created_at   │       │    email        │
│    role         │       │    updated_at   │       │    address      │
│    status       │       └────────┬────────┘       │    created_at   │
│    created_at   │                │                │    updated_at   │
│    updated_at   │                │                └────────┬────────┘
└────────┬────────┘                │                         │
         │                         │                         │
         │ 1:N                     │ 1:N                     │ 1:N
         ▼                         ▼                         ▼
┌─────────────────┐       ┌─────────────────────────────────────────┐
│   WAREHOUSES    │       │              PRODUCTS                    │
├─────────────────┤       ├─────────────────────────────────────────┤
│ PK id           │       │ PK id                                    │
│    name         │       │    code (unique)                         │
│    address      │       │    barcode                               │
│ FK manager_id ──┼───────│    name                                  │
│    created_at   │       │ FK category_id ─────────────────────────┤
│    updated_at   │       │ FK supplier_id ─────────────────────────┤
└────────┬────────┘       │    unit                                  │
         │                │    cost_price                            │
         │                │    sell_price                            │
         │                │    min_stock                             │
         │                │    max_stock                             │
         │                │    description                           │
         │                │    created_at                            │
         │                │    updated_at                            │
         │                └────────────────────┬────────────────────┘
         │                                     │
         │ 1:N                                 │ 1:N
         ▼                                     ▼
┌─────────────────────────────────────────────────────┐
│                    INVENTORY                         │
├─────────────────────────────────────────────────────┤
│ PK id                                                │
│ FK product_id ──────────────────────────────────────┤
│ FK warehouse_id ────────────────────────────────────┤
│    quantity                                          │
│    created_at                                        │
│    updated_at                                        │
│    UNIQUE(product_id, warehouse_id)                  │
└─────────────────────────────────────────────────────┘

┌─────────────────┐                   ┌─────────────────┐
│   STOCK_INS     │                   │   STOCK_OUTS    │
├─────────────────┤                   ├─────────────────┤
│ PK id           │                   │ PK id           │
│    code (unique)│                   │    code (unique)│
│ FK warehouse_id │                   │ FK warehouse_id │
│ FK supplier_id  │                   │ FK user_id      │
│ FK user_id      │                   │    customer_name│
│    total_amount │                   │    total_amount │
│    note         │                   │    note         │
│    status       │                   │    status       │
│    created_at   │                   │    created_at   │
│    updated_at   │                   │    updated_at   │
└────────┬────────┘                   └────────┬────────┘
         │ 1:N                                 │ 1:N
         ▼                                     ▼
┌─────────────────────┐               ┌─────────────────────┐
│ STOCK_IN_DETAILS    │               │ STOCK_OUT_DETAILS   │
├─────────────────────┤               ├─────────────────────┤
│ PK id               │               │ PK id               │
│ FK stock_in_id      │               │ FK stock_out_id     │
│ FK product_id       │               │ FK product_id       │
│    quantity         │               │    quantity         │
│    unit_price       │               │    unit_price       │
│    batch_number     │               │    serial_number    │
│    expiry_date      │               │    created_at       │
│    serial_number    │               │    updated_at       │
│    created_at       │               └─────────────────────┘
│    updated_at       │
└─────────────────────┘
```

---

## 2. CHI TIẾT CÁC BẢNG

### 2.1. Bảng `users` - Người dùng

| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----|--------------|-----------|-------|
| id | BIGINT | PK, AUTO_INCREMENT | Mã người dùng |
| name | VARCHAR(255) | NOT NULL | Họ tên |
| email | VARCHAR(255) | NOT NULL, UNIQUE | Email đăng nhập |
| password | VARCHAR(255) | NOT NULL | Mật khẩu (bcrypt) |
| role | ENUM | DEFAULT 'warehouse_keeper' | Vai trò: admin, warehouse_keeper, sales |
| status | BOOLEAN | DEFAULT true | Trạng thái hoạt động |
| remember_token | VARCHAR(100) | NULLABLE | Token ghi nhớ đăng nhập |
| created_at | TIMESTAMP | | Ngày tạo |
| updated_at | TIMESTAMP | | Ngày cập nhật |

---

### 2.2. Bảng `categories` - Danh mục sản phẩm

| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----|--------------|-----------|-------|
| id | BIGINT | PK, AUTO_INCREMENT | Mã danh mục |
| name | VARCHAR(100) | NOT NULL | Tên danh mục |
| description | TEXT | NULLABLE | Mô tả |
| created_at | TIMESTAMP | | Ngày tạo |
| updated_at | TIMESTAMP | | Ngày cập nhật |

---

### 2.3. Bảng `suppliers` - Nhà cung cấp

| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----|--------------|-----------|-------|
| id | BIGINT | PK, AUTO_INCREMENT | Mã NCC |
| name | VARCHAR(150) | NOT NULL | Tên nhà cung cấp |
| phone | VARCHAR(20) | NULLABLE | Số điện thoại |
| email | VARCHAR(100) | NULLABLE | Email |
| address | TEXT | NULLABLE | Địa chỉ |
| created_at | TIMESTAMP | | Ngày tạo |
| updated_at | TIMESTAMP | | Ngày cập nhật |

---

### 2.4. Bảng `warehouses` - Kho hàng

| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----|--------------|-----------|-------|
| id | BIGINT | PK, AUTO_INCREMENT | Mã kho |
| name | VARCHAR(100) | NOT NULL | Tên kho |
| address | TEXT | NULLABLE | Địa chỉ kho |
| manager_id | BIGINT | FK → users(id), NULLABLE | Người quản lý |
| created_at | TIMESTAMP | | Ngày tạo |
| updated_at | TIMESTAMP | | Ngày cập nhật |

---

### 2.5. Bảng `products` - Sản phẩm

| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----|--------------|-----------|-------|
| id | BIGINT | PK, AUTO_INCREMENT | Mã sản phẩm |
| code | VARCHAR(50) | NOT NULL, UNIQUE | Mã SP (VD: SP000001) |
| barcode | VARCHAR(50) | NULLABLE, UNIQUE | Mã vạch |
| name | VARCHAR(200) | NOT NULL | Tên sản phẩm |
| category_id | BIGINT | FK → categories(id), NULLABLE | Danh mục |
| supplier_id | BIGINT | FK → suppliers(id), NULLABLE | Nhà cung cấp |
| unit | VARCHAR(30) | DEFAULT 'Cái' | Đơn vị tính |
| cost_price | DECIMAL(15,2) | DEFAULT 0 | Giá vốn |
| sell_price | DECIMAL(15,2) | DEFAULT 0 | Giá bán |
| min_stock | INT | DEFAULT 10 | Tồn kho tối thiểu |
| max_stock | INT | DEFAULT 1000 | Tồn kho tối đa |
| description | TEXT | NULLABLE | Mô tả |
| image | VARCHAR(255) | NULLABLE | Đường dẫn ảnh |
| created_at | TIMESTAMP | | Ngày tạo |
| updated_at | TIMESTAMP | | Ngày cập nhật |

---

### 2.6. Bảng `inventory` - Tồn kho

| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----|--------------|-----------|-------|
| id | BIGINT | PK, AUTO_INCREMENT | ID |
| product_id | BIGINT | FK → products(id), CASCADE | Sản phẩm |
| warehouse_id | BIGINT | FK → warehouses(id), CASCADE | Kho |
| quantity | INT | DEFAULT 0 | Số lượng tồn |
| created_at | TIMESTAMP | | Ngày tạo |
| updated_at | TIMESTAMP | | Ngày cập nhật |

**Ràng buộc:** UNIQUE(product_id, warehouse_id) - Mỗi sản phẩm chỉ có 1 bản ghi tồn kho trong mỗi kho.

---

### 2.7. Bảng `stock_ins` - Phiếu nhập kho

| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----|--------------|-----------|-------|
| id | BIGINT | PK, AUTO_INCREMENT | ID phiếu |
| code | VARCHAR(50) | NOT NULL, UNIQUE | Mã phiếu (VD: PN20260106001) |
| warehouse_id | BIGINT | FK → warehouses(id) | Kho nhập |
| supplier_id | BIGINT | FK → suppliers(id), NULLABLE | Nhà cung cấp |
| user_id | BIGINT | FK → users(id) | Người tạo phiếu |
| total_amount | DECIMAL(15,2) | DEFAULT 0 | Tổng tiền |
| note | TEXT | NULLABLE | Ghi chú |
| status | ENUM | DEFAULT 'completed' | Trạng thái: pending, completed, cancelled |
| created_at | TIMESTAMP | | Ngày tạo |
| updated_at | TIMESTAMP | | Ngày cập nhật |

---

### 2.8. Bảng `stock_in_details` - Chi tiết phiếu nhập

| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----|--------------|-----------|-------|
| id | BIGINT | PK, AUTO_INCREMENT | ID |
| stock_in_id | BIGINT | FK → stock_ins(id), CASCADE | Phiếu nhập |
| product_id | BIGINT | FK → products(id) | Sản phẩm |
| quantity | INT | NOT NULL | Số lượng nhập |
| unit_price | DECIMAL(15,2) | DEFAULT 0 | Đơn giá nhập |
| batch_number | VARCHAR(50) | NULLABLE | Số lô |
| expiry_date | DATE | NULLABLE | Hạn sử dụng |
| serial_number | VARCHAR(100) | NULLABLE | Số serial |
| created_at | TIMESTAMP | | Ngày tạo |
| updated_at | TIMESTAMP | | Ngày cập nhật |

---

### 2.9. Bảng `stock_outs` - Phiếu xuất kho

| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----|--------------|-----------|-------|
| id | BIGINT | PK, AUTO_INCREMENT | ID phiếu |
| code | VARCHAR(50) | NOT NULL, UNIQUE | Mã phiếu (VD: PX20260106001) |
| warehouse_id | BIGINT | FK → warehouses(id) | Kho xuất |
| user_id | BIGINT | FK → users(id) | Người tạo phiếu |
| customer_name | VARCHAR(150) | NULLABLE | Tên khách hàng |
| total_amount | DECIMAL(15,2) | DEFAULT 0 | Tổng tiền |
| note | TEXT | NULLABLE | Ghi chú |
| status | ENUM | DEFAULT 'completed' | Trạng thái |
| created_at | TIMESTAMP | | Ngày tạo |
| updated_at | TIMESTAMP | | Ngày cập nhật |

---

### 2.10. Bảng `stock_out_details` - Chi tiết phiếu xuất

| Cột | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----|--------------|-----------|-------|
| id | BIGINT | PK, AUTO_INCREMENT | ID |
| stock_out_id | BIGINT | FK → stock_outs(id), CASCADE | Phiếu xuất |
| product_id | BIGINT | FK → products(id) | Sản phẩm |
| quantity | INT | NOT NULL | Số lượng xuất |
| unit_price | DECIMAL(15,2) | DEFAULT 0 | Đơn giá xuất |
| serial_number | VARCHAR(100) | NULLABLE | Số serial |
| created_at | TIMESTAMP | | Ngày tạo |
| updated_at | TIMESTAMP | | Ngày cập nhật |

---

## 3. QUAN HỆ GIỮA CÁC BẢNG

| Bảng 1 | Quan hệ | Bảng 2 | Mô tả |
|--------|---------|--------|-------|
| users | 1:N | warehouses | Một user quản lý nhiều kho |
| users | 1:N | stock_ins | Một user tạo nhiều phiếu nhập |
| users | 1:N | stock_outs | Một user tạo nhiều phiếu xuất |
| categories | 1:N | products | Một danh mục có nhiều sản phẩm |
| suppliers | 1:N | products | Một NCC cung cấp nhiều sản phẩm |
| suppliers | 1:N | stock_ins | Một NCC có nhiều phiếu nhập |
| warehouses | 1:N | inventory | Một kho có nhiều bản ghi tồn kho |
| warehouses | 1:N | stock_ins | Một kho có nhiều phiếu nhập |
| warehouses | 1:N | stock_outs | Một kho có nhiều phiếu xuất |
| products | 1:N | inventory | Một SP có tồn kho ở nhiều kho |
| products | 1:N | stock_in_details | Một SP xuất hiện trong nhiều chi tiết nhập |
| products | 1:N | stock_out_details | Một SP xuất hiện trong nhiều chi tiết xuất |
| stock_ins | 1:N | stock_in_details | Một phiếu nhập có nhiều chi tiết |
| stock_outs | 1:N | stock_out_details | Một phiếu xuất có nhiều chi tiết |

---

## 4. INDEXES

| Bảng | Index | Cột | Mục đích |
|------|-------|-----|----------|
| users | UNIQUE | email | Đảm bảo email không trùng |
| products | UNIQUE | code | Đảm bảo mã SP không trùng |
| products | UNIQUE | barcode | Đảm bảo mã vạch không trùng |
| inventory | UNIQUE | (product_id, warehouse_id) | Mỗi SP chỉ 1 bản ghi/kho |
| stock_ins | UNIQUE | code | Đảm bảo mã phiếu không trùng |
| stock_outs | UNIQUE | code | Đảm bảo mã phiếu không trùng |

---

## 5. CHUẨN HÓA DATABASE

Database được thiết kế theo **chuẩn 3NF (Third Normal Form)**:

1. **1NF:** Mỗi cột chứa giá trị nguyên tử, không có nhóm lặp
2. **2NF:** Tất cả cột không khóa phụ thuộc hoàn toàn vào khóa chính
3. **3NF:** Không có phụ thuộc bắc cầu giữa các cột không khóa

**Ví dụ:**
- Thông tin NCC được tách riêng bảng `suppliers`, không lưu trực tiếp trong `products`
- Chi tiết phiếu nhập/xuất được tách riêng bảng `stock_in_details`, `stock_out_details`
