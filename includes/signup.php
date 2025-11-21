<?php
error_reporting(0);
if(isset($_POST['submit']))
{
$fname=$_POST['fname'];
$mnumber=$_POST['mobilenumber'];
$email=$_POST['email'];
$password=md5($_POST['password']);
$sql="INSERT INTO  tblusers(FullName,MobileNumber,EmailId,Password) VALUES(:fname,:mnumber,:email,:password)";
$query = $dbh->prepare($sql);
$query->bindParam(':fname',$fname,PDO::PARAM_STR);
$query->bindParam(':mnumber',$mnumber,PDO::PARAM_STR);
$query->bindParam(':email',$email,PDO::PARAM_STR);
$query->bindParam(':password',$password,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
$_SESSION['msg']="Bạn đã đăng ký thành công. Bây giờ bạn có thể đăng nhập.";
header('location:thankyou.php');
}
else 
{
$_SESSION['msg']="Có lỗi xảy ra. Vui lòng thử lại.";
header('location:thankyou.php');
}
}
?>
<script>
async function checkAvailability() {
	const emailField = document.getElementById('signup-email');
	const statusField = document.getElementById('user-availability-status');
	if(!emailField || !statusField){
		return;
	}
	const email = emailField.value.trim();
	if(!email){
		statusField.textContent = '';
		return;
	}
	statusField.textContent = 'Đang kiểm tra...';
	try{
		const formData = new FormData();
		formData.append('emailid', email);
		const response = await fetch('check_availability.php', {
			method: 'POST',
			body: formData
		});
		const result = await response.text();
		statusField.innerHTML = result;
	}catch(error){
		statusField.textContent = 'Không thể kiểm tra ngay bây giờ.';
	}
}
</script>

<div class="modal" id="signup-modal" aria-hidden="true">
	<div class="modal__dialog">
		<button class="modal__close" data-modal-close aria-label="Đóng">&times;</button>
		<h3>Tạo tài khoản</h3>
		<p class="helper-text">Chỉ mất vài giây để bắt đầu quản lý lịch trình tour của bạn.</p>
		<form name="signup" method="post" class="form-stack">
			<div class="form-group">
				<label for="signup-name">Họ và tên</label>
				<input type="text" id="signup-name" name="fname" autocomplete="off" required>
			</div>
			<div class="form-group">
				<label for="signup-phone">Số điện thoại</label>
				<input type="text" id="signup-phone" name="mobilenumber" maxlength="10" autocomplete="off" required>
			</div>
			<div class="form-group">
				<label for="signup-email">Email</label>
				<input type="email" id="signup-email" name="email" autocomplete="off" required onblur="checkAvailability()">
				<span id="user-availability-status" class="helper-text"></span>
			</div>
			<div class="form-group">
				<label for="signup-password">Mật khẩu</label>
				<input type="password" id="signup-password" name="password" required>
			</div>
			<button type="submit" name="submit" id="submit" class="btn w-100">Tạo tài khoản</button>
			<p class="helper-text">Khi đăng ký, bạn đồng ý với <a href="page.php?type=terms">Điều khoản</a> và <a href="page.php?type=privacy">Chính sách bảo mật</a>.</p>
		</form>
	</div>
</div>