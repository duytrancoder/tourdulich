-- ============================================
-- MIGRATION SCRIPT - Tương thích với MySQL cũ
-- ============================================

USE `webdulich`;

-- Tạo bảng wishlist
DROP TABLE IF EXISTS `tblwishlist`;
CREATE TABLE `tblwishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UserEmail` nvarchar(70) NOT NULL,
  `PackageId` int(11) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_package` (`UserEmail`, `PackageId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm các cột mới vào tblusers (bỏ qua lỗi nếu cột đã tồn tại)
SET @query1 = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_schema = 'webdulich' 
     AND table_name = 'tblusers' 
     AND column_name = 'Avatar') = 0,
    'ALTER TABLE tblusers ADD COLUMN Avatar nvarchar(255) DEFAULT NULL AFTER Password',
    'SELECT "Column Avatar already exists" as Info'
);
PREPARE stmt1 FROM @query1;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;

SET @query2 = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_schema = 'webdulich' 
     AND table_name = 'tblusers' 
     AND column_name = 'Address') = 0,
    'ALTER TABLE tblusers ADD COLUMN Address nvarchar(255) DEFAULT NULL',
    'SELECT "Column Address already exists" as Info'
);
PREPARE stmt2 FROM @query2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

SET @query3 = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_schema = 'webdulich' 
     AND table_name = 'tblusers' 
     AND column_name = 'DateOfBirth') = 0,
    'ALTER TABLE tblusers ADD COLUMN DateOfBirth date DEFAULT NULL',
    'SELECT "Column DateOfBirth already exists" as Info'
);
PREPARE stmt3 FROM @query3;
EXECUTE stmt3;
DEALLOCATE PREPARE stmt3;

SET @query4 = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE table_schema = 'webdulich' 
     AND table_name = 'tblusers' 
     AND column_name = 'Gender') = 0,
    'ALTER TABLE tblusers ADD COLUMN Gender nvarchar(10) DEFAULT NULL',
    'SELECT "Column Gender already exists" as Info'
);
PREPARE stmt4 FROM @query4;
EXECUTE stmt4;
DEALLOCATE PREPARE stmt4;

-- Hiển thị kết quả
SELECT 'Migration completed!' as Status;
