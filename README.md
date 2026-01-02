# BACLINK - Hệ sinh thái Doanh nghiệp

Chào mừng đến với dự án BACLINK - Nền tảng kết nối và phát triển doanh nghiệp.

## 🚀 Cài đặt & Khởi chạy

### 1. Yêu cầu hệ thống
*   PHP >= 8.1
*   Composer
*   MySQL/MariaDB
*   Node.js (Optional, cho assets)

### 2. Cài đặt

Clone dự án về máy:
```bash
git clone https://github.com/quanganhbn168/baclink.git
cd baclink
```

Cài đặt các gói phụ thuộc PHP:
```bash
composer install
```

Copy file môi trường và cấu hình Database:
```bash
cp .env.example .env
# Mở file .env và chỉnh sửa thông tin DB_DATABASE, DB_USERNAME, DB_PASSWORD cho phù hợp
```

Tạo key cho ứng dụng:
```bash
php artisan key:generate
```

Chạy migration và seed dữ liệu mẫu (Quan trọng để có Account Admin và Menu):
```bash
php artisan migrate:fresh --seed
```
*Lệnh này sẽ tạo lại database sạch và nạp dữ liệu cần thiết như Tài khoản Admin, Intro, Menu Header/Footer.*

### 3. Thông tin đăng nhập mặc định
*   **Link Admin**: `/admin`
*   **Email**: `admin@baclink.vn`
*   **Password**: `password` (hoặc `12345678` tùy cấu hình seed)

### 4. Cấu hình quyền (Permission) cho aaPanel/VPS
Nếu deploy lên VPS (đặc biệt là aaPanel), bạn cần cấp quyền ghi cho các thư mục hệ thống:

```bash
# Cách 1: Dùng lệnh (trong Terminal)
chmod -R 775 storage bootstrap/cache
chown -R www:www storage bootstrap/cache # Với aaPanel user thường là 'www'
```

*Lưu ý: Trong giao diện aaPanel, bạn có thể vào "Files", tìm đến thư mục code, nhấp chuột phải chọn "Permission", set User là `www` và quyền là `755` cho toàn bộ thư mục.*

---

## 🛠 Hướng dẫn vận hành

### Quản lý Hội viên
*   Truy cập **Admin > Quản lý Hội viên**.
*   Tại đây admin có thể xem danh sách hội viên, tìm kiếm, xem chi tiết hồ sơ doanh nghiệp và xóa hội viên (bao gồm cả tài khoản user) nếu cần.

### Cấu hình Menu & Nội dung
*   Menu Header và Footer được quản lý động. Nếu cần reset lại menu chuẩn, hãy chạy lại seeder: `php artisan db:seed --class=BaclinkSeeder`.
*   Các trang Giới thiệu (Intro) cũng được khởi tạo từ seeder.

### Lưu ý quan trọng
*   Dự án sử dụng Laravel Media Library để quản lý ảnh.
*   Giao diện Admin sử dụng AdminLTE 3.

---

## 🐛 Troubleshooting

Nếu gặp lỗi **403 Forbidden** hoặc **404 Not Found** với assets:
```bash
php artisan storage:link
```

Nếu update code mà giao diện không nhận thay đổi:
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```
