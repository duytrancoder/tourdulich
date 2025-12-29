# Hướng dẫn chạy dự án GoTravel

## Yêu cầu hệ thống

- **PHP**: Phiên bản 7.4 trở lên (khuyến nghị PHP 8.0+)
- **MySQL/MariaDB**: Phiên bản 5.7 trở lên
- **Apache**: Với mod_rewrite được bật
- **XAMPP/WAMP/LAMP**: Hoặc môi trường tương tự

## Các bước cài đặt

### Bước 1: Cài đặt XAMPP (nếu chưa có)

1. Tải XAMPP từ [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Cài đặt XAMPP vào thư mục mặc định (thường là `C:\xampp`)
3. Khởi động Apache và MySQL từ XAMPP Control Panel

### Bước 2: Đặt dự án vào thư mục htdocs

Dự án đã được đặt tại: `C:\xampp\htdocs\tour1`

Nếu bạn muốn đặt ở vị trí khác, hãy đảm bảo cập nhật đường dẫn trong file `.htaccess` và cấu hình Apache.

### Bước 3: Tạo cơ sở dữ liệu

1. Mở phpMyAdmin: Truy cập `http://localhost/phpmyadmin`
2. Tạo database mới:
   - Tên database: `webdulich`
   - Chọn collation: `utf8mb4_unicode_ci`
3. Import file SQL:
   - Chọn database `webdulich` vừa tạo
   - Vào tab "Import"
   - Chọn file `webdulich_db.sql` trong thư mục gốc của dự án
   - Nhấn "Go" để import

### Bước 4: Cấu hình kết nối database (Tùy chọn)

Dự án hỗ trợ sử dụng file `.env` để cấu hình. Tạo file `.env` trong thư mục gốc với nội dung:

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=webdulich
```

**Lưu ý**: Nếu không tạo file `.env`, dự án sẽ sử dụng giá trị mặc định:
- DB_HOST: localhost
- DB_USER: root
- DB_PASS: (trống)
- DB_NAME: webdulich

### Bước 5: Cấu hình Apache

Đảm bảo Apache được cấu hình đúng:

1. Mở file `C:\xampp\apache\conf\httpd.conf`
2. **Bật mod_rewrite:**
   - Tìm dòng: `#LoadModule rewrite_module modules/mod_rewrite.so`
   - Bỏ dấu `#` ở đầu dòng để bật mod_rewrite
3. **Cho phép .htaccess hoạt động:**
   - Tìm đoạn cấu hình cho thư mục htdocs (thường có `<Directory "C:/xampp/htdocs">`)
   - Tìm dòng `AllowOverride None` và đổi thành `AllowOverride All`
   - Tìm dòng `Options Indexes FollowSymLinks` và đổi thành `Options -Indexes +FollowSymLinks` (để tắt directory listing)
4. **Lưu file và khởi động lại Apache**

### Bước 6: Truy cập dự án

#### Trang chủ (Frontend):
```
http://localhost/tour1/
```

#### Trang quản trị (Admin):
```
http://localhost/tour1/admin/index.php
```

**Thông tin đăng nhập admin mặc định:**
- Username: `admin`
- Password: `admin123` (hoặc kiểm tra trong file SQL đã import)

**Lưu ý**: Mật khẩu được mã hóa MD5 trong database. Nếu cần đổi mật khẩu, bạn có thể:
- Sử dụng chức năng "Đổi mật khẩu" trong admin panel
- Hoặc cập nhật trực tiếp trong database với MD5 hash của mật khẩu mới

## Cấu trúc dự án

```
tour1/
├── admin/              # Phần quản trị
│   ├── includes/       # File include cho admin
│   ├── css/            # CSS cho admin
│   ├── js/             # JavaScript cho admin
│   └── *.php           # Các trang quản trị
├── app/
│   ├── controllers/    # Controllers (MVC)
│   ├── models/         # Models (MVC)
│   └── views/          # Views (MVC)
├── core/               # Core classes (App, Controller, Model, Database)
├── includes/           # File include chung
├── public/             # Entry point và assets công khai
│   ├── css/            # CSS cho frontend
│   └── index.php       # Entry point chính
├── js/                 # JavaScript cho frontend
├── .htaccess           # Rewrite rules
└── webdulich_db.sql    # File SQL database
```

## Routing

Dự án sử dụng routing tự động:
- `http://localhost/tour1/` → HomeController@index
- `http://localhost/tour1/package` → PackageController@index
- `http://localhost/tour1/package/details/1` → PackageController@details với ID = 1
- `http://localhost/tour1/user/profile` → UserController@profile

## Xử lý lỗi thường gặp

### Lỗi hiển thị "Index of /tour1" (Directory Listing)
**Nguyên nhân**: Apache đang hiển thị danh sách thư mục thay vì chạy ứng dụng.

**Giải pháp**:
1. Kiểm tra file `index.php` đã có trong thư mục gốc chưa (đã được tạo tự động)
2. Kiểm tra `AllowOverride` trong `httpd.conf` phải là `All`:
   - Mở `C:\xampp\apache\conf\httpd.conf`
   - Tìm `<Directory "C:/xampp/htdocs">`
   - Đảm bảo có dòng: `AllowOverride All`
3. Kiểm tra mod_rewrite đã được bật chưa
4. Khởi động lại Apache
5. Nếu vẫn không được, thử truy cập trực tiếp: `http://localhost/tour1/public/index.php`

### Lỗi 404 Not Found
- Kiểm tra mod_rewrite đã được bật chưa
- Kiểm tra file `.htaccess` có tồn tại không
- Kiểm tra AllowOverride trong httpd.conf phải là `All`

### Lỗi kết nối database
- Kiểm tra MySQL đã khởi động chưa
- Kiểm tra thông tin kết nối trong file `.env` hoặc `includes/config.php`
- Kiểm tra database `webdulich` đã được tạo và import chưa

### Lỗi đường dẫn CSS/JS không tải
- Kiểm tra BASE_URL đã được định nghĩa đúng chưa
- Kiểm tra đường dẫn file có đúng không
- Mở Developer Tools (F12) để xem lỗi cụ thể

### Lỗi session
- Đảm bảo `session_start()` được gọi trước khi sử dụng session
- Kiểm tra quyền ghi của thư mục session (thường là thư mục temp của PHP)

## Phát triển thêm

### Thêm Controller mới:
1. Tạo file trong `app/controllers/YourController.php`
2. Kế thừa từ class `Controller`
3. Tạo các method public để xử lý request
4. Sử dụng `$this->view()` để load view
5. Sử dụng `$this->model()` để load model

### Thêm Model mới:
1. Tạo file trong `app/models/YourModel.php`
2. Kế thừa từ class `Model`
3. Sử dụng `$this->db` để truy cập database

### Thêm View mới:
1. Tạo file trong `app/views/your-folder/your-view.php`
2. Sử dụng `include ROOT . "/includes/header.php"` và `include ROOT . "/includes/footer.php"`

## Hỗ trợ

Nếu gặp vấn đề, hãy kiểm tra:
1. Logs của Apache trong `C:\xampp\apache\logs\error.log`
2. Logs của PHP (bật display_errors trong php.ini để debug)
3. Console của trình duyệt (F12) để xem lỗi JavaScript/CSS

