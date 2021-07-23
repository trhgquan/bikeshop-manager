# bikeshop-manager
Quản lý cửa hàng xe máy

Đồ án lớn Lập trình Hướng đối tượng (CSC10003)

**Sinh viên thực hiện:** Trần Hoàng Quân - **MSSV:** 19120338.

**GVHD:** Thầy Trần Duy Quang

## Công nghệ sử dụng
- Laravel 8.x

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
```
DB_HOST=<DATABASE HOST>
DB_USERNAME=<DATABASE USERNAME>
DB_PASSWORD=<DATABASE PASSWORD>
```
4. **<ins style="color: red;">NHỚ BẬT MYSQL!</ins>**
5. Tạo database (vào MySQL tạo!)
6. Tạo các bảng liên quan
```
php artisan migrate
```
6. Seed data
```
php artisan db:seed
```