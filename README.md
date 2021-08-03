# bikeshop-manager
Quản lý cửa hàng xe máy

Đồ án lớn Lập trình Hướng đối tượng (CSC10003)

**Sinh viên thực hiện:** Trần Hoàng Quân - **MSSV:** 19120338.

**GVHD:** Thầy Trần Duy Quang

## Công nghệ sử dụng
- [Laravel 8.x](https://laravel.com)
- [Chart.js](https://www.chartjs.org) (vẽ biểu đồ)

## Cài đặt
1. Tải xuống
```
git clone <url repository>
```
2. Chạy Composer để cài framework:
```
composer install
```
3. Vào file `.env` và cài biến môi trường:
- Database:
```
DB_HOST=<DATABASE HOST>
DB_USERNAME=<DATABASE USERNAME>
DB_PASSWORD=<DATABASE PASSWORD>
```
- APP_KEY:

Khởi tạo key:
```
php artisan key:generate
```

Chép key mới tạo, xong paste vào
```
APP_KEY=<key mới tạo>
```

4. **NHỚ BẬT MYSQL!**
5. Tạo database (vào MySQL tạo!)
6. Tạo các bảng liên quan
```
php artisan migrate
```
6. Seed data (chủ yếu là account đầu)
```
php artisan db:seed
```

## Chạy trên domain ở local
 (VD: `bike.test`)

- Sửa file `hosts`, trỏ `bike.test` về `127.0.0.1`.
- Sửa file `.env`:
```
APP_URL=http://bike.test
```
- Lệnh artisan serve có thêm parameter `--host`:
```
php artisan serve --host=bike.test [--port=8000]
```