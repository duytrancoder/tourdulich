<?php
require_once 'api/core/Database.php';
$db = \Api\Core\Database::getConnection();
$stmt = $db->query("SELECT EmailId FROM tblusers WHERE EmailId = 'dy@gmail.com'");
$user = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($user);
