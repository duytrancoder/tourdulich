<?php
require_once 'api/core/Database.php';
$db = \Api\Core\Database::getConnection();
$stmt = $db->query("SELECT b.*, p.PackageName FROM tblbooking b JOIN tbltourpackages p ON b.PackageId = p.PackageId WHERE b.UserEmail = 'dy@gmail.com'");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Count with JOIN: " . count($bookings) . "\n";
print_r($bookings);
