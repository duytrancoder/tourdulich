-- ============================================
-- SIMPLE DATABASE MIGRATION
-- Chạy script này trong phpMyAdmin
-- ============================================

USE `webdulich`;

-- 1. Tạo bảng wishlist
CREATE TABLE IF NOT EXISTS `tblwishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UserEmail` nvarchar(70) NOT NULL,
  `PackageId` int(11) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_package` (`UserEmail`, `PackageId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Thêm cột Avatar (nếu chưa có)
ALTER TABLE `tblusers` ADD COLUMN IF NOT EXISTS `Avatar` nvarchar(255) DEFAULT NULL AFTER `Password`;

-- 3. Thêm cột Address (nếu chưa có)
ALTER TABLE `tblusers` ADD COLUMN IF NOT EXISTS `Address` nvarchar(255) DEFAULT NULL AFTER `Avatar`;

-- 4. Thêm cột DateOfBirth (nếu chưa có)
ALTER TABLE `tblusers` ADD COLUMN IF NOT EXISTS `DateOfBirth` date DEFAULT NULL AFTER `Address`;

-- 5. Thêm cột Gender (nếu chưa có)
ALTER TABLE `tblusers` ADD COLUMN IF NOT EXISTS `Gender` nvarchar(10) DEFAULT NULL AFTER `DateOfBirth`;

-- Kiểm tra kết quả
SELECT 'Migration completed successfully!' as Status;
DESCRIBE tblwishlist;
DESCRIBE tblusers;
