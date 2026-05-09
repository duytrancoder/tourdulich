<?php
require_once 'api/core/Database.php';
$db = \Api\Core\Database::getConnection();
$stmt = $db->query("SELECT b.*, p.PackageName FROM tblbooking b LEFT JOIN tbltourpackages p ON b.PackageId = p.PackageId LIMIT 10");
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($bookings);
