# Hướng dẫn Deploy Laravel lên Render.com (Miễn phí)

## Ưu điểm Render.com
- ✅ Hoàn toàn miễn phí, không cần thẻ tín dụng
- ✅ PostgreSQL database miễn phí
- ✅ Deploy tự động từ GitHub
- ⚠️ App "ngủ" sau 15 phút không dùng (khởi động lại mất ~30s)

---

## Bước 1: Chuẩn bị code

### 1.1 Push code lên GitHub
```bash
cd kho-laravel
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/kho-laravel.git
git push -u origin main
```

### 1.2 Các file cần có (đã tạo sẵn)
- `Dockerfile`
- `render.yaml`

---

## Bước 2: Đăng ký Render.com

1. Truy cập: https://render.com
2. Click **Get Started for Free**
3. Đăng nhập bằng **GitHub** (khuyến nghị)

---

## Bước 3: Tạo PostgreSQL Database

1. Vào Dashboard → **New** → **PostgreSQL**
2. Điền:
   - Name: `kho-laravel-db`
   - Region: `Singapore` (gần VN nhất)
   - Instance Type: **Free**
3. Click **Create Database**
4. Chờ tạo xong, copy **Internal Database URL**

---

## Bước 4: Tạo Web Service

1. Dashboard → **New** → **Web Service**
2. Chọn **Build and deploy from a Git repository**
3. Connect GitHub repo `kho-laravel`
4. Cấu hình:
   - Name: `kho-laravel`
   - Region: `Singapore`
   - Branch: `main`
   - Runtime: **Docker**
   - Instance Type: **Free**

5. Click **Advanced** → **Add Environment Variable**:

| Key | Value |
|-----|-------|
| APP_NAME | Quản lý Kho |
| APP_ENV | production |
| APP_DEBUG | false |
| APP_KEY | (để trống, sẽ generate sau) |
| APP_URL | https://kho-laravel.onrender.com |
| DB_CONNECTION | pgsql |
| DATABASE_URL | (paste Internal Database URL từ bước 3) |
| SESSION_DRIVER | cookie |
| CACHE_DRIVER | file |

6. Click **Create Web Service**

---

## Bước 5: Generate APP_KEY

Sau khi deploy xong:
1. Vào Web Service → **Shell**
2. Chạy:
```bash
php artisan key:generate --show
```
3. Copy key (dạng `base64:xxxxx`)
4. Vào **Environment** → Sửa `APP_KEY` = key vừa copy
5. Service sẽ tự redeploy

---

## Bước 6: Chạy Migration

Trong Shell của Web Service:
```bash
php artisan migrate --force
php artisan db:seed --force
```

---

## Bước 7: Truy cập website

URL: `https://kho-laravel.onrender.com`

**Tài khoản mặc định:**
- Email: admin@example.com
- Password: password

---

## Lưu ý quan trọng

1. **App ngủ sau 15 phút** - lần đầu truy cập sẽ chậm ~30s
2. **Database free giới hạn 90 ngày** - sau đó cần tạo lại hoặc upgrade
3. **Mỗi lần push GitHub** sẽ tự động redeploy

---

## Troubleshooting

### Lỗi "Application Error"
- Kiểm tra Logs trong Render Dashboard
- Đảm bảo APP_KEY đã được set

### Lỗi Database
- Kiểm tra DATABASE_URL đúng chưa
- Chạy lại `php artisan migrate --force`

### Lỗi 500
- Set `APP_DEBUG=true` tạm thời để xem lỗi chi tiết
- Kiểm tra logs: `php artisan log:tail`
