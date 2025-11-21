<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0)
{
	header('location:index.php');
}
else{
if(isset($_POST['submit5']))
{
$password=md5($_POST['password']);
$newpassword=md5($_POST['newpassword']);
$email=$_SESSION['login'];
$sql ="SELECT Password FROM tblusers WHERE EmailId=:email and Password=:password";
$query= $dbh -> prepare($sql);
$query-> bindParam(':email', $email, PDO::PARAM_STR);
$query-> bindParam(':password', $password, PDO::PARAM_STR);
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
$con="update tblusers set Password=:newpassword where EmailId=:email";
$chngpwd = $dbh->prepare($con);
$chngpwd-> bindParam(':email', $email, PDO::PARAM_STR);
$chngpwd-> bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
$chngpwd->execute();
$msg="Cập nhật mật khẩu thành công";
}
else {
$error="Mật khẩu hiện tại không chính xác";
}
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Đổi mật khẩu</title>
	<link rel="stylesheet" href="css/style.css">
	<script>
	function valid(){
		const newPass = document.chngpwd.newpassword.value;
		const confirmPass = document.chngpwd.confirmpassword.value;
		if(newPass !== confirmPass){
			alert("Mật khẩu mới và xác nhận mật khẩu không trùng khớp!");
			document.chngpwd.confirmpassword.focus();
			return false;
		}
		return true;
	}
	</script>
</head>
<body>
<?php include('includes/header.php');?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1>Đổi mật khẩu</h1>
			<p>Giữ an toàn tài khoản bằng cách thay đổi mật khẩu định kỳ.</p>
		</section>
		<?php if($error){?><div class="alert error"><strong>Lỗi:</strong> <?php echo htmlentities($error); ?></div><?php } elseif($msg){?><div class="alert success"><strong>Thành công:</strong> <?php echo htmlentities($msg); ?></div><?php }?>
		<section class="card">
			<form name="chngpwd" method="post" onSubmit="return valid();" class="form-stack">
				<div class="form-group">
					<label for="current-password">Mật khẩu hiện tại</label>
					<input type="password" name="password" id="current-password" required>
				</div>
				<div class="form-grid">
					<div class="form-group">
						<label for="newpassword">Mật khẩu mới</label>
						<input type="password" name="newpassword" id="newpassword" required>
					</div>
					<div class="form-group">
						<label for="confirmpassword">Xác nhận mật khẩu</label>
						<input type="password" name="confirmpassword" id="confirmpassword" required>
					</div>
				</div>
				<button type="submit" name="submit5" class="btn">Cập nhật</button>
			</form>
		</section>
	</div>
</main>
<?php include('includes/footer.php');?>
<?php include('includes/signup.php');?>
<?php include('includes/signin.php');?>
<?php include('includes/write-us.php');?>
</body>
</html>
<?php } ?>
