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

    <!-- Sync Admin JWT Token to LocalStorage -->
    <script>
        // Cookie Bridge: Đọc token từ cookie và lưu vào localStorage cho Fetch API
        const cookies = document.cookie.split(';');
        let tokenFound = false;
        for(let i=0; i<cookies.length; i++) {
            let c = cookies[i].trim();
            if (c.indexOf('admin_jwt_token=') === 0) {
                localStorage.setItem('jwt_token', c.substring(16));
                tokenFound = true;
                break;
            }
        }
    </script>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>admin/css/style.css">
</head>
<body>
<?php include('includes/header.php');?>
<div class="admin-shell">
<?php include('includes/sidebarmenu.php');?>
<main class="admin-content">
