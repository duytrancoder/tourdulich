<?php
require_once 'api/core/Database.php';
$db = \Api\Core\Database::getConnection();
$stmt = $db->query("DESCRIBE tblbooking");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach($columns as $col) {
    echo $col['Field'] . "\n";
}
