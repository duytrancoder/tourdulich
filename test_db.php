<?php 
$db = new PDO('mysql:host=localhost;dbname=webdulich', 'root', ''); 
$stmt = $db->query("SELECT 'Hà Nội' LIKE '%ha noi%' as test"); 
var_dump($stmt->fetchColumn()); 
?>
