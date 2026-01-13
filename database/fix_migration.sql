-- ============================================
-- FIX DATABASE - Sửa lỗi UpdationDate trước
-- ============================================

USE webdulich;

-- Bước 1: Sửa cột UpdationDate trong tblusers
ALTER TABLE tblusers 
MODIFY COLUMN UpdationDate timestamp NULL DEFAULT NULL ON UPDATE current_timestamp();

-- Bước 2: Tạo bảng wishlist
CREATE TABLE IF NOT EXISTS tblwishlist (
  id int(11) NOT NULL AUTO_INCREMENT,
  UserEmail nvarchar(70) NOT NULL,
  PackageId int(11) NOT NULL,
  CreatedAt timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (id),
  UNIQUE KEY user_package (UserEmail, PackageId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bước 3: Thêm cột Avatar
ALTER TABLE tblusers ADD Avatar nvarchar(255) DEFAULT NULL;

-- Bước 4: Thêm cột Address  
ALTER TABLE tblusers ADD Address nvarchar(255) DEFAULT NULL;

-- Bước 5: Thêm cột DateOfBirth
ALTER TABLE tblusers ADD DateOfBirth date DEFAULT NULL;

-- Bước 6: Thêm cột Gender
ALTER TABLE tblusers ADD Gender nvarchar(10) DEFAULT NULL;

-- Kiểm tra kết quả
SELECT 'Migration completed successfully!' as Status;
DESCRIBE tblwishlist;
DESCRIBE tblusers;
