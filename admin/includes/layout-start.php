<?php
if(!isset($pageTitle)) {
    $pageTitle = 'GoTravel Admin';
}
if(!isset($currentPage)) {
    $currentPage = '';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlentities($pageTitle);?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include('includes/header.php');?>
<div class="admin-shell">
<?php include('includes/sidebarmenu.php');?>
<main class="admin-content">
