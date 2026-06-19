# Hướng dẫn Triển khai Hệ thống & Đề cương Ôn thi Vấn đáp (Docker - Git - SVN - Web Server)

Tài liệu này được biên soạn bám sát theo yêu cầu môn học **Quy trình và công cụ phát triển phần mềm - UTT**, hỗ trợ các thành viên trong nhóm thực hiện triển khai hệ thống trên Server Linux (Ubuntu) và ôn tập cho buổi thi vấn đáp trực tiếp.

---

## MỤC LỤC
* [PHẦN I: HƯỚNG DẪN TRIỂN KHAI HỆ THỐNG](#phần-i-hướng-dẫn-triển-khai-hệ-thống)
  * [1. Cấu hình các dịch vụ cơ bản (SSH, FTP/SFTP, SVN)](#1-cấu-hình-các-dịch-vụ-cơ-bản-ssh-ftpsftp-svn)
  * [2. Triển khai Docker và Docker Compose](#2-triển-khai-docker-và-docker-compose)
  * [3. Cấu hình Apache2 Host làm Reverse Proxy & SSL (Tùy chọn)](#3-cấu-hình-apache2-host-làm-reverse-proxy--ssl)
* [PHẦN II: ĐỀ CƯƠNG ÔN TẬP VẤN ĐÁP (THỰC HÀNH TRỰC TIẾP)](#phần-ii-đề-cương-ôn-tập-vấn-đáp-thực-hành-trực-tiếp)
  * [Chủ đề 1: SSH và Truyền file (FTP/SFTP)](#chủ-đề-1-ssh-và-truyền-file-ftpsftp)
  * [Chủ đề 2: Hệ thống quản lý phiên bản SVN](#chủ-đề-2-hệ-thống-quản-lý-phiên-bản-svn)
  * [Chủ đề 3: Sử dụng thành thạo Git & Giải quyết Xung đột](#chủ-đề-3-sử-dụng-thành-thạo-git--giải-quyết-xung-đột)
  * [Chủ đề 4: Kiến thức cốt lõi & Thực hành Docker](#chủ-đề-4-kiến-thức-cốt-lõi--thực-hành-docker)

---

# PHẦN I: HƯỚNG DẪN TRIỂN KHAI HỆ THỐNG

## 1. Cấu hình các dịch vụ cơ bản (SSH, FTP/SFTP, SVN)

Thực hiện cập nhật hệ thống trước khi bắt đầu:
```bash
sudo apt update && sudo apt upgrade -y
```

### 1.1. SSH (Secure Shell)
Mặc định dịch vụ SSH thường đã được cài đặt trên Ubuntu Server. Nếu chưa có, tiến hành cài đặt:
```bash
sudo apt install -y openssh-server
sudo systemctl enable ssh
sudo systemctl start ssh
```

### 1.2. FTP (Vsftpd) & SFTP
#### Cài đặt FTP (vsftpd):
```bash
sudo apt install -y vsftpd
```
Cấu hình file `/etc/vsftpd.conf` để cho phép ghi dữ liệu và giới hạn user trong thư mục của họ:
```bash
sudo nano /etc/vsftpd.conf
```
*Đảm bảo các cấu hình sau được bật (bỏ dấu `#` nếu có):*
```ini
write_enable=YES
local_enable=YES
chroot_local_user=YES
allow_writeable_chroot=YES
```
Khởi động lại dịch vụ:
```bash
sudo systemctl restart vsftpd
```

#### Sử dụng SFTP:
SFTP hoạt động dựa trên giao thức mã hóa SSH (mặc định cổng 22) nên **không cần cài thêm phần mềm gì khác**. Bạn chỉ cần dùng tài khoản SSH để đăng nhập thông qua các client như FileZilla hoặc Bitvise SSH Client.

---

### 1.3. Cài đặt và cấu hình SVN (Subversion) Server
SVN là hệ thống quản lý mã nguồn tập trung (Centralized Version Control). Cài đặt trên Server Ubuntu:
```bash
sudo apt install -y subversion apache2 libapache2-mod-svn
```

#### Tạo Repository chung cho nhóm:
```bash
# Tạo thư mục chứa repos
sudo mkdir -p /var/svn
# Tạo repository có tên là 'gotravel_repo'
sudo svnadmin create /var/svn/gotravel_repo
# Phân quyền cho user apache đọc ghi
sudo chown -R www-data:www-data /var/svn/gotravel_repo
```

#### Tạo tài khoản và phân quyền cho các thành viên:
1. Mở file cấu hình repository:
   ```bash
   sudo nano /var/svn/gotravel_repo/conf/svnserve.conf
   ```
   Bật các dòng sau (bỏ dấu `#` và khoảng trắng ở đầu dòng):
   ```ini
   anon-access = none
   auth-access = write
   password-db = passwd
   authz-db = authz
   ```

2. Thêm tài khoản thành viên trong nhóm vào file `/var/svn/gotravel_repo/conf/passwd`:
   ```bash
   sudo nano /var/svn/gotravel_repo/conf/passwd
   ```
   Thêm tên đăng nhập và mật khẩu dưới phần `[users]`:
   ```ini
   [users]
   user1 = pass123
   user2 = pass456
   admin = admin123
   ```

3. Phân quyền truy cập trong file `/var/svn/gotravel_repo/conf/authz`:
   ```bash
   sudo nano /var/svn/gotravel_repo/conf/authz
   ```
   Phân quyền đọc/ghi cho thư mục gốc của repo:
   ```ini
   [/]
   admin = rw
   user1 = rw
   user2 = rw
   * = r
   ```

4. Khởi động SVN Server lắng nghe ở cổng mặc định 3690:
   ```bash
   sudo svnserve -d -r /var/svn
   ```

---

## 2. Triển khai Docker và Docker Compose

### 2.1. Cài đặt Docker Engine & Docker Compose
```bash
# Gỡ cài đặt phiên bản cũ (nếu có)
sudo apt remove docker docker-engine docker.io containerd runc

# Cài các gói hỗ trợ tải qua HTTPS
sudo apt install -y apt-transport-https ca-certificates curl software-properties-common

# Thêm GPG key của Docker và repository
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Cài đặt Docker
sudo apt update
sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Cấp quyền chạy lệnh docker cho user hiện tại (không cần gõ sudo)
sudo usermod -aG docker $USER
# (Cần Log out và Log in lại server để phân quyền này có hiệu lực)
```

### 2.2. Viết Dockerfile cho Web Server
Tạo tệp `Dockerfile` tại thư mục gốc dự án để đóng gói mã nguồn PHP cùng máy chủ Apache:
```dockerfile
FROM php:8.2-apache

# Cài đặt thư viện hệ thống và các PHP extension cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Kích hoạt module rewrite của Apache (cho file .htaccess)
RUN a2enmod rewrite

# Cho phép ghi đè cấu hình Apache (.htaccess) trong thư mục web
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Thiết lập thư mục làm việc mặc định trong container
WORKDIR /var/www/html

# Copy toàn bộ mã nguồn vào container
COPY . /var/www/html/

# Phân quyền sở hữu thư mục cho Apache user
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
```

### 2.3. Viết tệp docker-compose.yml
Kết nối Web PHP-Apache và Cơ sở dữ liệu MySQL 8.0, gắn Volume lưu trữ dữ liệu bền vững:
```yaml
version: '3.8'

services:
  web:
    build: .
    container_name: gotravel_web
    ports:
      - "8080:80"           # Ánh xạ cổng 8080 của Host vào cổng 80 của Container
    volumes:
      - .:/var/www/html     # Mount mã nguồn thực tế ngoài máy chủ vào container (tiện cho việc code live)
    environment:
      DB_HOST: db
      DB_USER: root
      DB_PASS: rootpassword
      DB_NAME: webdulich
    depends_on:
      - db

  db:
    image: mysql:8.0
    container_name: gotravel_db
    command: --default-authentication-plugin=mysql_native_password
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: webdulich
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql
      # Tự động import database lúc khởi chạy lần đầu
      - ./database.sql:/docker-entrypoint-initdb.d/database.sql

volumes:
  db_data:                 # Tạo Volume độc lập để lưu trữ dữ liệu database bền vững
```

> [!IMPORTANT]
> **Quy tắc triển khai trong môi trường nhiều User cá nhân:**
> Nếu máy chủ có nhiều thành viên cùng chạy Docker, hãy đổi `ports` của Host (ví dụ: `8081:80`, `3307:3306`), đổi `container_name` (ví dụ: `user1_gotravel_web`) và đổi tên volume dữ liệu ở cuối file để tránh xung đột hệ thống.

---

### 2.4. Triển khai và Kiểm tra
Di chuyển vào thư mục dự án trên thư mục cá nhân (ví dụ: `~/tourdulich`) và khởi chạy hệ thống:
```bash
cd ~/tourdulich
docker compose up -d --build
```
Kiểm tra trạng thái các container:
```bash
docker compose ps
```

---

## 3. Cấu hình Apache2 Host làm Reverse Proxy & SSL

Nếu muốn tên miền của mình trỏ trực tiếp đến cổng Docker `8080` (hoặc cổng riêng của user) mà không cần điền cổng trên thanh địa chỉ:

1. Kích hoạt module của Apache trên Host:
   ```bash
   sudo a2enmod proxy proxy_http headers rewrite
   sudo systemctl restart apache2
   ```
2. Tạo file cấu hình Virtual Host tại `/etc/apache2/sites-available/gotravel.conf`:
   ```apache
   <VirtualHost *:80>
       ServerName yourdomain.com
       ServerAlias www.yourdomain.com

       ProxyPreserveHost On
       ProxyPass / http://127.0.0.1:8080/
       ProxyPassReverse / http://127.0.0.1:8080/

       ErrorLog ${APACHE_LOG_DIR}/gotravel_error.log
       CustomLog ${APACHE_LOG_DIR}/gotravel_access.log combined
   </VirtualHost>
   ```
3. Kích hoạt Virtual Host:
   ```bash
   sudo a2ensite gotravel.conf
   sudo systemctl restart apache2
   ```

---
---

# PHẦN II: ĐỀ CƯƠNG ÔN TẬP VẤN ĐÁP (THỰC HÀNH TRỰC TIẾP)

## Chủ đề 1: SSH và Truyền file (FTP/SFTP)

### 1. Cách SSH vào máy chủ Linux:
* **Lệnh chạy trên Terminal / PowerShell:**
  ```bash
  ssh user_name@dia_chi_ip_server
  # Ví dụ: ssh root@192.168.1.100
  ```
  Sau đó nhập mật khẩu máy chủ để truy cập vào shell từ xa.
* **Sử dụng công cụ Bitvise SSH Client:**
  1. Nhập IP Server vào ô **Host**.
  2. Nhập cổng (mặc định **22**) vào ô **Port**.
  3. Nhập **Username** và **Password**.
  4. Nhấn **Log in**. Bitvise sẽ tự động mở một cửa sổ Terminal (SSH) và một cửa sổ SFTP để truyền file.

### 2. Sự khác biệt giữa FTP và SFTP:
| Đặc điểm | FTP (File Transfer Protocol) | SFTP (SSH File Transfer Protocol) |
| :--- | :--- | :--- |
| **Giao thức nền** | TCP gốc (không mã hóa) | SSH (Mã hóa toàn bộ phiên truyền tải) |
| **Độ bảo mật** | **Thấp** (Mật khẩu và dữ liệu truyền dạng văn bản thuần túy) | **Cao** (Tất cả thông tin được mã hóa bảo mật) |
| **Cổng mặc định** | Cổng `21` | Cổng `22` |
| **Cấu hình bổ sung** | Cần cài và cấu hình dịch vụ FTP Server (như `vsftpd`) | Tích hợp sẵn cùng dịch vụ SSH Daemon, không cần cài đặt thêm |

### 3. Thực hành truyền file:
* **FTP:** Sử dụng phần mềm FTP Client (ví dụ FileZilla), điền IP, Username, Password, cổng `21` và thực hiện kéo thả file từ máy cá nhân lên server.
* **SFTP bằng lệnh trên Terminal (dựa trên SSH):**
  ```bash
  # Tải file từ máy local lên server:
  scp /duong_dan_file_local root@192.168.1.100:/var/www/
  
  # Tải thư mục từ máy local lên server (-r):
  scp -r /thu_muc_local root@192.168.1.100:/var/www/
  ```

---

## Chủ đề 2: Hệ thống quản lý phiên bản SVN

### 1. Quy trình làm việc cơ bản với SVN:
SVN là hệ thống quản lý phiên bản tập trung (Centralized Version Control). Tất cả lịch sử thay đổi code đều được lưu tại một kho chứa (Repository) đặt tập trung trên máy chủ SVN Server.

### 2. Các lệnh SVN thực hành trực tiếp:
* **Checkout (Tải code từ Repo server về máy Client lần đầu):**
  ```bash
  svn checkout svn://dia_chi_ip_server/gotravel_repo --username user1
  ```
* **Update (Cập nhật code mới nhất từ Repo server về local trước khi làm việc):**
  ```bash
  svn update
  ```
* **Add (Đánh dấu file mới tạo để đưa vào quản lý):**
  ```bash
  svn add index.php
  ```
* **Commit (Đẩy những thay đổi đã chỉnh sửa lên Server kèm theo mô tả):**
  ```bash
  svn commit -m "Thêm trang liên hệ mới"
  ```
* **Status (Kiểm tra trạng thái thay đổi của các file ở local):**
  ```bash
  svn status
  ```

---

## Chủ đề 3: Sử dụng thành thạo Git & Giải quyết Xung đột

### 1. Bảng tra cứu các lệnh Git cơ bản:
| Lệnh | Ý nghĩa chức năng |
| :--- | :--- |
| `git init` | Khởi tạo một Git repository mới tại thư mục hiện tại |
| `git clone <url>` | Sao chép repository từ GitHub về máy tính local |
| `git status` | Kiểm tra trạng thái thay đổi của các tệp tin trong thư mục |
| `git add <file>` | Thêm tệp tin cụ thể (hoặc `git add .` để thêm toàn bộ) vào vùng chuẩn bị commit (Staging Area) |
| `git commit -m "ghi chú"` | Lưu lại ảnh chụp (snapshot) các thay đổi kèm theo thông điệp mô tả |
| `git push origin <branch>` | Đẩy commit từ repo local lên repository chung trên GitHub |
| `git pull origin <branch>` | Kéo code mới nhất từ GitHub về và tự động gộp (merge) vào mã nguồn local |
| `git branch` | Xem danh sách các nhánh hoặc tạo một nhánh mới (`git branch ten_nhanh`) |
| `git checkout <branch>` | Chuyển đổi sang làm việc trên nhánh được chỉ định |

---

### 2. Cách giải quyết xung đột (Resolve Conflict) khi làm việc nhóm
**Xung đột (Conflict)** xảy ra khi hai người cùng sửa đổi ở cùng một dòng trong một file trên cùng một nhánh, và người thứ nhất đã push lên GitHub trước, người thứ hai pull về sau sẽ bị báo lỗi conflict.

#### Quy trình xử lý xung đột trực tiếp trên máy:

1. **Nhận diện lỗi:** Khi chạy lệnh `git pull`, Git sẽ báo dòng thông tin:
   `CONFLICT (content): Merge conflict in file_name.php`
2. **Mở file bị xung đột:** Khi mở file, Git sẽ tự động chèn các ký tự đánh dấu xung đột:
   ```php
   <<<<<<< HEAD
   // Code hiện tại của bạn ở local
   $db = new Database("localhost");
   =======
   // Code mới nhất từ GitHub do thành viên khác push lên
   $db = new Database("gotravel_db");
   >>>>>>> 45a78d... (mã băm commit của người kia)
   ```
3. **Tiến hành sửa đổi:**
   * Thảo luận với thành viên trong nhóm để chọn giữ lại dòng code nào, hoặc kết hợp cả hai.
   * Xóa bỏ các dòng đánh dấu kỹ thuật (`<<<<<<< HEAD`, `=======`, `>>>>>>>`).
4. **Đánh dấu đã sửa đổi và push lại:**
   ```bash
   git add file_name.php
   git commit -m "Fix merge conflict in file_name.php"
   git push origin main
   ```

---

## Chủ đề 4: Kiến thức cốt lõi & Thực hành Docker

### 1. Phân biệt Container (Docker) và Máy ảo (Virtual Machine - VM):
| Tiêu chí | Máy ảo (VM) | Container (Docker) |
| :--- | :--- | :--- |
| **Kiến trúc hệ thống** | Chạy trên Hypervisor, mỗi VM cần cài **đầy đủ một hệ điều hành khách (Guest OS)** độc lập. | Chia sẻ **chung nhân hệ điều hành Host (Kernel)**, chỉ đóng gói mã nguồn và thư viện ứng dụng cần thiết. |
| **Hiệu năng & Tài nguyên** | **Tốn tài nguyên** (RAM/CPU phải phân bổ cố định cho từng OS khách). | **Rất nhẹ**, khởi động chỉ mất vài giây, chiếm rất ít RAM/CPU của máy chủ. |
| **Tốc độ khởi động** | Chậm (Mất từ vài chục giây đến vài phút để khởi động hệ điều hành khách). | Cực nhanh (Gần như ngay lập tức, tính bằng mili-giây). |
| **Dung lượng ảnh đĩa** | Lớn (Vài GB đến vài chục GB mỗi VM). | Nhỏ (Chỉ từ vài MB đến vài trăm MB). |

```
    MÁY ẢO (VM)                      CONTAINER (DOCKER)
┌──────────────────────┐          ┌──────────────────────┐
│  App A   │   App B   │          │  App A   │   App B   │
├──────────┼───────────┤          ├──────────┼───────────┤
│ Guest OS │ Guest OS  │          │ Libs/Bins│ Libs/Bins │
├──────────┴───────────┤          ├──────────┴───────────┤
│      Hypervisor      │          │    Docker Engine     │
├──────────────────────┤          ├──────────────────────┤
│  Host OS (Kernel)    │          │  Host OS (Shared)    │
├──────────────────────┤          ├──────────────────────┤
│    Phần cứng (HW)    │          │    Phần cứng (HW)    │
└──────────────────────┘          └──────────────────────┘
```

### 2. Ưu điểm nổi bật của Docker trong phát triển phần mềm:
* **Nhất quán môi trường:** Giải quyết triệt để lỗi *"Chạy tốt trên máy tôi nhưng lỗi trên server"*. Code chạy trên máy phát triển thế nào thì khi đem lên server chạy y hệt như vậy.
* **Đóng gói nhanh gọn:** Đóng gói ứng dụng và toàn bộ thư viện liên quan thành một Image duy nhất, dễ dàng chuyển giao.
* **Tối ưu tài nguyên:** Chạy được nhiều container cùng một lúc trên một máy chủ cấu hình yếu.

---

### 3. Thực hành các lệnh Docker thông dụng:
* **`docker build -t <image_name> .`**: Tạo (Build) một Docker Image từ tệp `Dockerfile` tại thư mục hiện tại.
* **`docker run -d -p <host_port>:<container_port> --name <name> <image>`**: Tạo và khởi chạy một container mới từ image ở chế độ chạy ngầm (`-d`), ánh xạ cổng và đặt tên.
* **`docker ps`**: Xem danh sách các container đang hoạt động trên hệ thống. (Sử dụng `docker ps -a` để xem tất cả bao gồm cả container đã dừng).
* **`docker logs <container_name>`**: Xem nhật ký (logs) hoạt động của container để tìm lỗi.
* **`docker exec -it <container_name> bash`** (hoặc `sh`): Truy cập trực tiếp vào shell (dòng lệnh) của container đang chạy để debug, gõ lệnh như một máy chủ Linux thu nhỏ.
* **`docker compose up -d`**: Khởi động toàn bộ các dịch vụ được định nghĩa trong file `docker-compose.yml` cùng lúc ở chế độ chạy ngầm.
* **`docker compose down`**: Dừng và xóa toàn bộ các container, mạng ảo được tạo ra bởi Docker Compose.

---

### 4. Ý nghĩa của Ánh xạ cổng (Port Mapping) và Mount Volume:
* **Ánh xạ cổng (Port Mapping):**
  * *Vì sao cần?* Các container chạy cô lập và có IP nội bộ riêng mà bên ngoài internet không thể kết nối trực tiếp.
  * *Giải pháp:* Dùng tùy chọn `-p 8080:80` để liên kết cổng `8080` của máy chủ vật lý với cổng `80` của container web. Khi người dùng truy cập `http://IP_Server:8080`, yêu cầu sẽ được chuyển tiếp thẳng vào cổng `80` bên trong container.
* **Mount Volume (Gắn ổ đĩa bền vững):**
  * *Vì sao cần?* Dữ liệu tạo ra bên trong container (như cơ sở dữ liệu MySQL) sẽ **hoàn toàn biến mất** khi container bị xóa hoặc build lại (tính chất Stateless).
  * *Giải pháp:* Ánh xạ một thư mục trên máy chủ vật lý vào thư mục lưu trữ dữ liệu của container (Ví dụ: `db_data:/var/lib/mysql`). Khi đó toàn bộ dữ liệu MySQL sẽ được ghi trực tiếp lên ổ cứng của máy chủ, đảm bảo an toàn dữ liệu và không bị mất khi container bị tắt/xóa.

