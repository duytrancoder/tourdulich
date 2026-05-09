<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Quên mật khẩu</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
</head>
<body>
<?php include ROOT . "/includes/header.php"; ?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1>Khôi phục mật khẩu</h1>
			<p>Nhập email đã đăng ký. Chúng tôi sẽ gửi hướng dẫn đặt lại mật khẩu cho bạn.</p>
		</section>
		<section class="card" style="max-width: 480px; margin: 0 auto;">
			<!-- Alert box (hiển thị bởi JS) -->
			<div id="forgot-alert" style="display:none; margin-bottom:1rem;"></div>

			<form id="forgotPasswordForm" class="form-stack">
				<div class="form-group">
					<label for="forgot-email">Email đã đăng ký</label>
					<input type="email" name="email" id="forgot-email" placeholder="you@example.com" required>
				</div>
				<button type="submit" id="forgot-submit-btn" class="btn w-100">Gửi yêu cầu khôi phục</button>
				<p class="helper-text" style="text-align:center; margin-top:0.8rem;">
					<a href="<?php echo BASE_URL; ?>" style="color: var(--brand);">← Quay về trang chủ</a>
				</p>
			</form>
		</section>
	</div>
</main>
<?php include ROOT . "/includes/footer.php"; ?>
<?php include ROOT . "/includes/signup.php"; ?>
<?php include ROOT . "/includes/signin.php"; ?>
<?php include ROOT . "/includes/write-us.php"; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form     = document.getElementById('forgotPasswordForm');
    var alertBox = document.getElementById('forgot-alert');
    var btn      = document.getElementById('forgot-submit-btn');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        var email = document.getElementById('forgot-email').value.trim();

        btn.disabled = true;
        btn.textContent = 'Đang xử lý...';
        alertBox.style.display = 'none';

        try {
            var res = await fetch((window.BASE_API_URL || '/tour1/api/') + 'auth/forgot-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ email: email })
            });
            var result = await res.json();

            // Luôn hiện thông báo thành công (security: không tiết lộ email có tồn tại không)
            alertBox.className = 'alert success';
            alertBox.innerHTML = '<strong>Đã gửi!</strong> ' + (result.message || 'Kiểm tra hộp thư của bạn.');
            alertBox.style.display = 'block';
            form.reset();

            // Redirect về trang chủ sau 3 giây
            setTimeout(function () {
                window.location.href = window.BASE_URL_FROM_PHP || '/tour1/';
            }, 3000);

        } catch (err) {
            alertBox.className = 'alert error';
            alertBox.innerHTML = '<strong>Lỗi:</strong> Không thể kết nối máy chủ, vui lòng thử lại.';
            alertBox.style.display = 'block';
            console.error(err);
        } finally {
            btn.disabled = false;
            btn.textContent = 'Gửi yêu cầu khôi phục';
        }
    });
});
</script>
</body>
</html>
