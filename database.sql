-- ============================================
-- DATABASE WEBDULICH - SCRIPT TỔNG HỢP
-- Tạo database hoàn chỉnh cho dự án Du lịch
-- ============================================

-- 1. TẠO DATABASE (Nếu chưa có) VÀ SỬ DỤNG
CREATE DATABASE IF NOT EXISTS `webdulich` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `webdulich`;

-- 2. THIẾT LẬP MÔI TRƯỜNG
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ============================================
-- DATABASE WEBDULICH - FULL SCHEMA (MySQL/XAMPP)
-- Run this script directly in phpMyAdmin or mysql CLI
-- ============================================

-- 1) CREATE DATABASE & USE
CREATE DATABASE IF NOT EXISTS `webdulich` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `webdulich`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- 2) DROP TABLES (clean import)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `tblitinerary`;
DROP TABLE IF EXISTS `tblwishlist`;
DROP TABLE IF EXISTS `tblbooking`;
DROP TABLE IF EXISTS `tblissues`;
DROP TABLE IF EXISTS `tblenquiry`;
DROP TABLE IF EXISTS `tblpages`;
DROP TABLE IF EXISTS `tbltourpackages`;
DROP TABLE IF EXISTS `tblusers`;
DROP TABLE IF EXISTS `tbladmin`;
SET FOREIGN_KEY_CHECKS = 1;

-- 3) TABLE DEFINITIONS

