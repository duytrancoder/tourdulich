-- Tour Itinerary Table
-- Stores itinerary items (lộ trình) for each tour package

CREATE TABLE IF NOT EXISTS `tblitinerary` (
  `ItineraryId` int(11) NOT NULL AUTO_INCREMENT,
  `PackageId` int(11) NOT NULL,
  `TimeLabel` varchar(255) NOT NULL COMMENT 'Time period (e.g., "Ngày 1 - Sáng", "08:00 - 10:00")',
  `Activity` text NOT NULL COMMENT 'Activity description for this time period',
  `SortOrder` int(11) DEFAULT 0 COMMENT 'Display order (lower numbers first)',
  `CreatedAt` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ItineraryId`),
  KEY `PackageId` (`PackageId`),
  KEY `SortOrder` (`SortOrder`),
  CONSTRAINT `fk_itinerary_package` FOREIGN KEY (`PackageId`) REFERENCES `tbltourpackages` (`PackageId`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Note: Sample data removed. 
-- Please add itinerary items through the admin interface at:
-- Admin > Manage Packages > Quản lý lộ trình
