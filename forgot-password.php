<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(isset($_POST['submit']))
{
$contact=$_POST['mobile'];
$email=$_POST['email'];
$newpassword=md5($_POST['newpassword']);
$sql ="SELECT EmailId FROM tblusers WHERE EmailId=:email and MobileNumber=:contact";
$query= $dbh -> prepare($sql);
$query-> bindParam(':email', $email, PDO::PARAM_STR);
$query-> bindParam(':contact', $contact, PDO::PARAM_STR);
$query-> execute();
$results = $query -> fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
$con="update tblusers set Password=:newpassword where EmailId=:email and MobileNumber=:contact";
$chngpwd1 = $dbh->prepare($con);
$chngpwd1-> bindParam(':email', $email, PDO::PARAM_STR);
$chngpwd1-> bindParam(':contact', $contact, PDO::PARAM_STR);
$chngpwd1-> bindParam(':newpassword', $newpassword, PDO::PARAM_STR);
$chngpwd1->execute();
$msg="Đặt lại mật khẩu thành công";
}
else {
$error="Email hoặc số điện thoại không hợp lệ";
}
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Quên mật khẩu</title>
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
			<h1>Khôi phục mật khẩu</h1>
			<p>Nhập email và số điện thoại đã đăng ký để tạo mật khẩu mới.</p>
		</section>
		<?php if($error){?><div class="alert error"><strong>Lỗi:</strong> <?php echo htmlentities($error); ?></div><?php } elseif($msg){?><div class="alert success"><strong>Thành công:</strong> <?php echo htmlentities($msg); ?></div><?php }?>
		<section class="card">
			<form name="chngpwd" method="post" onSubmit="return valid();" class="form-stack">
				<div class="form-group">
					<label for="email">Email đã đăng ký</label>
					<input type="email" name="email" id="email" required>
				</div>
				<div class="form-group">
					<label for="mobile">Số điện thoại</label>
					<input type="text" name="mobile" id="mobile" maxlength="10" required>
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
				<button type="submit" name="submit" class="btn">Cập nhật mật khẩu</button>
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
