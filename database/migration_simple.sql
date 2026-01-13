-- ============================================
-- DATABASE MIGRATION - Phiên bản đơn giản
-- Chạy từng câu lệnh một trong phpMyAdmin
-- ============================================

-- Chọn database
USE `webdulich`;

-- Bước 1: Tạo bảng wishlist
CREATE TABLE IF NOT EXISTS `tblwishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UserEmail` nvarchar(70) NOT NULL,
  `PackageId` int(11) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_package` (`UserEmail`, `PackageId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bước 2: Thêm cột Avatar
-- Nếu báo lỗi "Duplicate column name" thì bỏ qua, cột đã tồn tại
ALTER TABLE `tblusers` ADD `Avatar` nvarchar(255) DEFAULT NULL;

-- Bước 3: Thêm cột Address
-- Nếu báo lỗi "Duplicate column name" thì bỏ qua, cột đã tồn tại
ALTER TABLE `tblusers` ADD `Address` nvarchar(255) DEFAULT NULL;

-- Bước 4: Thêm cột DateOfBirth
-- Nếu báo lỗi "Duplicate column name" thì bỏ qua, cột đã tồn tại
ALTER TABLE `tblusers` ADD `DateOfBirth` date DEFAULT NULL;

-- Bước 5: Thêm cột Gender
-- Nếu báo lỗi "Duplicate column name" thì bỏ qua, cột đã tồn tại
ALTER TABLE `tblusers` ADD `Gender` nvarchar(10) DEFAULT NULL;

-- Kiểm tra kết quả
SELECT 'Migration completed!' as Message;
