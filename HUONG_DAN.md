# Hướng dẫn chạy Website Quản lý Kho - Laravel trên XAMPP

## Bước 1: Khởi động XAMPP
1. Mở XAMPP Control Panel
2. Start **Apache** và **MySQL**

## Bước 2: Tạo Database
1. Mở trình duyệt: http://localhost/phpmyadmin
2. Tạo database mới tên: `warehouse_db`
3. Chọn Collation: `utf8mb4_unicode_ci`

## Bước 3: Chạy Migration
Mở Command Prompt/PowerShell, chạy:
```cmd
cd C:\xampp\htdocs\quanlykho\kho-laravel
C:\xampp\php\php.exe artisan migrate
C:\xampp\php\php.exe artisan db:seed
```

## Bước 4: Truy cập Website
Mở trình duyệt: http://localhost/quanlykho/kho-laravel/public/

## Tài khoản đăng nhập
| Email | Password | Vai trò |
|-------|----------|---------|
| admin@warehouse.com | password | Quản trị viên |
| thukho@warehouse.com | password | Thủ kho |
| sales@warehouse.com | password | Nhân viên KD |

## Tính năng
- ✅ Dashboard với biểu đồ Chart.js
- ✅ CRUD Sản phẩm, Danh mục, Nhà cung cấp, Kho
- ✅ Nhập/Xuất kho với quản lý lô, serial, hạn sử dụng
- ✅ Báo cáo tồn kho, cảnh báo tồn kho thấp/cao
- ✅ Phân quyền RBAC (Admin, Thủ kho, Sales)
- ✅ Laravel Breeze Authentication
- ✅ Eloquent ORM, Migration, Seeder

## Cấu trúc thư mục
```
kho-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controllers
│   │   └── Middleware/      # AdminMiddleware
│   └── Models/              # Eloquent Models
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/             # Data seeders
├── resources/views/         # Blade templates
├── routes/web.php           # Web routes
└── .env                     # Cấu hình database
```

## Lưu ý
- Nếu gặp lỗi, kiểm tra file `.env` đã cấu hình đúng database chưa
- Đảm bảo MySQL đang chạy trước khi chạy migration
