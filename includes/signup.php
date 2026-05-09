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
		const response = await fetch((window.BASE_API_URL || '/tour1/api/') + 'auth/check-availability', {
			method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
			body: JSON.stringify({ email: email })
		});
		const result = await response.json();
		statusField.textContent = result.message;
		if(result.success && result.data.available){
			document.getElementById('submit').disabled = false;
			statusField.style.color = 'green';
		} else {
			document.getElementById('submit').disabled = true;
			statusField.style.color = 'red';
		}
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
		<form name="signup" class="form-stack">
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
			<p class="helper-text">Khi đăng ký, bạn đồng ý với <a href="<?php echo BASE_URL; ?>page/terms">Điều khoản</a> và <a href="<?php echo BASE_URL; ?>page/privacy">Chính sách bảo mật</a>.</p>
		</form>
	</div>
</div>