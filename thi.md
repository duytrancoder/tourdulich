# TỔNG HỢP CODE CHỨC NĂNG ĐẶT TOUR

## MỤC LỤC
1. [Giao diện đặt tour (HTML/CSS)](#1-giao-diện-đặt-tour-htmlcss)
2. [Xử lý đặt tour (PHP Controller)](#2-xử-lý-đặt-tour-php-controller)
3. [Model xử lý database](#3-model-xử-lý-database)
4. [Cấu trúc database](#4-cấu-trúc-database)
5. [Luồng hoạt động](#5-luồng-hoạt-động)

---

## 1. GIAO DIỆN ĐẶT TOUR (HTML/CSS)

### File: `app/views/package/details.php`

**Chức năng**: Hiển thị form đặt tour với các trường nhập liệu (ngày khởi hành, số người, ghi chú)

**Code HTML - Form đặt tour** (Dòng 57-79):

```html
<section class="card">
    <h3>Đặt tour</h3>
    <form name="book" method="post" class="form-stack" action="<?php echo BASE_URL; ?>package/book/<?php echo htmlentities($data["package"]->PackageId); ?>">
        
        <!-- Trường chọn ngày khởi hành -->
        <div class="form-group">
            <label for="departuredate">Ngày khởi hành</label>
            <input type="date" id="departuredate" name="departuredate" required>
        </div>
        
        <!-- Trường nhập số người -->
        <div class="form-group">
            <label for="numberofpeople">Số người</label>
            <input type="number" id="numberofpeople" name="numberofpeople" min="1" max="100" value="1" required>
        </div>
        
        <!-- Trường nhập ghi chú -->
        <div class="form-group">
            <label for="comment">Ghi chú</label>
            <textarea id="comment" name="comment" required placeholder="Nêu thêm yêu cầu cụ thể"></textarea>
        </div>
        
        <!-- Nút đặt tour (hiển thị khác nhau tùy trạng thái đăng nhập) -->
        <?php if (!empty($_SESSION["login"])): ?>
            <button type="submit" name="submit2" class="btn">Đặt tour</button>
        <?php else: ?>
            <a class="btn btn-ghost" href="#" data-modal-target="signin-modal">Đăng nhập để đặt tour</a>
        <?php endif; ?>
    </form>
</section>
```

**Giải thích các thành phần**:
- **Form action**: Gửi dữ liệu đến `package/book/{PackageId}` bằng phương thức POST
- **departuredate**: Input type="date" để chọn ngày khởi hành
- **numberofpeople**: Input type="number" với giới hạn min=1, max=100
- **comment**: Textarea để nhập ghi chú/yêu cầu đặc biệt
- **submit2**: Tên của nút submit, được kiểm tra trong controller
- **Điều kiện hiển thị nút**: Nếu đã đăng nhập hiển thị nút "Đặt tour", nếu chưa hiển thị "Đăng nhập để đặt tour"

---

### CSS - Styling cho form

**File: `public/css/style.css`**

**Code CSS cho form-group** (Dòng 510-519):

```css
.form-group label {
  font-size: 0.9rem;
  font-weight: 600;
  display: block;
  margin-bottom: 0.25rem;
}

.form-stack .form-group {
  margin-bottom: 1rem;
}
```

**Code CSS cho input fields** (Dòng 121-152):

```css
input,
select,
textarea {
  width: 100%;
  padding: 0.75rem 0.9rem;
  border-radius: 0.75rem;
  border: 1px solid var(--border);
  background: #fff;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

input::placeholder {
  color: #94a3b8;
  opacity: 1;
}

select option[disabled][selected] {
  color: #94a3b8;
}

input:focus,
select:focus,
textarea:focus {
  outline: none;
  border-color: var(--brand);
  box-shadow: 0 0 0 3px var(--brand-soft);
}

textarea {
  min-height: 140px;
  resize: vertical;
}
```

**Code CSS cho nút "Đặt tour"** (Dòng 167-216):

```css
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  border-radius: 50px;
  padding: 0.875rem 2rem;
  font-weight: 600;
  font-size: 0.95rem;
  border: none;
  cursor: pointer;
  background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
  color: #fff;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 12px rgba(255, 127, 80, 0.25);
  letter-spacing: 0.02em;
}

.btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(255, 127, 80, 0.35);
  background: linear-gradient(135deg, var(--accent-light) 0%, var(--accent) 100%);
  color: #fff;
}

.btn:active {
  transform: translateY(0);
  box-shadow: 0 2px 8px rgba(255, 127, 80, 0.25);
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  box-shadow: none;
  transform: none;
}

.btn-ghost {
  background: transparent;
  color: var(--brand);
  border: 2px solid var(--brand);
  box-shadow: none;
}

.btn-ghost:hover {
  background: var(--brand);
  color: #fff;
  border-color: var(--brand);
  box-shadow: 0 4px 12px rgba(0, 90, 156, 0.25);
}
```

**Code CSS cho card container** (Dòng 424-441):

```css
.card {
  background: var(--card);
  border-radius: 1.5rem;
  padding: 1.75rem;
  border: 1px solid var(--border);
  box-shadow: 0 8px 24px rgba(44, 62, 80, 0.08);
  transition: all 0.3s ease;
}

.card:hover {
  box-shadow: 0 12px 32px rgba(44, 62, 80, 0.12);
  transform: translateY(-2px);
}
```

**Chức năng CSS**:
- **form-group**: Tạo khoảng cách giữa các trường nhập liệu
- **input/textarea**: Styling cho các ô nhập với hiệu ứng focus màu xanh brand
- **.btn**: Nút gradient màu cam với hiệu ứng hover nổi lên
- **.btn-ghost**: Nút viền trong suốt cho trường hợp chưa đăng nhập
- **.card**: Container với bo góc, shadow và hiệu ứng hover

---

## 2. XỬ LÝ ĐẶT TOUR (PHP CONTROLLER)

### File: `app/controllers/PackageController.php`

**Chức năng**: Xử lý logic khi người dùng submit form đặt tour

**Code PHP - Hàm book()** (Dòng 43-111):

```php
public function book($id = 0) {
    // 1. KIỂM TRA ĐĂNG NHẬP
    if (strlen($_SESSION['login']) == 0) {
        $_SESSION['error'] = "Vui lòng đăng nhập để đặt tour";
        header('Location: ' . BASE_URL);
        exit;
    }

    // 2. XỬ LÝ KHI FORM ĐƯỢC SUBMIT
    if (isset($_POST['submit2'])) {
        // 2.1. Lấy dữ liệu từ form
        $pid = intval($id);  // Package ID từ URL
        $useremail = $_SESSION['login'];  // Email người dùng từ session
        $departureDate = trim($_POST['departuredate'] ?? '');  // Ngày khởi hành
        $numberofpeople = intval($_POST['numberofpeople'] ?? 1);  // Số người
        $comment = trim($_POST['comment'] ?? '');  // Ghi chú

        // 2.2. VALIDATE DỮ LIỆU NHẬP VÀO
        
        // Kiểm tra ngày khởi hành không được để trống
        if (empty($departureDate)) {
            $_SESSION['error'] = "Vui lòng chọn ngày khởi hành";
            header('Location: ' . BASE_URL . 'package/details/' . $pid);
            exit;
        }

        // Kiểm tra số người trong khoảng 1-100
        if ($numberofpeople < 1 || $numberofpeople > 100) {
            $_SESSION['error'] = "Số người phải từ 1 đến 100";
            header('Location: ' . BASE_URL . 'package/details/' . $pid);
            exit;
        }

        // Kiểm tra định dạng ngày hợp lệ
        $departureTimestamp = strtotime($departureDate);
        $todayTimestamp = strtotime('today');

        if ($departureTimestamp === false) {
            $_SESSION['error'] = "Ngày không hợp lệ";
            header('Location: ' . BASE_URL . 'package/details/' . $pid);
            exit;
        }

        // Kiểm tra ngày khởi hành không được là ngày trong quá khứ
        if ($departureTimestamp < $todayTimestamp) {
            $_SESSION['error'] = "Ngày khởi hành không thể là ngày trong quá khứ";
            header('Location: ' . BASE_URL . 'package/details/' . $pid);
            exit;
        }

        // Kiểm tra Package ID hợp lệ
        if ($pid <= 0) {
            $_SESSION['error'] = "Gói tour không hợp lệ";
            header('Location: ' . BASE_URL . 'package');
            exit;
        }

        // 2.3. TÍNH TỔNG GIÁ
        $packageModel = $this->model('PackageModel');
        $package = $packageModel->getPackageById($pid);
        $totalprice = $package->PackagePrice * $numberofpeople;

        // 2.4. LƯU VÀO DATABASE
        // Sử dụng cùng ngày cho fromdate và todate để tương thích database
        $bookingModel = $this->model('BookingModel');
        $lastInsertId = $bookingModel->createBooking(
            $pid, 
            $useremail, 
            $departureDate, 
            $departureDate,  // ToDate = FromDate
            $comment, 
            $numberofpeople, 
            $totalprice
        );

        // 2.5. THÔNG BÁO KẾT QUẢ
        if ($lastInsertId) {
            $_SESSION['msg'] = "Đặt tour thành công.";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra. Vui lòng thử lại";
        }
        
        // Redirect về trang chi tiết gói tour
        header('Location: ' . BASE_URL . 'package/details/' . $pid);
        exit;
    }
    
    // Nếu không phải POST request, redirect về danh sách tour
    header('Location: ' . BASE_URL . 'package');
    exit;
}
```

**Giải thích luồng xử lý**:

1. **Kiểm tra đăng nhập**: Nếu chưa đăng nhập → redirect về trang chủ với thông báo lỗi
2. **Lấy dữ liệu**: Thu thập dữ liệu từ form POST và session
3. **Validate dữ liệu**:
   - Ngày khởi hành không được trống
   - Số người phải từ 1-100
   - Ngày phải hợp lệ và không được là quá khứ
   - Package ID phải hợp lệ
4. **Tính tổng giá**: Lấy giá gói tour × số người
5. **Lưu database**: Gọi BookingModel để insert vào bảng tblbooking
6. **Thông báo**: Set session message (success/error) và redirect

---

## 3. MODEL XỬ LÝ DATABASE

### File: `app/models/BookingModel.php`

**Chức năng**: Tương tác với database để lưu thông tin đặt tour

**Code PHP - Hàm createBooking()** (Dòng 6-20):

```php
public function createBooking($pid, $useremail, $fromdate, $todate, $comment, $numberofpeople = 1, $totalprice = 0) {
    // 1. Thiết lập trạng thái mặc định (0 = Pending/Chờ xác nhận)
    $status = 0;
    
    // 2. Chuẩn bị câu SQL INSERT
    $sql = "INSERT INTO tblbooking(
        PackageId,
        UserEmail,
        FromDate,
        ToDate,
        Comment,
        NumberOfPeople,
        TotalPrice,
        status
    ) VALUES(
        :pid,
        :useremail,
        :fromdate,
        :todate,
        :comment,
        :numberofpeople,
        :totalprice,
        :status
    )";
    
    // 3. Chuẩn bị và bind parameters (bảo mật SQL Injection)
    $query = $this->db->prepare($sql);
    $query->bindParam(':pid', $pid, PDO::PARAM_INT);
    $query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
    $query->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
    $query->bindParam(':todate', $todate, PDO::PARAM_STR);
    $query->bindParam(':comment', $comment, PDO::PARAM_STR);
    $query->bindParam(':numberofpeople', $numberofpeople, PDO::PARAM_INT);
    $query->bindParam(':totalprice', $totalprice, PDO::PARAM_STR);
    $query->bindParam(':status', $status, PDO::PARAM_INT);
    
    // 4. Thực thi query
    $query->execute();
    
    // 5. Trả về ID của booking vừa tạo
    return $this->db->lastInsertId();
}
```

**Giải thích**:
- **Parameters**: Nhận 7 tham số (PackageId, Email, FromDate, ToDate, Comment, NumberOfPeople, TotalPrice)
- **Status**: Mặc định = 0 (Pending - chờ admin xác nhận)
- **PDO Prepared Statement**: Sử dụng bindParam để tránh SQL Injection
- **Return**: Trả về BookingId của bản ghi vừa insert (dùng để kiểm tra thành công)

---

**Code PHP - Hàm getBookingsByUserEmail()** (Dòng 22-28):

```php
public function getBookingsByUserEmail($email) {
    // Lấy danh sách tất cả booking của user kèm thông tin package
    $sql = "SELECT 
        tblbooking.BookingId as bookid,
        tblbooking.PackageId as pkgid,
        tbltourpackages.PackageName as packagename,
        tbltourpackages.PackagePrice as packageprice,
        tblbooking.FromDate as fromdate,
        tblbooking.ToDate as todate,
        tblbooking.Comment as comment,
        tblbooking.status as status,
        tblbooking.RegDate as regdate,
        tblbooking.CancelledBy as cancelby,
        tblbooking.UpdationDate as upddate,
        tblbooking.CancelReason as cancelreason,
        tblbooking.CustomerMessage as customermessage
    FROM tblbooking 
    JOIN tbltourpackages ON tbltourpackages.PackageId = tblbooking.PackageId 
    WHERE UserEmail = :email 
    ORDER BY tblbooking.RegDate DESC";
    
    $query = $this->db->prepare($sql);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->execute();
    
    return $query->fetchAll(PDO::FETCH_OBJ);
}
```

**Chức năng**: Lấy lịch sử đặt tour của user để hiển thị trong trang "Lịch sử đặt tour"

---

## 4. CẤU TRÚC DATABASE

### File: `database.sql`

**Bảng: tblbooking** (Dòng 82-101):

```sql
CREATE TABLE `tblbooking` (
  `BookingId` int(11) NOT NULL AUTO_INCREMENT,
  `PackageId` int(11) NOT NULL,
  `UserEmail` varchar(100) NOT NULL,
  `FromDate` varchar(100) NOT NULL,
  `ToDate` varchar(100) NOT NULL,
  `Comment` mediumtext NOT NULL,
  `NumberOfPeople` int(11) NOT NULL,
  `TotalPrice` decimal(10,2) NOT NULL,
  `AdminNotes` mediumtext DEFAULT NULL,
  `CancelReason` mediumtext DEFAULT NULL,
  `RegDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL,
  `CancelledBy` varchar(5) DEFAULT NULL,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `CustomerMessage` mediumtext DEFAULT NULL,
  PRIMARY KEY (`BookingId`),
  KEY `idx_user_email` (`UserEmail`),
  KEY `idx_package` (`PackageId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Giải thích các cột**:

| Cột | Kiểu dữ liệu | Mô tả |
|-----|--------------|-------|
| `BookingId` | int(11) AUTO_INCREMENT | ID đặt tour (Primary Key) |
| `PackageId` | int(11) NOT NULL | ID gói tour được đặt |
| `UserEmail` | varchar(100) NOT NULL | Email người đặt tour |
| `FromDate` | varchar(100) NOT NULL | **Ngày khởi hành** (từ form) |
| `ToDate` | varchar(100) NOT NULL | Ngày kết thúc (hiện tại = FromDate) |
| `Comment` | mediumtext NOT NULL | **Ghi chú** của khách hàng |
| `NumberOfPeople` | int(11) NOT NULL | **Số người** tham gia |
| `TotalPrice` | decimal(10,2) NOT NULL | Tổng giá (PackagePrice × NumberOfPeople) |
| `AdminNotes` | mediumtext | Ghi chú của admin |
| `CancelReason` | mediumtext | Lý do hủy tour |
| `RegDate` | timestamp | Thời gian đặt tour (tự động) |
| `status` | int(11) NOT NULL | Trạng thái: 0=Pending, 1=Confirmed, 2=Cancelled |
| `CancelledBy` | varchar(5) | Người hủy: 'u'=User, 'a'=Admin |
| `UpdationDate` | timestamp | Thời gian cập nhật cuối |
| `CustomerMessage` | mediumtext | Tin nhắn từ admin gửi cho khách |

**Foreign Keys** (Dòng 155-157):

```sql
ALTER TABLE `tblbooking`
  ADD CONSTRAINT `fk_booking_package` FOREIGN KEY (`PackageId`) 
      REFERENCES `tbltourpackages` (`PackageId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`UserEmail`) 
      REFERENCES `tblusers` (`EmailId`) ON DELETE CASCADE;
```

**Ý nghĩa**:
- Khi xóa package → tự động xóa các booking liên quan
- Khi xóa user → tự động xóa các booking của user đó

---

## 5. LUỒNG HOẠT ĐỘNG

### 5.1. Khi người dùng truy cập trang chi tiết tour

```
1. User truy cập: /package/details/{PackageId}
2. PackageController->details() được gọi
3. Lấy thông tin package từ database
4. Render view details.php với form đặt tour
5. Hiển thị:
   - Nếu đã đăng nhập: Nút "Đặt tour"
   - Nếu chưa đăng nhập: Nút "Đăng nhập để đặt tour"
```

### 5.2. Khi người dùng điền form và ấn "Đặt tour"

```
1. User điền:
   - Ngày khởi hành (input type="date")
   - Số người (input type="number", min=1, max=100)
   - Ghi chú (textarea)

2. User ấn nút "Đặt tour" (submit form)

3. Form gửi POST request đến: /package/book/{PackageId}

4. PackageController->book() xử lý:
   
   a) Kiểm tra đăng nhập
      - Nếu chưa đăng nhập → redirect về trang chủ với lỗi
   
   b) Validate dữ liệu:
      - Ngày khởi hành không trống
      - Số người từ 1-100
      - Ngày hợp lệ và không phải quá khứ
      - Package ID hợp lệ
   
   c) Tính tổng giá:
      - Lấy PackagePrice từ database
      - TotalPrice = PackagePrice × NumberOfPeople
   
   d) Lưu vào database:
      - Gọi BookingModel->createBooking()
      - INSERT vào bảng tblbooking với status=0 (Pending)
   
   e) Thông báo kết quả:
      - Thành công: $_SESSION['msg'] = "Đặt tour thành công."
      - Lỗi: $_SESSION['error'] = "Có lỗi xảy ra..."
   
   f) Redirect về trang chi tiết tour để hiển thị thông báo

5. User thấy thông báo thành công/lỗi trên trang chi tiết
```

### 5.3. Dữ liệu được lưu vào database

```sql
INSERT INTO tblbooking(
    PackageId,        -- ID gói tour
    UserEmail,        -- Email từ $_SESSION['login']
    FromDate,         -- Ngày khởi hành từ form
    ToDate,           -- = FromDate
    Comment,          -- Ghi chú từ form
    NumberOfPeople,   -- Số người từ form
    TotalPrice,       -- Giá gói × Số người
    status            -- 0 (Pending)
) VALUES (...)
```

### 5.4. Sau khi đặt tour thành công

```
1. Admin có thể xem booking trong: /admin/manage-bookings.php
2. Admin có thể:
   - Xác nhận (status = 1)
   - Hủy (status = 2)
   - Gửi tin nhắn cho khách (CustomerMessage)

3. User có thể xem lịch sử đặt tour trong: /user/account (tab "Lịch sử đặt tour")
4. User có thể hủy tour (nếu còn > 24h trước ngày khởi hành)
```

---

## 6. TÓM TẮT CÁC FILE LIÊN QUAN

| File | Chức năng |
|------|-----------|
| `app/views/package/details.php` | Giao diện form đặt tour (HTML) |
| `public/css/style.css` | Styling cho form và nút đặt tour |
| `app/controllers/PackageController.php` | Xử lý logic đặt tour (validate, tính giá) |
| `app/models/BookingModel.php` | Tương tác database (INSERT booking) |
| `database.sql` | Cấu trúc bảng tblbooking |

---

## 7. CÁC TRƯỜNG DỮ LIỆU QUAN TRỌNG

### Từ Form (Input của User):
1. **departuredate** (name="departuredate") - Ngày khởi hành
2. **numberofpeople** (name="numberofpeople") - Số người
3. **comment** (name="comment") - Ghi chú

### Được tính toán tự động:
1. **PackageId** - Lấy từ URL
2. **UserEmail** - Lấy từ $_SESSION['login']
3. **TotalPrice** - PackagePrice × NumberOfPeople
4. **status** - Mặc định = 0 (Pending)
5. **RegDate** - Timestamp hiện tại (tự động)

---

## 8. VALIDATION RULES

| Trường | Quy tắc |
|--------|---------|
| Ngày khởi hành | Không trống, phải là ngày hợp lệ, không được là quá khứ |
| Số người | Từ 1 đến 100 |
| Ghi chú | Bắt buộc nhập (required) |
| User | Phải đăng nhập |
| Package ID | Phải tồn tại trong database |

---

## 9. STATUS CODES

| Code | Ý nghĩa | Hiển thị |
|------|---------|----------|
| 0 | Pending | Chờ xác nhận (màu vàng) |
| 1 | Confirmed | Đã xác nhận (màu xanh) |
| 2 | Cancelled | Đã hủy (màu đỏ) |

---

**Tài liệu được tạo bởi**: Antigravity AI  
**Ngày tạo**: 2026-01-17  
**Mục đích**: Tổng hợp code chức năng đặt tour cho dự án Tour Du Lịch
