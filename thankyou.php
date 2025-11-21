<?php
session_start();
error_reporting(0);
include('includes/config.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Thông báo</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include('includes/header.php');?>
<main class="page">
	<div class="container">
		<section class="card" style="text-align:center;">
			<h2>Cảm ơn bạn!</h2>
			<p><?php echo htmlentities($_SESSION['msg']);?></p>
			<div style="margin-top:1.5rem;">
				<a class="btn" href="index.php">Quay lại trang chủ</a>
			</div>
		</section>
	</div>
</main>
<?php include('includes/footer.php');?>
<?php include('includes/signup.php');?>
<?php include('includes/signin.php');?>
<?php include('includes/write-us.php');?>
</body>
</html>
