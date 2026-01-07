# Công nghệ sử dụng - Hệ thống Quản lý Kho Đồ Gia Dụng

## 1. CÔNG NGHỆ CHÍNH

| Công nghệ | Phiên bản | Mô tả |
|-----------|-----------|-------|
| **PHP** | 8.2+ | Ngôn ngữ lập trình backend |
| **Laravel** | 12.x | Framework PHP theo mô hình MVC |
| **PostgreSQL** | 15+ | Hệ quản trị CSDL (Production - Neon) |
| **Bootstrap** | 5.3 | CSS Framework responsive |
| **Chart.js** | Latest | Thư viện vẽ biểu đồ |

---

## 2. BACKEND

| Công nghệ | Mô tả |
|-----------|-------|
| Laravel Framework | Framework PHP theo mô hình MVC |
| Laravel Breeze | Hệ thống Authentication |
| Eloquent ORM | Object-Relational Mapping |
| Blade Template | Template engine của Laravel |
| Maatwebsite Excel | Xuất file Excel |
| Barryvdh DomPDF | Xuất file PDF |
| Simple QRCode | Tạo mã QR cho sản phẩm |

## 3. FRONTEND

| Công nghệ | Mô tả |
|-----------|-------|
| Bootstrap 5.3 | CSS Framework responsive |
| Bootstrap Icons | Thư viện icon SVG |
| jQuery 3.6 | JavaScript library |
| DataTables 1.13 | Plugin bảng dữ liệu (phân trang, tìm kiếm, sắp xếp) |
| Chart.js | Biểu đồ thống kê (bar, line, pie) |

## 4. DEPLOYMENT & INFRASTRUCTURE

| Công nghệ | Mô tả |
|-----------|-------|
| Docker | Container hóa ứng dụng |
| Render.com | Cloud hosting (Web Service) |
| Neon.tech | PostgreSQL Database hosting |
| Apache | Web server trong Docker |

## 5. KIẾN TRÚC & DESIGN PATTERNS

| Pattern | Mô tả |
|---------|-------|
| MVC | Model-View-Controller |
| Middleware | Xử lý Auth, Admin permission |
| Migration | Quản lý schema database |
| Seeder | Tạo dữ liệu mẫu |
| RBAC | Phân quyền theo vai trò |

## 6. BẢO MẬT

| Tính năng | Mô tả |
|-----------|-------|
| CSRF Protection | Chống Cross-Site Request Forgery |
| XSS Protection | Blade tự động escape output |
| SQL Injection Prevention | Eloquent sử dụng prepared statements |
| Password Hashing | Bcrypt encryption |
| Session Security | Session mã hóa và xác thực |

## 7. CẤU TRÚC THƯ MỤC

```
kho-laravel/
├── app/
│   ├── Http/Controllers/    # Xử lý logic nghiệp vụ
│   ├── Models/              # Eloquent Models
│   └── Exports/             # Export Excel/PDF
├── database/
│   ├── migrations/          # Schema database
│   └── seeders/             # Dữ liệu mẫu
├── resources/views/         # Blade templates
├── routes/web.php           # Routes
├── Dockerfile               # Docker configuration
└── docker-entrypoint.sh     # Docker startup script
```

## 8. TÍNH NĂNG ĐÃ TRIỂN KHAI

- ✅ Quản lý sản phẩm, danh mục, nhà cung cấp, kho hàng
- ✅ Nhập kho / Xuất kho với chi tiết phiếu
- ✅ Báo cáo tồn kho theo kho, trạng thái
- ✅ Dashboard thống kê với biểu đồ
- ✅ Tạo mã QR cho sản phẩm
- ✅ Xuất báo cáo PDF/Excel
- ✅ Phân quyền: Admin, Thủ kho, Nhân viên KD
- ✅ Cảnh báo tồn kho (thấp/cao)
- ✅ Responsive design (mobile-friendly)