CREATE TABLE `tbladmin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tblusers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `FullName` varchar(100) NOT NULL,
  `MobileNumber` char(10) NOT NULL,
  `EmailId` varchar(70) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `RegDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `Avatar` varchar(255) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `Gender` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unique` (`EmailId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tbltourpackages` (
  `PackageId` int(11) NOT NULL AUTO_INCREMENT,
  `PackageName` varchar(200) NOT NULL,
  `PackageType` varchar(150) NOT NULL,
  `TourDuration` varchar(100) NOT NULL,
  `PackageLocation` varchar(100) NOT NULL,
  `PackagePrice` int(11) NOT NULL,
  `PackageFetures` varchar(255) NOT NULL,
  `PackageDetails` mediumtext NOT NULL,
  `PackageImage` varchar(100) NOT NULL,
  `Creationdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdationDate` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PackageId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE `tblenquiry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `FullName` varchar(100) NOT NULL,
  `EmailId` varchar(100) NOT NULL,
  `MobileNumber` char(10) NOT NULL,
  `Subject` varchar(100) NOT NULL,
  `Description` mediumtext NOT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Status` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tblissues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UserEmail` varchar(100) NOT NULL,
  `Issue` varchar(100) NOT NULL,
  `Description` mediumtext NOT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `AdminRemark` mediumtext DEFAULT NULL,
  `AdminremarkDate` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tblpages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL DEFAULT '',
  `detail` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tblwishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UserEmail` varchar(70) NOT NULL,
  `PackageId` int(11) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_package` (`UserEmail`,`PackageId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tblitinerary` (
  `ItineraryId` int(11) NOT NULL AUTO_INCREMENT,
  `PackageId` int(11) NOT NULL,
  `TimeLabel` varchar(255) NOT NULL COMMENT 'Time period (e.g., "Ngày 1 - Sáng", "08:00 - 10:00")',
  `Activity` text NOT NULL COMMENT 'Activity description for this time period',
  `SortOrder` int(11) DEFAULT 0 COMMENT 'Display order (lower numbers first)',
  `CreatedAt` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ItineraryId`),
  KEY `PackageId` (`PackageId`),
  KEY `SortOrder` (`SortOrder`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4) FOREIGN KEYS
ALTER TABLE `tblbooking`
  ADD CONSTRAINT `fk_booking_package` FOREIGN KEY (`PackageId`) REFERENCES `tbltourpackages` (`PackageId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`UserEmail`) REFERENCES `tblusers` (`EmailId`) ON DELETE CASCADE;

ALTER TABLE `tblwishlist`
  ADD CONSTRAINT `fk_wishlist_package` FOREIGN KEY (`PackageId`) REFERENCES `tbltourpackages` (`PackageId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`UserEmail`) REFERENCES `tblusers` (`EmailId`) ON DELETE CASCADE;

ALTER TABLE `tblitinerary`
  ADD CONSTRAINT `fk_itinerary_package` FOREIGN KEY (`PackageId`) REFERENCES `tbltourpackages` (`PackageId`) ON DELETE CASCADE;

-- 5) SEED DATA
INSERT INTO `tbladmin` (`id`, `UserName`, `Password`, `updationDate`) VALUES
(1, 'admin', 'f925916e2754e5e03f75dd58a5733251', '2017-05-13 11:18:49');

INSERT INTO `tblusers` (`id`, `FullName`, `MobileNumber`, `EmailId`, `Password`, `Avatar`, `Address`, `DateOfBirth`, `Gender`, `RegDate`, `UpdationDate`) VALUES
(12, 'Le Van Uy', '0763165881', 'leuy26011@gmail.com', '17b9cdbb06619ebae36bfeb59dd89449', NULL, NULL, NULL, NULL, '2025-11-20 04:20:33', NULL),
(13, 'Le Van Uy', '0389378485', 'leuy260105@gmail.com', '258d88e5ebfa52d32ad49bf932146263', NULL, NULL, NULL, NULL, '2025-11-21 03:14:09', NULL);

INSERT INTO `tbltourpackages` (`PackageId`, `PackageName`, `PackageType`, `TourDuration`, `PackageLocation`, `PackagePrice`, `PackageFetures`, `PackageDetails`, `PackageImage`, `Creationdate`, `UpdationDate`) VALUES
(7, 'TOUR VIP 2N1Đ KHÁM PHÁ CHỢ NỔI CÁI RĂNG - TRẢI NGHIỆM TÂY ĐÔ ĐẬM ĐÀ BẢN SẮC', 'Gia Đình', '2 ngày 1 đêm', 'Miền Tây', 100, 'Đưa đón tận tình', 'Nếu bạn chỉ có 2 ngày nhưng vẫn muốn cảm nhận trọn vẹn nét đẹp sông nước miền Tây, Tour Miền Tây 2N1Đ sẽ là lựa chọn phù hợp nhất. Chuyến đi đưa bạn từ khung cảnh miệt vườn xanh mát đến trải nghiệm bữa tối trên du thuyền, ngắm thành phố Cần Thơ lung linh về đêm.', 'tour_mientay.webp', '2025-11-20 04:12:40', '0000-00-00 00:00:00'),
(8, 'Ha Long Bay Instagram Tour: Most Famous Spots', 'Cặp Đôi', '1 ngày', 'Hạ Long', 270, 'Đưa đón tận tình', 'Our Ha Long Bay Tour will take you to the most Instagrammable and adventurous spots in Ha Long Bay all in one day.', 'tour_halong.webp', '2025-11-21 04:00:59', '0000-00-00 00:00:00'),
(9, 'Tour Du Lịch Khám Phá Cao Nguyên Mộc Châu 2N1Đ', 'Gia Đình', '2 ngày 1 đêm', 'Hà Nội - Thác Chiềng Khoa - Đồi Chè - Vườn Hồng Chín - Mộc Châu', 70, 'Phục vụ nhiệt tình', 'Chương trình du lịch cao nguyên Mộc Châu.', 'mocchau.jpg', '2025-11-21 06:59:45', '0000-00-00 00:00:00');

INSERT INTO `tblbooking` (`BookingId`, `PackageId`, `UserEmail`, `FromDate`, `ToDate`, `Comment`, `NumberOfPeople`, `TotalPrice`, `AdminNotes`, `CancelReason`, `RegDate`, `status`, `CancelledBy`, `UpdationDate`, `CustomerMessage`) VALUES
(11, 7, 'leuy26011@gmail.com', '2025-11-02', '2025-11-04', 'Mong sẽ được tận hưởng một chuyến đi trọn vẹn', 2, 200.00, NULL, NULL, '2025-11-20 04:21:14', 1, NULL, '2025-11-21 06:31:48', NULL),
(12, 7, 'leuy26011@gmail.com', '2025-11-30', '2025-12-01', 'Hãy hoàn thiện các dịch vụ giúp tôi', 1, 100.00, NULL, 'Khách hàng có việc đột xuất', '2025-11-21 03:32:39', 2, 'u', '2025-11-21 03:36:53', NULL),
(13, 8, 'leuy26011@gmail.com', '2025-11-28', '2025-11-30', 'Hãy đón tôi đúng giờ', 1, 270.00, NULL, NULL, '2025-11-21 06:47:30', 1, NULL, '2025-11-21 06:48:29', NULL);

INSERT INTO `tblissues` (`id`, `UserEmail`, `Issue`, `Description`, `PostingDate`, `AdminRemark`, `AdminremarkDate`) VALUES
(8, 'leuy26011@gmail.com', 'Tôi gặp sự cố ở tour', 'Hãy cho người tới giúp tôi', '2025-11-21 06:35:01', 'Tôi đồng ý', '2025-11-21 06:48:49');

INSERT INTO `tblpages` (`id`, `type`, `detail`) VALUES
(1, 'terms', 'Chưa có gì'),
(2, 'privacy', 'Nội dung chính sách bảo mật'),
(3, 'aboutus', 'Giới thiệu về công ty'),
(11, 'contact', '0763165881');

-- 6) AUTO INCREMENT FIXES (align with seed IDs)
ALTER TABLE `tbladmin` AUTO_INCREMENT = 2;
ALTER TABLE `tblusers` AUTO_INCREMENT = 14;
ALTER TABLE `tbltourpackages` AUTO_INCREMENT = 10;
ALTER TABLE `tblbooking` AUTO_INCREMENT = 14;
ALTER TABLE `tblenquiry` AUTO_INCREMENT = 8;
ALTER TABLE `tblissues` AUTO_INCREMENT = 9;
ALTER TABLE `tblpages` AUTO_INCREMENT = 22;
ALTER TABLE `tblwishlist` AUTO_INCREMENT = 1;
ALTER TABLE `tblitinerary` AUTO_INCREMENT = 1;

COMMIT;
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblitinerary`
--

CREATE TABLE `tblitinerary` (
  `ItineraryId` int(11) NOT NULL,
  `PackageId` int(11) NOT NULL,
  `TimeLabel` varchar(255) NOT NULL COMMENT 'Time period (e.g., "Ngày 1 - Sáng", "08:00 - 10:00")',
  `Activity` text NOT NULL COMMENT 'Activity description for this time period',
  `SortOrder` int(11) DEFAULT 0 COMMENT 'Display order (lower numbers first)',
  `CreatedAt` timestamp DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tblbooking`
--
ALTER TABLE `tblbooking`
  ADD PRIMARY KEY (`BookingId`);

--
-- Chỉ mục cho bảng `tblenquiry`
--
ALTER TABLE `tblenquiry`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tblissues`
--
ALTER TABLE `tblissues`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tblpages`
--
ALTER TABLE `tblpages`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `tbltourpackages`
--
ALTER TABLE `tbltourpackages`
  ADD PRIMARY KEY (`PackageId`);

--
-- Chỉ mục cho bảng `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `EmailId` (`EmailId`),
  ADD KEY `EmailId_2` (`EmailId`);

--
-- Chỉ mục cho bảng `tblwishlist`
--
ALTER TABLE `tblwishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_package` (`UserEmail`, `PackageId`);

--
-- Chỉ mục cho bảng `tblitinerary`
--
ALTER TABLE `tblitinerary`
  ADD PRIMARY KEY (`ItineraryId`),
  ADD KEY `PackageId` (`PackageId`),
  ADD KEY `SortOrder` (`SortOrder`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `tblbooking`
--
ALTER TABLE `tblbooking`
  MODIFY `BookingId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `tblenquiry`
--
ALTER TABLE `tblenquiry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `tblissues`
--
ALTER TABLE `tblissues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `tblpages`
--
ALTER TABLE `tblpages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT cho bảng `tbltourpackages`
--
ALTER TABLE `tbltourpackages`
  MODIFY `PackageId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `tblwishlist`
--
ALTER TABLE `tblwishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `tblitinerary`
--
ALTER TABLE `tblitinerary`
  MODIFY `ItineraryId` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ràng buộc cho các bảng đã đổ
--

--
-- Ràng buộc cho bảng `tblitinerary`
--
ALTER TABLE `tblitinerary`
  ADD CONSTRAINT `fk_itinerary_package` FOREIGN KEY (`PackageId`) REFERENCES `tbltourpackages` (`PackageId`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
