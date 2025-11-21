<?php
if(session_status() === PHP_SESSION_NONE){
	session_start();
}
if(isset($_POST['signin']))
{
	$email=$_POST['email'];
	$password=md5($_POST['password']);
	$sql ="SELECT EmailId,Password FROM tblusers WHERE EmailId=:email and Password=:password";
	$query= $dbh -> prepare($sql);
	$query-> bindParam(':email', $email, PDO::PARAM_STR);
	$query-> bindParam(':password', $password, PDO::PARAM_STR);
	$query-> execute();
	$results=$query->fetchAll(PDO::FETCH_OBJ);
	if($query->rowCount() > 0)
	{
		$_SESSION['login']=$_POST['email'];
		echo "<script type='text/javascript'> document.location = 'package-list.php'; </script>";
	} else{
		echo "<script>alert('Thông tin không hợp lệ');</script>";
	}
}
?>

<div class="modal" id="signin-modal" aria-hidden="true">
	<div class="modal__dialog">
		<button class="modal__close" data-modal-close aria-label="Đóng">&times;</button>
		<h3>Đăng nhập</h3>
		<p class="helper-text">Truy cập để quản lý lịch sử tour và gửi yêu cầu hỗ trợ.</p>
		<form method="post" class="form-stack">
			<div class="form-group">
				<label for="signin-email">Email</label>
				<input type="email" name="email" id="signin-email" placeholder="you@example.com" required>
			</div>
			<div class="form-group">
				<label for="signin-password">Mật khẩu</label>
				<input type="password" name="password" id="signin-password" required>
			</div>
			<div class="form-group" style="margin-top:-0.5rem;">
				<a class="link-muted" href="forgot-password.php">Quên mật khẩu?</a>
			</div>
			<button type="submit" name="signin" class="btn w-100">Đăng nhập</button>
			<p class="helper-text">Bằng việc đăng nhập, bạn đồng ý với <a href="page.php?type=terms">Điều khoản</a> và <a href="page.php?type=privacy">Chính sách bảo mật</a>.</p>
		</form>
	</div>
</div>