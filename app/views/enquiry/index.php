<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Liên hệ</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
</head>
<body style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('<?php echo BASE_URL; ?>admin/packageimages/tour_halong.webp') no-repeat center center; background-size: cover;">
<?php include ROOT . "/includes/header.php"; ?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1 style="color: #fff">Liên hệ đội ngũ GoTravel</h1>
			<p style="color: #e5e7eb">Gửi câu hỏi về tour, thanh toán hoặc hợp tác. Chúng tôi phản hồi trong 2 giờ.</p>
		</section>
		<section class="card" style="background: transparent; border: none;">
			<!-- Alert box (hiển thị bởi JS, không phải PHP Session) -->
			<div id="enquiry-alert" style="display:none; margin-bottom:1rem;"></div>

			<form id="enquiryForm" class="form-stack">
				<div class="form-grid">
					<div class="form-group">
						<label for="fname" style="color: #fff">Họ và tên</label>
						<input type="text" name="fname" id="fname" placeholder="Nguyễn Văn A" required>
					</div>
					<div class="form-group">
						<label for="email" style="color: #fff">Email</label>
						<input type="email" name="email" id="email" placeholder="ban@example.com" required>
					</div>
					<div class="form-group">
						<label for="mobileno" style="color: #fff">Số điện thoại</label>
						<input type="text" name="mobileno" id="mobileno" maxlength="10" placeholder="10 chữ số" required>
					</div>
					<div class="form-group">
						<label for="subject" style="color: #fff">Chủ đề</label>
						<input type="text" name="subject" id="subject" placeholder="Ví dụ: Tour Đà Lạt" required>
					</div>
				</div>
				<div class="form-group">
					<label for="description" style="color: #fff">Nội dung</label>
					<textarea name="description" id="description" rows="5" placeholder="Chia sẻ chi tiết nhu cầu của bạn" required></textarea>
				</div>
				<button type="submit" id="enquiry-submit-btn" class="btn">Gửi yêu cầu</button>
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
    var form    = document.getElementById('enquiryForm');
    var alertBox = document.getElementById('enquiry-alert');
    var btn     = document.getElementById('enquiry-submit-btn');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        var payload = {
            fname:       document.getElementById('fname').value.trim(),
            email:       document.getElementById('email').value.trim(),
            mobileno:    document.getElementById('mobileno').value.trim(),
            subject:     document.getElementById('subject').value.trim(),
            description: document.getElementById('description').value.trim()
        };

        btn.disabled = true;
        btn.textContent = 'Đang gửi...';
        alertBox.style.display = 'none';

        try {
            var res = await fetch((window.BASE_API_URL || '/tour1/api/') + 'enquiries', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });
            var result = await res.json();

            if (result.success) {
                alertBox.className = 'alert success';
                alertBox.innerHTML = '<strong>Thành công:</strong> ' + result.message;
                alertBox.style.display = 'block';
                form.reset();
                if (typeof showToast === 'function') showToast(result.message, 'success');
            } else {
                var errMsg = result.message || 'Đã xảy ra lỗi.';
                if (result.data && typeof result.data === 'object') {
                    errMsg += '<ul style="margin:0.4rem 0 0 1.2rem;">' +
                        Object.values(result.data).map(function(e){ return '<li>' + e + '</li>'; }).join('') +
                        '</ul>';
                }
                alertBox.className = 'alert error';
                alertBox.innerHTML = '<strong>Lỗi:</strong> ' + errMsg;
                alertBox.style.display = 'block';
            }
        } catch (err) {
            alertBox.className = 'alert error';
            alertBox.innerHTML = '<strong>Lỗi:</strong> Không thể kết nối máy chủ, vui lòng thử lại.';
            alertBox.style.display = 'block';
            console.error(err);
        } finally {
            btn.disabled = false;
            btn.textContent = 'Gửi yêu cầu';
        }
    });
});
</script>
</body>
</html>
