# MÔ TẢ CHỨC NĂNG WEBSITE QUẢN LÝ KHO ĐỒ GIA DỤNG

## 1. GIỚI THIỆU

Website Quản lý Kho Đồ Gia Dụng là hệ thống quản lý kho hàng chuyên nghiệp, được xây dựng trên nền tảng Laravel Framework. Hệ thống hỗ trợ quản lý đa kho, theo dõi nhập xuất hàng hóa, cảnh báo tồn kho và báo cáo thống kê trực quan.

---

## 2. CÁCH HOẠT ĐỘNG CỦA NHẬP KHO

### 2.1. Quy trình nhập kho

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│  Tạo phiếu nhập │ --> │  Thêm sản phẩm  │ --> │   Lưu phiếu     │
│  (Chọn kho,NCC) │     │  (SL, giá, lô)  │     │   nhập kho      │
└─────────────────┘     └─────────────────┘     └────────┬────────┘
                                                         │
                                                         ▼
                                               ┌─────────────────┐
                                               │  Cập nhật tồn   │
                                               │  kho tự động    │
                                               │  (CỘNG số lượng)│
                                               └─────────────────┘
```

### 2.2. Chi tiết các bước

**Bước 1: Tạo phiếu nhập**
- Thủ kho vào menu "Nhập kho" → Click "Tạo phiếu nhập"
- Hệ thống tự động sinh mã phiếu theo format: `PN` + `YYYYMMDD` + `số thứ tự`
  - Ví dụ: `PN202601050001` (Phiếu nhập ngày 05/01/2026, số 0001)
- Chọn kho nhập (Kho Quận 7 hoặc Kho Quận 9)
- Chọn nhà cung cấp (tùy chọn)
- Nhập ghi chú (tùy chọn)

**Bước 2: Thêm sản phẩm vào phiếu**
- Chọn sản phẩm từ danh sách
- Nhập số lượng nhập
- Nhập đơn giá nhập (hệ thống tự động điền giá vốn mặc định)
- Nhập số lô (Batch Number) - dùng cho hàng tiêu hao
- Nhập hạn sử dụng (Expiry Date) - dùng cho hàng có hạn
- Nhập số Serial - dùng cho đồ điện tử
- Click "Thêm dòng" để thêm nhiều sản phẩm

**Bước 3: Lưu phiếu nhập**
- Click "Lưu phiếu nhập"
- Hệ thống thực hiện trong 1 transaction:
  1. Tạo record trong bảng `stock_ins`
  2. Tạo các record chi tiết trong bảng `stock_in_details`
  3. Cập nhật bảng `inventory`: **CỘNG** số lượng vào tồn kho
  4. Tính tổng tiền phiếu nhập

### 2.3. Code xử lý nhập kho (Model StockIn)

```php
public static function createWithDetails(array $data, array $details): self
{
    return DB::transaction(function () use ($data, $details) {
        // Tạo phiếu nhập
        $stockIn = self::create($data);
        $totalAmount = 0;

        foreach ($details as $detail) {
            // Thêm chi tiết sản phẩm
            $stockIn->details()->create($detail);
            $totalAmount += $detail['quantity'] * $detail['unit_price'];
            
            // CỘNG tồn kho
            Inventory::updateStock(
                $detail['product_id'],
                $data['warehouse_id'],
                $detail['quantity'],
                true  // true = cộng
            );
        }

        // Cập nhật tổng tiền
        $stockIn->update(['total_amount' => $totalAmount]);
        return $stockIn;
    });
}
```

---

## 3. CÁCH HOẠT ĐỘNG CỦA XUẤT KHO

### 3.1. Quy trình xuất kho

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│  Tạo phiếu xuất │ --> │  Kiểm tra tồn   │ --> │   Thêm sản phẩm │
│  (Chọn kho, KH) │     │  kho đủ không?  │     │   (SL, giá)     │
└─────────────────┘     └────────┬────────┘     └────────┬────────┘
                                 │                       │
                        ┌────────┴────────┐              │
                        │                 │              │
                        ▼                 ▼              ▼
                   ┌─────────┐      ┌──────────┐   ┌─────────────┐
                   │ KHÔNG   │      │   ĐỦ     │   │ Lưu phiếu   │
                   │ đủ hàng │      │  hàng    │-->│ xuất kho    │
                   │ → Báo   │      └──────────┘   └──────┬──────┘
                   │   lỗi   │                            │
                   └─────────┘                            ▼
                                                  ┌─────────────────┐
                                                  │  Cập nhật tồn   │
                                                  │  kho tự động    │
                                                  │  (TRỪ số lượng) │
                                                  └─────────────────┘
```

### 3.2. Chi tiết các bước

**Bước 1: Tạo phiếu xuất**
- Thủ kho vào menu "Xuất kho" → Click "Tạo phiếu xuất"
- Hệ thống tự động sinh mã phiếu theo format: `PX` + `YYYYMMDD` + `số thứ tự`
  - Ví dụ: `PX202601050001` (Phiếu xuất ngày 05/01/2026, số 0001)
- Chọn kho xuất
- Nhập tên khách hàng (tùy chọn)
- Nhập ghi chú (tùy chọn)

**Bước 2: Thêm sản phẩm vào phiếu**
- Chọn sản phẩm từ danh sách
- Nhập số lượng xuất
- **Hệ thống kiểm tra**: Số lượng xuất ≤ Tồn kho hiện tại
- Nếu không đủ hàng → Báo lỗi, không cho xuất
- Nhập đơn giá bán (hệ thống tự động điền giá bán mặc định)
- Nhập số Serial (nếu có)
- Click "Thêm dòng" để thêm nhiều sản phẩm

