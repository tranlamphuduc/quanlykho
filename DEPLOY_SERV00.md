# Hướng dẫn Deploy Laravel lên Serv00.com (Miễn phí)

## Bước 1: Đăng ký tài khoản Serv00

1. Truy cập: https://serv00.com
2. Click "Register" → Điền thông tin
3. Chờ email xác nhận (có thể mất vài giờ)
4. Sau khi được duyệt, bạn sẽ nhận email chứa:
   - Username
   - Password
   - Server (vd: s1.serv00.com)

## Bước 2: Đăng nhập Panel quản lý

1. Truy cập: https://panel.serv00.com
2. Đăng nhập với thông tin từ email

## Bước 3: Tạo Database MySQL

1. Vào **MySQL** trong panel
2. Click **Add database**
3. Điền:
   - Database name: `kho_laravel`
   - Username: `kho_user`
   - Password: (tạo password mạnh)
4. Ghi nhớ thông tin này

## Bước 4: Cấu hình Website

1. Vào **WWW Websites** → **Add new website**
2. Chọn:
   - Domain: `username.serv00.net` (hoặc domain riêng)
   - Document root: `/domains/username.serv00.net/public_html`
   - PHP version: **8.2** hoặc cao hơn

## Bước 5: Kết nối SSH

Mở terminal/cmd và chạy:
```bash
ssh username@sX.serv00.com
# Nhập password từ email
```

## Bước 6: Cài đặt Laravel trên Server

```bash
# Di chuyển vào thư mục domains
cd ~/domains/username.serv00.net

# Xóa public_html mặc định
rm -rf public_html

# Clone project từ GitHub (nếu có)
git clone https://github.com/your-username/kho-laravel.git .

# Hoặc upload qua FileZilla/SFTP

# Cài đặt dependencies
composer install --optimize-autoloader --no-dev

# Tạo symbolic link cho public
ln -s ~/domains/username.serv00.net/public ~/domains/username.serv00.net/public_html

# Copy file .env
cp .env.example .env

# Tạo key
php artisan key:generate
```

## Bước 7: Cấu hình .env

Sửa file `.env`:
```bash
nano .env
```

Cập nhật các giá trị:
```env
APP_NAME="Quản lý Kho"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://username.serv00.net

DB_CONNECTION=mysql
DB_HOST=mysql.serv00.com
DB_PORT=3306
DB_DATABASE=username_kho_laravel
DB_USERNAME=username_kho_user
DB_PASSWORD=your_password_here

SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

Lưu: `Ctrl+X` → `Y` → `Enter`

## Bước 8: Chạy Migration và Seeder

```bash
# Chạy migration
php artisan migrate --force

# Chạy seeder (tạo dữ liệu mẫu)
php artisan db:seed --force

# Tối ưu
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Phân quyền storage
chmod -R 775 storage bootstrap/cache
```

## Bước 9: Cấu hình .htaccess (nếu cần)

Tạo file `public/.htaccess`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.php$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]
</IfModule>
```

## Bước 10: Truy cập website

Mở trình duyệt: `https://username.serv00.net`

**Tài khoản mặc định:**
- Email: admin@example.com
- Password: password

---

## Upload bằng FileZilla (Thay thế cho Git)

Nếu không dùng Git:

1. Tải FileZilla: https://filezilla-project.org
2. Kết nối:
   - Host: `sX.serv00.com`
   - Username: từ email
   - Password: từ email
   - Port: `22` (SFTP)
3. Upload toàn bộ project vào `/domains/username.serv00.net/`
4. Tiếp tục từ Bước 6 (phần composer install)

---

## Lưu ý quan trọng

1. **Serv00 có thể xóa tài khoản không hoạt động** sau 90 ngày - đăng nhập panel định kỳ
2. **Backup database** thường xuyên qua phpMyAdmin
3. **Không upload file .env lên GitHub** - tạo trực tiếp trên server
4. Nếu gặp lỗi 500, kiểm tra `storage/logs/laravel.log`

---

## Troubleshooting

### Lỗi "Permission denied"
```bash
chmod -R 775 storage bootstrap/cache
```

### Lỗi "Class not found"
```bash
composer dump-autoload
php artisan config:clear
```

### Lỗi kết nối database
- Kiểm tra lại thông tin trong `.env`
- Host MySQL của Serv00 thường là: `mysql.serv00.com`

### Lỗi 404 trên các route
- Kiểm tra file `.htaccess` trong thư mục `public`
- Đảm bảo mod_rewrite được bật
