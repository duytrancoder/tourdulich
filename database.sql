-- ============================================
-- DATABASE WEBDULICH - CLEAN MASTER SCRIPT
-- ============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- 1) CREATE DATABASE & USE
CREATE DATABASE IF NOT EXISTS `webdulich` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `webdulich`;

-- 2) DROP TABLES (clean import)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `tblreviews`;
DROP TABLE IF EXISTS `tblitinerary`;
DROP TABLE IF EXISTS `tblwishlist`;
DROP TABLE IF EXISTS `tblbooking`;
DROP TABLE IF EXISTS `tblissues`;
DROP TABLE IF EXISTS `tblenquiry`;
DROP TABLE IF EXISTS `tblpages`;
DROP TABLE IF EXISTS `tbltourpackages`;
DROP TABLE IF EXISTS `tblusers`;
DROP TABLE IF EXISTS `admin`;
SET FOREIGN_KEY_CHECKS = 1;

-- 3) TABLE DEFINITIONS

CREATE TABLE `admin` (
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
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
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
  `status` int(11) NOT NULL DEFAULT 0,
  `CancelledBy` varchar(5) DEFAULT NULL,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `CustomerMessage` mediumtext DEFAULT NULL,
  PRIMARY KEY (`BookingId`)
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
  `AdminremarkDate` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tblpages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL DEFAULT '',
  `detail` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tblwishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UserEmail` varchar(70) NOT NULL,
  `PackageId` int(11) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_package` (`UserEmail`,`PackageId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tblreviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `BookingId` int(11) NOT NULL,
  `UserEmail` varchar(100) NOT NULL,
  `PackageId` int(11) NOT NULL,
  `Rating` tinyint(1) NOT NULL,
  `Comment` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_booking_review` (`BookingId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tblitinerary` (
  `ItineraryId` int(11) NOT NULL AUTO_INCREMENT,
  `PackageId` int(11) NOT NULL,
  `TimeLabel` varchar(255) NOT NULL,
  `Activity` text NOT NULL,
  `SortOrder` int(11) DEFAULT 0,
  `CreatedAt` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ItineraryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4) SEED DATA

-- Admin
INSERT INTO `admin` (`id`, `UserName`, `Password`) VALUES
(1, 'admin', 'f925916e2754e5e03f75dd58a5733251');

-- Users
INSERT INTO `tblusers` (`id`, `FullName`, `MobileNumber`, `EmailId`, `Password`, `Address`, `DateOfBirth`, `Gender`) VALUES
(101, 'Nguyễn Văn Khoa', '0910000001', 'user01@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Da Nang', '1998-01-01', 'Nam'),
(102, 'Trần Thị Lan', '0910000002', 'user02@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Hue', '1998-01-02', 'Nu'),
(103, 'Phạm Minh Tuấn', '0910000003', 'user03@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Hoi An', '1998-01-03', 'Nam'),
(104, 'Lê Thị Hương', '0910000004', 'user04@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Da Lat', '1998-01-04', 'Nu'),
(105, 'Hoàng Đức Mạnh', '0910000005', 'user05@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Nha Trang', '1998-01-05', 'Nam');

-- Tour Packages (Detailed & Engaging)
INSERT INTO `tbltourpackages` (`PackageId`, `PackageName`, `PackageType`, `TourDuration`, `PackageLocation`, `PackagePrice`, `PackageFetures`, `PackageDetails`, `PackageImage`) VALUES
(201, 'Đà Nẵng Biển Ngọc - Kỳ Nghỉ Sang Trọng', 'Tour cao cấp', '3 ngay 2 dem', 'Bán đảo Sơn Trà - TP. Đà Nẵng', 3100000, 'Dịch vụ xe Limousine đưa đón, Khách sạn view panorama toàn thành phố', 'Khám phá thiên đường giữa lòng đại dương với những rặng san hô rực rỡ sắc màu. Hành trình sẽ đưa bạn đến với những hòn đảo hoang sơ, nơi tiếng sóng vỗ rì rào hòa cùng tiếng gió đại ngàn. Bạn sẽ được thưởng thức những món hải sản tươi sống vừa được đánh bắt và tham gia vào những hoạt động mạo hiểm như lặn biển, chèo kayak.', 'tour01.jpg'),
(202, 'Sắc Màu Cố Đô - Hành Trình Di Sản', 'Tour tiêu chuẩn', '2 ngay 1 dem', 'Kinh thành Huế - Thừa Thiên Huế', 3200000, 'Hướng dẫn viên am hiểu lịch sử, Workshop làm đồ thủ công truyền thống', 'Hành trình tìm về cội nguồn văn hóa dân tộc. Bạn sẽ được tản bộ trên những con phố cổ kính, lắng nghe tiếng chuông chùa vang vọng trong sương sớm và chiêm ngưỡng những công trình kiến trúc di sản nghìn năm tuổi. Đừng quên thử sức mình với các workshop làm đồ thủ công truyền thống để cảm nhận sự tinh xảo trong đôi bàn tay người nghệ nhân.', 'tour02.jpg'),
(203, 'Phố Cổ Hội An - Ánh Sáng Lung Linh', 'Tour riêng', '3 ngay 2 dem', 'Phố cổ Hội An - Quảng Nam', 3300000, 'Trải nghiệm workshop làm lồng đèn, Thưởng thức bữa tối chuẩn Michelin địa phương', 'Khám phá vẻ đẹp huyền ảo của Hội An về đêm. Bạn sẽ được tự tay làm những chiếc đèn lồng xinh xắn, đi thuyền trên sông Hoài và thưởng thức những món ăn đặc sản tinh túy nhất của vùng đất Quảng Nam.', 'tour03.jpg'),
(204, 'Đà Lạt Mùa Sương Sớm - Chill Cùng Thiên Nhiên', 'Tour tiết kiệm', '2 ngay 1 dem', 'Hồ Xuân Hương - TP. Đà Lạt, Lâm Đồng', 3400000, 'Tặng gói chụp ảnh chuyên nghiệp, Nghỉ dưỡng tại homestay sân vườn', 'Nâng tầm kỳ nghỉ của bạn với dịch vụ nghỉ dưỡng cao cấp nhất. Tận hưởng không gian riêng tư tại các resort 5 sao bên bờ biển, thưởng thức bữa tối lãng mạn dưới ánh nến và thư giãn với các liệu trình spa chuyên nghiệp.', 'tour04.jpg'),
(205, 'Nha Trang Làn Biển Xanh - Cuộc Phiêu Lưu Đại Dương', 'Tour tiêu chuẩn', '3 ngay 2 dem', 'Vịnh Nha Trang - Khánh Hòa', 3500000, 'Trải nghiệm lặn biển ngắm san hô độc quyền, Cano cao tốc riêng biệt', 'Chinh phục những cung đường mây ngàn và những đỉnh núi cao vút. Đây là tour dành cho những tâm hồn ưa mạo hiểm, muốn thử thách bản thân và tận hưởng cảm giác tự do giữa đất trời bao la.', 'tour05.jpg');

-- Itinerary
INSERT INTO `tblitinerary` (`ItineraryId`, `PackageId`, `TimeLabel`, `Activity`, `SortOrder`) VALUES
(301, 201, 'Ngày 1 - 08:00', 'Đón khách tại sân bay Đà Nẵng và nhận phòng khách sạn.', 1),
(302, 201, 'Ngày 1 - 14:00', 'Tham quan Bán đảo Sơn Trà và Linh Ứng Tự.', 2),
(303, 202, 'Ngày 1 - 09:00', 'Tham quan Đại Nội Huế và nghe thuyết minh về lịch sử vương triều.', 1),
(304, 202, 'Ngày 1 - 19:00', 'Nghe ca Huế trên sông Hương và thả đèn hoa đăng cầu may.', 2);

-- Bookings
INSERT INTO `tblbooking` (`BookingId`, `PackageId`, `UserEmail`, `FromDate`, `ToDate`, `Comment`, `NumberOfPeople`, `TotalPrice`, `status`) VALUES
(401, 201, 'user01@gmail.com', '2026-06-01', '2026-06-03', 'Chuyến đi kỷ niệm 10 năm ngày cưới.', 2, 6200000.00, 1),
(402, 202, 'user02@gmail.com', '2026-06-02', '2026-06-03', 'Muốn tìm hiểu sâu về văn hóa Huế.', 1, 3200000.00, 1);

-- Reviews
INSERT INTO `tblreviews` (`id`, `BookingId`, `UserEmail`, `PackageId`, `Rating`, `Comment`) VALUES
(1, 401, 'user01@gmail.com', 201, 5, 'Một trải nghiệm thực sự tuyệt vời! Tôi đã đi nhiều nơi nhưng sự chu đáo của GoTravel làm tôi bất ngờ.'),
(2, 402, 'user02@gmail.com', 202, 4, 'Hướng dẫn viên am hiểu kiến thức lịch sử, nói chuyện rất duyên. Tôi đã học được rất nhiều điều mới.');

-- Pages
INSERT INTO `tblpages` (`id`, `type`, `detail`) VALUES
(1, 'terms', 'Nội dung điều khoản dịch vụ...'),
(2, 'privacy', 'Nội dung chính sách bảo mật...'),
(3, 'aboutus', 'GoTravel là đơn vị lữ hành hàng đầu với hơn 10 năm kinh nghiệm...'),
(11, 'contact', 'Hotline: 0910-000-000 | Email: contact@gotravel.com');

-- Auto Increment Fixes
ALTER TABLE `admin` AUTO_INCREMENT = 2;
ALTER TABLE `tblusers` AUTO_INCREMENT = 131;
ALTER TABLE `tbltourpackages` AUTO_INCREMENT = 231;
ALTER TABLE `tblbooking` AUTO_INCREMENT = 441;
ALTER TABLE `tblitinerary` AUTO_INCREMENT = 353;
ALTER TABLE `tblreviews` AUTO_INCREMENT = 50;

COMMIT;
