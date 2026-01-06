# Công nghệ sử dụng trong Website Quản lý Kho Đồ Gia Dụng

## CÔNG NGHỆ CHÍNH

| Công nghệ | Mô tả |
|-----------|-------|
| **PHP 8.2** | Ngôn ngữ lập trình backend |
| **Laravel 12** | Framework PHP theo mô hình MVC |
| **MySQL** | Hệ quản trị cơ sở dữ liệu |
| **Bootstrap 5** | CSS Framework cho giao diện |
| **Chart.js** | Thư viện vẽ biểu đồ |

---

## DANH SÁCH ĐẦY ĐỦ CÁC CÔNG NGHỆ

### 1. Backend

| Công nghệ | Phiên bản | Mô tả |
|-----------|-----------|-------|
| PHP | 8.2+ | Ngôn ngữ lập trình chính |
| Laravel Framework | 12.44.0 | Framework PHP theo mô hình MVC |
| Laravel Breeze | 2.3 | Hệ thống Authentication (đăng nhập/đăng ký/quên mật khẩu) |
| Laravel Tinker | 2.10 | REPL để debug và test |
| Eloquent ORM | (built-in) | Object-Relational Mapping - thao tác database như object |
| Blade Template Engine | (built-in) | Template engine của Laravel |
| MySQL | 5.7+ | Hệ quản trị cơ sở dữ liệu |
| PDO | (built-in) | PHP Data Objects - database driver |

### 2. Frontend

| Công nghệ | Phiên bản | Mô tả |
|-----------|-----------|-------|
| Bootstrap | 5.3.0 | CSS Framework responsive, mobile-first |
| Bootstrap Icons | 1.10.0 | Thư viện icon SVG |
| jQuery | 3.6.0 | JavaScript library |
| DataTables | 1.13.4 | Plugin hiển thị bảng dữ liệu (phân trang, tìm kiếm, sắp xếp) |
| Chart.js | latest | Thư viện vẽ biểu đồ (bar, line, pie chart) |

### 3. Development Tools

| Công nghệ | Mô tả |
|-----------|-------|
| Composer | PHP dependency manager |
| NPM | Node.js package manager |
| Vite | Build tool cho frontend assets |
| PHPUnit | Framework unit testing |
| Laravel Pint | Code style fixer theo chuẩn PSR-12 |
| FakerPHP | Thư viện tạo dữ liệu mẫu |

### 4. Kiến trúc & Design Patterns

| Pattern | Mô tả |
|---------|-------|
| MVC (Model-View-Controller) | Tách biệt logic xử lý, giao diện và dữ liệu |
| Middleware | Xử lý request trước khi đến controller (Auth, Admin) |
| Migration | Quản lý version schema database |
| Seeder | Tạo dữ liệu mẫu tự động |
| RBAC (Role-Based Access Control) | Phân quyền theo vai trò (Admin, Thủ kho, Sales) |

### 5. Bảo mật

| Tính năng | Mô tả |
|-----------|-------|
| CSRF Protection | Chống tấn công Cross-Site Request Forgery |
| XSS Protection | Blade tự động escape output |
| SQL Injection Prevention | Eloquent ORM sử dụng prepared statements |
| Password Hashing | Bcrypt với 12 rounds |
| Session Security | Session được mã hóa và xác thực |

### 6. Server & Infrastructure

| Công nghệ | Mô tả |
|-----------|-------|
| Apache | Web server |
| XAMPP | Local development stack (Apache + MySQL + PHP) |

### 7. Cấu trúc thư mục Laravel

```
kho-laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Xử lý logic nghiệp vụ
│   │   └── Middleware/      # Xử lý request (Auth, Admin)
│   └── Models/              # Eloquent Models (ORM)
├── database/
│   ├── migrations/          # Schema database
│   └── seeders/             # Dữ liệu mẫu
├── resources/
│   └── views/               # Blade templates
├── routes/
│   ├── web.php              # Routes cho web
│   └── auth.php             # Routes authentication
├── config/                  # Cấu hình ứng dụng
├── public/                  # Entry point (index.php)
├── storage/                 # Logs, cache, uploads
└── vendor/                  # Dependencies (Composer)
```
