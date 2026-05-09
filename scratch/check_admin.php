<?php
require_once 'api/core/Database.php';
use Api\Core\Database;

try {
    $db = Database::getConnection();
    $stmt = $db->query("SELECT id, UserName, Password FROM admin");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Admins found: " . count($admins) . "\n";
    foreach ($admins as $a) {
        echo "ID: {$a['id']}, User: {$a['UserName']}, Pwd: {$a['Password']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
