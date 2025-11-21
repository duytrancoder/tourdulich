<?php
session_start();
include('includes/config.php');
$loginError = '';
if(isset($_POST['login']))
{
	$uname=$_POST['username'];
	$password=md5($_POST['password']);
	$sql ="SELECT UserName,Password FROM admin WHERE UserName=:uname and Password=:password";
	$query= $dbh -> prepare($sql);
	$query-> bindParam(':uname', $uname, PDO::PARAM_STR);
	$query-> bindParam(':password', $password, PDO::PARAM_STR);
	$query-> execute();
	if($query->rowCount() > 0)
	{
		$_SESSION['alogin']=$uname;
		header('location:dashboard.php');
		exit;
	} else{
		$loginError = "Thông tin đăng nhập không hợp lệ";
	}
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel Admin | Đăng nhập</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
	<div class="auth-card">
		<h1>Đăng nhập quản trị</h1>
		<p class="helper-text">Sử dụng thông tin tài khoản được cấp để truy cập hệ thống quản trị.</p>
		<?php if($loginError){?><div class="alert error"><?php echo htmlentities($loginError);?></div><?php } ?>
		<form method="post">
			<div class="form-group">
				<label for="username">Tên đăng nhập</label>
				<input type="text" name="username" id="username" required>
			</div>
			<div class="form-group">
				<label for="password">Mật khẩu</label>
				<input type="password" name="password" id="password" required>
			</div>
			<button type="submit" name="login" class="btn btn-primary">Đăng nhập</button>
		</form>
		<p class="helper-text"><a href="../index.php">← Quay lại trang khách</a></p>
	</div>
	<script src="js/app.js" defer></script>
</body>
</html>