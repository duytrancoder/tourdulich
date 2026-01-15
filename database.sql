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

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `UserName` nvarchar(100) NOT NULL,
  `Password` nvarchar(100) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`id`, `UserName`, `Password`, `updationDate`) VALUES
(1, 'admin', 'f925916e2754e5e03f75dd58a5733251', '2017-05-13 11:18:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblbooking`
--

CREATE TABLE `tblbooking` (
  `BookingId` int(11) NOT NULL,
  `PackageId` int(11) NOT NULL,
  `UserEmail` nvarchar(100) NOT NULL,
  `FromDate` nvarchar(100) NOT NULL,
  `ToDate` nvarchar(100) NOT NULL,
  `Comment` mediumtext NOT NULL,
  `RegDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL,
  `CancelledBy` nvarchar(5) DEFAULT NULL,
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tblbooking`
--

INSERT INTO `tblbooking` (`BookingId`, `PackageId`, `UserEmail`, `FromDate`, `ToDate`, `Comment`, `RegDate`, `status`, `CancelledBy`, `UpdationDate`) VALUES
(11, 7, 'leuy26011@gmail.com', '2025-11-02', '2025-11-04', 'Mong sẽ được tận hưởng một chuyến đi trọn vẹn', '2025-11-20 04:21:14', 1, NULL, '2025-11-21 06:31:48'),
(12, 7, 'leuy26011@gmail.com', '2025-11-30', '2025-12-01', 'Hãy hoàn thiện các dịch vụ giúp tôi', '2025-11-21 03:32:39', 2, 'u', '2025-11-21 03:36:53'),
(13, 8, 'leuy26011@gmail.com', '2025-11-28', '2025-11-30', 'Hãy đón tôi đúng giờ', '2025-11-21 06:47:30', 1, NULL, '2025-11-21 06:48:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblenquiry`
--

CREATE TABLE `tblenquiry` (
  `id` int(11) NOT NULL,
  `FullName` nvarchar(100) NOT NULL,
  `EmailId` nvarchar(100) NOT NULL,
  `MobileNumber` char(10) NOT NULL,
  `Subject` nvarchar(100) NOT NULL,
  `Description` mediumtext NOT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `Status` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblissues`
--

CREATE TABLE `tblissues` (
  `id` int(11) NOT NULL,
  `UserEmail` nvarchar(100) NOT NULL,
  `Issue` nvarchar(100) NOT NULL,
  `Description` mediumtext NOT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `AdminRemark` mediumtext DEFAULT NULL,
  `AdminremarkDate` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tblissues`
--

INSERT INTO `tblissues` (`id`, `UserEmail`, `Issue`, `Description`, `PostingDate`, `AdminRemark`, `AdminremarkDate`) VALUES
(8, 'leuy26011@gmail.com', 'Tôi gặp sự cố ở tour', 'Hãy cho người tới giúp tôi', '2025-11-21 06:35:01', 'Tôi đồng ý', '2025-11-21 06:48:49');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblpages`
--

CREATE TABLE `tblpages` (
  `id` int(11) NOT NULL,
  `type` nvarchar(255) NOT NULL DEFAULT '',
  `detail` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tblpages`
--

INSERT INTO `tblpages` (`id`, `type`, `detail`) VALUES
(1, 'terms', 'Chưa có gì \r\n'),
(2, 'privacy', '<span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; text-align: justify;\">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat</span>'),
(3, 'aboutus', '<h1 style=\"font-size: 40px; font-weight: 600; line-height: 1.2; color: rgb(27, 31, 59); font-family: Roboto, Arial, sans-serif; text-align: justify;\">Giới thiệu về iVIVU.com</h1><p style=\"margin-bottom: 8px; font-size: 14px; color: rgb(27, 31, 59); font-family: Roboto, Arial, sans-serif;\">&nbsp;</p><p style=\"margin-bottom: 8px; font-size: 14px; color: rgb(27, 31, 59); font-family: Roboto, Arial, sans-serif; text-align: justify;\">Thành lập năm 2010,&nbsp;<span style=\"font-weight: bolder;\">iVIVU.com</span>&nbsp;là thành viên của&nbsp;<span style=\"font-weight: bolder;\">Tập đoàn TMG Việt Nam</span>&nbsp;với hơn 20 năm kinh nghiệm trong lĩnh vực Du lịch – Khách sạn.&nbsp;<span style=\"font-weight: bolder;\">iVIVU.com</span>&nbsp;tiên phong trong việc sáng tạo các sản phẩm du lịch tiện ích dành cho khách hàng nội địa. Liên tục tăng trưởng mạnh qua nhiều năm, iVIVU.com hiện là OTA hàng đầu Việt Nam trong phân khúc cao cấp với hệ thống khoảng 2,500 khách sạn tại Việt Nam &amp; hơn 30,000 khách sạn quốc tế.</p><p style=\"margin-bottom: 8px; font-size: 14px; color: rgb(27, 31, 59); font-family: Roboto, Arial, sans-serif; text-align: justify;\"><br>Với mục tiêu mang đến cho khách hàng một&nbsp;<span style=\"font-weight: bolder;\">"Trải nghiệm kỳ nghỉ tuyệt vời"</span>,&nbsp;<span style=\"font-weight: bolder;\">iVIVU.com</span>&nbsp;kỳ vọng trở thành nền tảng du lịch nghỉ dưỡng số 1 cho khách hàng Đông Nam Á trong 5 năm tới.</p><p style=\"margin-bottom: 8px; font-size: 14px; color: rgb(27, 31, 59); font-family: Roboto, Arial, sans-serif; text-align: justify;\"><br>Dòng sản phẩm chính của iVIVU là&nbsp;<span style=\"font-weight: bolder;\">Combo du lịch</span>&nbsp;- sản phẩm cung cấp đầy đủ cho một kỳ nghỉ bao gồm phòng khách sạn, vé máy bay, đưa đón, ăn uống, khám phá,… chỉ trong 1 lần đặt. Với combo du lịch khách hàng không cần băn khoăn chọn lựa hoặc mất thời gian đặt từng dịch vụ riêng lẻ, lại còn hưởng được mức giá vô cùng tốt với những dòng combo iVIVU.com mang lại:&nbsp;<span style=\"font-weight: bolder;\">combo tiết kiệm, voucher độc quyền, ưu đãi đặt sớm và flash sales</span>.</p><p style=\"margin-bottom: 8px; font-size: 14px; color: rgb(27, 31, 59); font-family: Roboto, Arial, sans-serif; text-align: justify;\"><br>Để đảm bảo cho khách hàng một&nbsp;<span style=\"font-weight: bolder;\">"Trải nghiệm kỳ nghỉ tuyệt vời"</span>, chúng tôi áp dụng công nghệ vào việc đọc hiểu nhu cầu của thị trường, nghiên cứu phát triển sản phẩm và gợi ý những sản phẩm và dịch vụ tốt nhất, phù hợp với từng khách hàng.</p><p style=\"margin-bottom: 8px; font-size: 14px; color: rgb(27, 31, 59); font-family: Roboto, Arial, sans-serif; text-align: justify;\"><br>Khách hàng chọn đặt dịch vụ với iVIVU.com có thể đặt qua rất nhiều kênh:&nbsp;<span style=\"font-weight: bolder;\">Đặt trực tiếp tại website, gọi điện thoại Hotline, đặt qua chat bot, app, đặt qua Facebook và các mạng xã hội khác</span>. Với iVIVU.com mỗi kỳ nghỉ là một trải nghiệm tuyệt vời!</p><p style=\"margin-bottom: 8px; font-size: 14px; color: rgb(27, 31, 59); font-family: Roboto, Arial, sans-serif; text-align: justify;\"><span style=\"font-weight: bolder;\">Vui lòng liên hệ:</span></p><p style=\"margin-bottom: 8px; font-size: 14px; color: rgb(27, 31, 59); font-family: Roboto, Arial, sans-serif; text-align: justify;\">1. Dịch vụ khách hàng, đặt phòng khách sạn: Hotline&nbsp;<span style=\"font-weight: bolder;\">1900 1870</span>&nbsp;– Email:&nbsp;<a href=\"mailto:TC@iVIVU.com\" style=\"color: rgb(47, 128, 237); cursor: pointer;\">TC@iVIVU.com</a></p><p style=\"margin-bottom: 8px; font-size: 14px; color: rgb(27, 31, 59); font-family: Roboto, Arial, sans-serif; text-align: justify;\">2. Nhà cung cấp liên hệ – Email:&nbsp;<a href=\"mailto:Market@ivivu.com\" style=\"color: rgb(47, 128, 237); cursor: pointer;\">Market@ivivu.com</a></p><p style=\"margin-bottom: 8px; font-size: 14px; color: rgb(27, 31, 59); font-family: Roboto, Arial, sans-serif; text-align: justify;\">3. Liên hệ Marketing – Email:&nbsp;<a href=\"mailto:thanhlong.nguyen@ivivu.com\" style=\"color: rgb(47, 128, 237); cursor: pointer;\">marketing@ivivu.com</a></p><p style=\"margin-bottom: 8px; font-size: 14px; color: rgb(27, 31, 59); font-family: Roboto, Arial, sans-serif; text-align: justify;\">4. Các liên hệ khác:&nbsp;<span style=\"font-weight: bolder;\">1900 1870</span></p>'),
(11, 'contact', '<div style=\"text-align: justify;\"><span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px;\">0763165881&nbsp;</span></div>');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tbltourpackages`
--

CREATE TABLE `tbltourpackages` (
  `PackageId` int(11) NOT NULL,
  `PackageName` nvarchar(200) NOT NULL,
  `PackageType` nvarchar(150) NOT NULL,
  `PackageLocation` nvarchar(100) NOT NULL,
  `PackagePrice` int(11) NOT NULL,
  `PackageFetures` nvarchar(255) NOT NULL,
  `PackageDetails` mediumtext NOT NULL,
  `PackageImage` nvarchar(100) NOT NULL,
  `Creationdate` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tbltourpackages`
--

INSERT INTO `tbltourpackages` (`PackageId`, `PackageName`, `PackageType`, `PackageLocation`, `PackagePrice`, `PackageFetures`, `PackageDetails`, `PackageImage`, `Creationdate`, `UpdationDate`) VALUES
(7, 'TOUR VIP 2N1Đ KHÁM PHÁ CHỢ NỔI CÁI RĂNG - TRẢI NGHIỆM TÂY ĐÔ ĐẬM ĐÀ BẢN SẮC', 'Gia Đình', 'Miền Tây ', 100, 'Đưa đón tận tình ', 'Nếu bạn chỉ có 2 ngày nhưng vẫn muốn cảm nhận trọn vẹn nét đẹp sông nước miền Tây, Tour Miền Tây 2N1Đ sẽ là lựa chọn phù hợp nhất. Chuyến đi đưa bạn từ khung cảnh miệt vườn xanh mát đến trải nghiệm bữa tối trên du thuyền, ngắm thành phố Cần Thơ lung linh về đêm.\r\n\r\nTour Miền Tây 2 ngày 1 đêm là một trong những hành trình ngắn nhưng mang lại nhiều trải nghiệm nhất. Chỉ trong hai ngày, bạn sẽ đi qua Mỹ Tho – Bến Tre – Cần Thơ, cảm nhận trọn vẹn vẻ đẹp sông Tiền, sông Hậu, làng nghề xứ Dừa, chợ nổi Cái Răng, cùng những điểm check-in độc đáo như Khu du lịch Mỹ Khánh hay Căn Nhà Màu Tím.\r\n\r\nĐây là hành trình lý tưởng cho gia đình, nhóm bạn hoặc du khách muốn tìm sự bình yên, nhẹ nhàng và trọn vẹn của miền sông nước.', 'tour_mientay.webp', '2025-11-20 04:12:40', '0000-00-00 00:00:00'),
(8, 'Ha Long Bay Instagram Tour: Most Famous Spots', 'Cặp Đôi ', 'Hạ Long ', 270, 'Đưa đón tận tình ', 'Our Ha Long Bay Tour will take you to the most Instagrammable and adventurous spots in Ha Long Bay all in one day. If you are looking for a little bit of a culture, delicious food, and a ton of adventure paired with great photos then this is the tour for you.\r\n\r\nThis tour will depart from Hanoi. The morning will start with a private pickup directly from your hotel or villa by one of our friendly, English speaking guides. From there your tour will begin as you start your drive in one of our comfortable, spacious and air-conditioned vehicles.\r\n\r\nThis private, full-day tour will be fully packed with famous landmarks, secretly located spots, relaxing on the water, Vietnamese coffee and much much more!\r\n\r\nOn this tour you will be able to visit:\r\n\r\nPoem Mountain\r\nVisit the Bay on a private boat\r\nExplore Sung Sot Caves\r\nDiscover Luon Cave on a kayak\r\nHike or swimming at Titop Island\r\n\r\nNo need to worry about planning where to go, waiting for other people, paying for entrance fees or buying lunch. We got you covered as this tour is private and all-inclusive. Our guides will also help you to take some beautiful photos that will look amazing on Instagram.\r\n\r\nIf you\'re looking for an exciting and stress-free day in Ha Long Bay (departing from Hanoi) then this is the tour for you. It's the perfect tour to make memories with loved ones, friends or family.\r\n\r\n', 'tour_halong.webp', '2025-11-21 04:00:59', '0000-00-00 00:00:00'),
(9, 'Tour Du Lịch Khám Phá Cao Nguyên Mộc Châu 2N1Đ', 'Gia Đình ', 'Hà Nội - Thác Chiềng Khoa - Đồi Chè - Vườn Hồng Chín - Mộc Châu Hoa Trái Bốn Mùa - Làng Nguyên Thủy ', 70, 'Phục vụ nhiệt tình ', 'Chương trình du lịch\r\n\r\nCAO NGUYÊN MỘC CHÂU\r\n\r\nHÀ NỘI - THÁC CHIỀNG KHOA - ĐỒI CHÈ - VƯỜN HỒNG CHÍN - HOA TRÁI 4 MÙA - LÀNG NGUYÊN THỦY HANG TÁU -\r\n\r\nCẦU KÍNH BẠCH LONG/ HAPPY LAND\r\n\r\nThời gian: 2 ngày 1 đêm\r\n\r\nKhởi hành: Thứ 7 hàng tuần\r\n\r\nLà điểm đến nổi tiếng trên cung đường Tây Bắc và chỉ cách Hà Nội 180km, cao nguyên Mộc Châu ẩn chứa vẻ đẹp của những đồi chè xanh mướt trải dài, những thung lũng được phủ sắc trắng của hoa cải, hoa mận, sắc hồng của hoa anh đào và rất nhiều các loại hoa trái bốn mùa; kết hợp với những con thác lớn đổ xuống vô cùng hùng vĩ và thơ mộng… đã tạo nên một Mộc Châu đầy cuốn hút mà bất cứ ai một lần đặt chân đến đây đều cảm thấy lưu luyến khó rời.', 'mocchau.jpg', '2025-11-21 06:59:45', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblusers`
--

CREATE TABLE `tblusers` (
  `id` int(11) NOT NULL,
  `FullName` nvarchar(100) NOT NULL,
  `MobileNumber` char(10) NOT NULL,
  `EmailId` nvarchar(70) NOT NULL,
  `Password` nvarchar(100) NOT NULL,
  `Avatar` nvarchar(255) DEFAULT NULL,
  `Address` nvarchar(255) DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `Gender` nvarchar(10) DEFAULT NULL,
  `RegDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tblusers`
--

INSERT INTO `tblusers` (`id`, `FullName`, `MobileNumber`, `EmailId`, `Password`, `Avatar`, `Address`, `DateOfBirth`, `Gender`, `RegDate`, `UpdationDate`) VALUES
(12, 'Lê Văn Uy ', '0763165881', 'leuy26011@gmail.com ', '17b9cdbb06619ebae36bfeb59dd89449', NULL, NULL, NULL, NULL, '2025-11-20 04:20:33', NULL),
(13, 'Lê Văn Uy ', '0389378485', 'leuy260105@gmail.com', '258d88e5ebfa52d32ad49bf932146263', NULL, NULL, NULL, NULL, '2025-11-21 03:14:09', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tblwishlist`
--

CREATE TABLE `tblwishlist` (
  `id` int(11) NOT NULL,
  `UserEmail` nvarchar(70) NOT NULL,
  `PackageId` int(11) NOT NULL,
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
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
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
-- AUTO_INCREMENT cho bảng `admin`
--
ALTER TABLE `admin`
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
