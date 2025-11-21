-- Database: webdulich
-- Created for GoTravel tour travel website

CREATE DATABASE IF NOT EXISTS webdulich DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE webdulich;

-- Users table for storing user registration information
CREATE TABLE tblusers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    EmailId VARCHAR(100) NOT NULL UNIQUE,
    FullName VARCHAR(255) NOT NULL,
    MobileNumber VARCHAR(15),
    Password VARCHAR(255) NOT NULL,
    RegDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdationDate TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tour packages table for storing available tour packages
CREATE TABLE tbltourpackages (
    PackageId INT AUTO_INCREMENT PRIMARY KEY,
    PackageName VARCHAR(255) NOT NULL,
    PackageType VARCHAR(150) NOT NULL,
    PackageLocation VARCHAR(150) NOT NULL,
    PackagePrice DECIMAL(10, 2) NOT NULL,
    PackageFetures TEXT,
    PackageDetails LONGTEXT,
    PackageImage VARCHAR(255),
    CreationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdationDate TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Booking table for storing user tour bookings
CREATE TABLE tblbooking (
    BookingId INT AUTO_INCREMENT PRIMARY KEY,
    PackageId INT NOT NULL,
    UserEmail VARCHAR(100) NOT NULL,
    FromDate DATE NOT NULL,
    ToDate DATE NOT NULL,
    Comment TEXT,
    status INT DEFAULT 0, -- 0=Pending, 1=Confirmed, 2=Cancelled
    RegDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CancelledBy ENUM('u', 'a') NULL, -- u=user, a=admin
    UpdationDate TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (PackageId) REFERENCES tbltourpackages(PackageId) ON DELETE CASCADE,
    FOREIGN KEY (UserEmail) REFERENCES tblusers(EmailId) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Support tickets table for user support requests
CREATE TABLE tblissues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    UserEmail VARCHAR(100) NOT NULL,
    Issue VARCHAR(255) NOT NULL,
    Description TEXT NOT NULL,
    AdminRemark TEXT NULL,
    PostingDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    AdminremarkDate TIMESTAMP NULL,
    FOREIGN KEY (UserEmail) REFERENCES tblusers(EmailId) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for storing static pages content (about us, terms, privacy, etc.)
CREATE TABLE tblpages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(200) NOT NULL UNIQUE, -- Page type identifier (aboutus, terms, privacy, contact)
    detail LONGTEXT, -- Page content
    PostingDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for storing contact form enquiries
CREATE TABLE tblenquiry (
    id INT AUTO_INCREMENT PRIMARY KEY,
    FullName VARCHAR(255) NOT NULL,
    EmailId VARCHAR(100) NOT NULL,
    MobileNumber VARCHAR(15) NOT NULL,
    Subject VARCHAR(300) NOT NULL,
    Description TEXT NOT NULL,
    PostingDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Status INT DEFAULT 0 -- 0=Unread, 1=Read
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for storing admin information
CREATE TABLE tbladmin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    AdminUserName VARCHAR(150) NOT NULL UNIQUE,
    AdminEmailId VARCHAR(150) NOT NULL UNIQUE,
    AdminPassword VARCHAR(255) NOT NULL,
    RegDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdationDate TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table for password reset tokens
CREATE TABLE tblpasswordreset (
    id INT AUTO_INCREMENT PRIMARY KEY,
    EmailId VARCHAR(100) NOT NULL,
    Token VARCHAR(255) NOT NULL,
    CreationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ExpiryDate TIMESTAMP NOT NULL,
    FOREIGN KEY (EmailId) REFERENCES tblusers(EmailId) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for tour packages
INSERT INTO tbltourpackages (PackageName, PackageType, PackageLocation, PackagePrice, PackageFetures, PackageDetails, PackageImage) VALUES
('Du lịch Đà Lạt 3N2Đ', 'Tour du lịch', 'Đà Lạt', 350.00, 'Khách sạn 3 sao, Ăn uống, Xe đưa đón', 'Khám phá thành phố ngàn hoa với các điểm đến nổi tiếng như Thung lũng Tình Yêu, Dinh Bảo Đại, Hồ Xuân Hương...', 'dalat.jpg'),
('Du lịch Nha Trang 4N3Đ', 'Tour biển', 'Nha Trang', 520.00, 'Khách sạn 4 sao, Ăn uống, Xe đưa đón, Vé tham quan', 'Trải nghiệm biển xanh, cát trắng, nắng vàng tại thành phố biển Nha Trang cùng các hoạt động vui chơi hấp dẫn...', 'nhatrang.jpg'),
('Du lịch Sa Pa 3N2Đ', 'Tour du lịch', 'Sa Pa', 420.00, 'Khách sạn 3 sao, Ăn uống, Xe đưa đón', 'Khám phá thị trấn sương mù với các điểm đến như Fansipan, bản làng người H\'Mông, ruộng bậc thang...', 'sapa.jpg'),
('Du lịch Phú Quốc 5N4Đ', 'Tour biển', 'Phú Quốc', 650.00, 'Khách sạn 5 sao, Ăn uống, Xe đưa đón, Vé tham quan', 'Trải nghiệm hòn ngọc của Việt Nam với các bãi biển tuyệt đẹp, khu vui chơi và ẩm thực đặc sản...', 'phuquoc.jpg');

-- Insert sample user data
INSERT INTO tblusers (EmailId, FullName, MobileNumber, Password, RegDate) VALUES
('admin@gmail.com', 'Nguyen Van A', '0123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-01-15 10:30:00'),
('user1@gmail.com', 'Le Thi B', '0987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-02-20 14:20:00'),
('user2@gmail.com', 'Tran Van C', '0876543210', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-03-10 09:15:00');

-- Insert sample booking data
INSERT INTO tblbooking (PackageId, UserEmail, FromDate, ToDate, Comment, status) VALUES
(1, 'user1@gmail.com', '2024-12-15', '2024-12-17', 'Yêu cầu phòng không hút thuốc', 1),
(2, 'user2@gmail.com', '2024-11-20', '2024-11-23', 'Gia đình 4 người, cần phòng lớn', 0),
(3, 'user1@gmail.com', '2024-12-01', '2024-12-03', 'Muốn đi cáp treo Fansipan', 2);

-- Insert sample support ticket data
INSERT INTO tblissues (UserEmail, Issue, Description, PostingDate) VALUES
('user1@gmail.com', 'Trễ giờ xe đón', 'Xe đón tour đi trễ 1 tiếng so với lịch hẹn', '2024-11-15 14:30:00'),
('user2@gmail.com', 'Chất lượng phòng không đúng mô tả', 'Phòng không sạch sẽ như quảng cáo, cần phản ánh', '2024-11-18 09:45:00');

-- Insert sample page data
INSERT INTO tblpages (type, detail) VALUES
('aboutus', '<p>GoTravel là công ty du lịch hàng đầu với hơn 10 năm kinh nghiệm trong lĩnh vực tổ chức tour du lịch trong và ngoài nước.</p><p>Chúng tôi cam kết mang đến cho khách hàng những trải nghiệm du lịch tuyệt vời với chất lượng dịch vụ tốt nhất.</p>'),
('terms', '<p>Khách hàng cần thanh toán đầy đủ trước khi tour bắt đầu.</p><p>Mọi sự thay đổi cần được thông báo trước ít nhất 7 ngày.</p><p>Công ty không chịu trách nhiệm nếu khách hàng vi phạm luật pháp tại địa phương.</p>'),
('privacy', '<p>Chúng tôi cam kết bảo mật thông tin cá nhân của khách hàng.</p><p>Thông tin chỉ được sử dụng cho mục đích phục vụ tour du lịch.</p><p>Chúng tôi không chia sẻ thông tin cho bên thứ ba khi không có sự cho phép.</p>'),
('contact', '<p>Vui lòng liên hệ với chúng tôi theo thông tin dưới đây:</p><p>Địa chỉ: 123 Đường Du Lịch, Quận 1, TP.HCM</p><p>Điện thoại: +84 123 456 789</p><p>Email: info@gotravel.com</p>');

-- Insert sample enquiry data
INSERT INTO tblenquiry (FullName, EmailId, MobileNumber, Subject, Description) VALUES
('Nguyen Van D', 'nguyenvand@gmail.com', '0123456788', 'Yêu cầu báo giá tour Đà Lạt', 'Tôi muốn biết thêm thông tin và giá tour Đà Lạt 4 ngày 3 đêm cho 2 người lớn và 1 trẻ em.', '2024-10-10 10:30:00'),
('Tran Thi E', 'tranthie@gmail.com', '0987654322', 'Hỏi về dịch vụ tour nước ngoài', 'Công ty có tổ chức tour sang các nước Đông Nam Á không? Tôi muốn đi Thái Lan.', '2024-10-15 14:20:00');

-- Insert sample admin user (password is 'admin123' after hashing)
INSERT INTO tbladmin (AdminUserName, AdminEmailId, AdminPassword) VALUES
('admin', 'admin@webdulich.com', '$2y$10$3u1/JXZy7L0w8N4B.058eO6H3K8zJ2w9q5Y7x6v4n1m0p9o8i7u6t5r4s3q2');

-- Create indexes for better performance
CREATE INDEX idx_package_location ON tbltourpackages(PackageLocation);
CREATE INDEX idx_booking_user_email ON tblbooking(UserEmail);
CREATE INDEX idx_booking_package_id ON tblbooking(PackageId);
CREATE INDEX idx_issues_user_email ON tblissues(UserEmail);
CREATE INDEX idx_booking_date ON tblbooking(FromDate, ToDate);
CREATE INDEX idx_users_email ON tblusers(EmailId);
CREATE INDEX idx_enquiry_email ON tblenquiry(EmailId);
CREATE INDEX idx_admin_username ON tbladmin(AdminUserName);
CREATE INDEX idx_pages_type ON tblpages(type);
CREATE INDEX idx_enquiry_status ON tblenquiry(Status);