**Bước 3: Lưu phiếu xuất**
- Click "Lưu phiếu xuất"
- Hệ thống thực hiện trong 1 transaction:
  1. Kiểm tra lại tồn kho tất cả sản phẩm
  2. Nếu đủ hàng: Tạo record trong bảng `stock_outs`
  3. Tạo các record chi tiết trong bảng `stock_out_details`
  4. Cập nhật bảng `inventory`: **TRỪ** số lượng khỏi tồn kho
  5. Tính tổng tiền phiếu xuất

### 3.3. Code xử lý xuất kho (Model StockOut)

```php
public static function createWithDetails(array $data, array $details): self
{
    return DB::transaction(function () use ($data, $details) {
        // KIỂM TRA TỒN KHO TRƯỚC
        foreach ($details as $detail) {
            $currentStock = Inventory::getStock($detail['product_id'], $data['warehouse_id']);
            if ($currentStock < $detail['quantity']) {
                throw new \Exception("Sản phẩm không đủ số lượng trong kho!");
            }
        }

        // Tạo phiếu xuất
        $stockOut = self::create($data);
        $totalAmount = 0;

        foreach ($details as $detail) {
            // Thêm chi tiết sản phẩm
            $stockOut->details()->create($detail);
            $totalAmount += $detail['quantity'] * $detail['unit_price'];
            
            // TRỪ tồn kho
            Inventory::updateStock(
                $detail['product_id'],
                $data['warehouse_id'],
                $detail['quantity'],
                false  // false = trừ
            );
        }

        // Cập nhật tổng tiền
        $stockOut->update(['total_amount' => $totalAmount]);
        return $stockOut;
    });
}
```

---

## 4. CÁCH CẬP NHẬT TỒN KHO

### 4.1. Model Inventory

```php
public static function updateStock($productId, $warehouseId, $quantity, $isAdd = true): void
{
    // Tìm hoặc tạo record tồn kho
    $inventory = self::firstOrCreate(
        ['product_id' => $productId, 'warehouse_id' => $warehouseId],
        ['quantity' => 0]
    );

    // Cộng hoặc trừ số lượng
    $inventory->quantity = $isAdd 
        ? $inventory->quantity + $quantity   // Nhập kho: CỘNG
        : $inventory->quantity - $quantity;  // Xuất kho: TRỪ
    
    $inventory->save();
}
```

### 4.2. Bảng Inventory

| Cột | Mô tả |
|-----|-------|
| product_id | ID sản phẩm |
| warehouse_id | ID kho |
| quantity | Số lượng tồn kho |

- Mỗi sản phẩm có thể tồn ở nhiều kho khác nhau
- Unique key: (product_id, warehouse_id)

---

## 5. PHÂN QUYỀN NGƯỜI DÙNG (RBAC)

| Vai trò | Quyền hạn |
|---------|-----------|
| **Admin** | Toàn quyền: Quản lý người dùng, xem tất cả báo cáo |
| **Thủ kho** | Nhập/xuất kho, quản lý sản phẩm, xem tồn kho |
| **Sales** | Chỉ xem danh sách sản phẩm và tồn kho |

---

## 6. CÁC MODULE CHỨC NĂNG

### 6.1. Dashboard
- Thống kê: Số sản phẩm, danh mục, NCC, giá trị tồn kho
- Biểu đồ nhập/xuất theo tháng (Chart.js)
- Top 5 sản phẩm xuất nhiều nhất
- Cảnh báo tồn kho thấp

### 6.2. Quản lý sản phẩm
- CRUD sản phẩm (Thêm, Sửa, Xóa)
- Thông tin: Mã SP, Barcode, Tên, Danh mục, NCC, Đơn vị, Giá vốn, Giá bán, Min/Max stock

### 6.3. Quản lý danh mục
- CRUD danh mục
- Hiển thị số sản phẩm trong mỗi danh mục

### 6.4. Quản lý nhà cung cấp
- CRUD nhà cung cấp
- Thông tin: Tên, SĐT, Email, Địa chỉ

### 6.5. Quản lý kho hàng
- CRUD kho
- Phân công người quản lý kho

### 6.6. Nhập kho
- Tạo phiếu nhập với nhiều sản phẩm
- Quản lý theo lô, hạn sử dụng, serial
- Tự động cập nhật tồn kho

### 6.7. Xuất kho
- Tạo phiếu xuất với nhiều sản phẩm
- Kiểm tra tồn kho trước khi xuất
- Tự động cập nhật tồn kho

### 6.8. Báo cáo tồn kho
- Xem tồn kho theo từng kho
- Trạng thái: Bình thường / Tồn thấp / Tồn cao
- Tổng giá trị tồn kho

### 6.9. Báo cáo thống kê
- Tổng nhập/xuất theo năm
- Biểu đồ xu hướng
- Top sản phẩm bán chạy
- Cảnh báo tồn kho

### 6.10. Quản lý người dùng (Admin)
- CRUD người dùng
- Phân quyền vai trò
- Khóa/mở khóa tài khoản

---

## 7. TÀI KHOẢN MẪU

| Email | Mật khẩu | Vai trò |
|-------|----------|---------|
| admin@warehouse.com | password | Quản trị viên |
| thukho@warehouse.com | password | Thủ kho |
| thukho2@warehouse.com | password | Thủ kho |
| sales@warehouse.com | password | Nhân viên KD |
