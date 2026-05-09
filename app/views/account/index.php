<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Tài khoản của tôi</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/account.css?v=1.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/modern-tour-cards.css?v=1.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/tour-card-sample.css?v=1.0">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include ROOT . "/includes/header.php"; ?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1>Tài khoản của tôi</h1>
			<p>Quản lý thông tin cá nhân, lịch sử đặt tour và danh sách yêu thích.</p>
		</section>

		<!-- Thông báo giờ được xử lý bằng Alert/JS -->

		<div class="account-container">
			<!-- Tab Navigation -->
			<nav class="account-tabs">
				<button class="account-tab active" data-tab="profile">
					<i class="fas fa-user"></i>
					<span>Thông tin cá nhân</span>
				</button>
				<button class="account-tab" data-tab="bookings">
					<i class="fas fa-history"></i>
					<span>Lịch sử đặt tour</span>
				</button>
				<button class="account-tab" data-tab="security">
					<i class="fas fa-lock"></i>
					<span>Đổi mật khẩu</span>
				</button>
				<button class="account-tab" data-tab="wishlist">
					<i class="fas fa-heart"></i>
					<span>Tour yêu thích</span>
				</button>
			</nav>

			<!-- Tab Content -->
			<div class="account-content">
				<!-- Profile Tab -->
				<div class="tab-pane active" id="profile-tab">
					<div class="card">
						<h3>Thông tin cá nhân</h3>
						<p class="helper-text">Thông tin này sẽ được tự động điền khi bạn đặt tour.</p>
						
						<div id="profile-tab-content">
							<p style="text-align:center;">Đang tải thông tin...</p>
						</div>
					</div>
				</div>

				<!-- Bookings Tab -->
				<div class="tab-pane" id="bookings-tab">
					<div class="card">
						<h3>Lịch sử đặt tour</h3>
						<p class="helper-text">Theo dõi tất cả các tour bạn đã đặt và trạng thái của chúng.</p>
						
						<div id="bookings-tab-content">
							<p style="text-align:center;">Đang tải dữ liệu...</p>
						</div>
					</div>
				</div>

				<!-- Security Tab -->
				<div class="tab-pane" id="security-tab">
					<div class="card">
						<h3>Đổi mật khẩu</h3>
						<p class="helper-text">Đảm bảo tài khoản của bạn luôn an toàn bằng mật khẩu mạnh.</p>
						
						<form name="changePasswordForm" class="form-stack" id="changePasswordForm">
							<div class="form-group">
								<label for="password">Mật khẩu hiện tại <span class="required">*</span></label>
								<input type="password" name="password" id="password" required>
							</div>
							<div class="form-group">
								<label for="newpassword">Mật khẩu mới <span class="required">*</span></label>
								<input type="password" name="newpassword" id="newpassword" required minlength="6">
								<p class="helper-text">Mật khẩu phải có ít nhất 6 ký tự.</p>
							</div>
							<div class="form-group">
								<label for="confirmpassword">Nhập lại mật khẩu mới <span class="required">*</span></label>
								<input type="password" name="confirmpassword" id="confirmpassword" required minlength="6">
							</div>
							<button type="submit" name="submit5" class="btn">
								<i class="fas fa-lock"></i> Đổi mật khẩu
							</button>
						</form>
					</div>
				</div>

				<!-- Wishlist Tab -->
				<div class="tab-pane" id="wishlist-tab">
					<div class="card">
						<h3>Tour yêu thích</h3>
						<p class="helper-text">Danh sách các tour bạn đã lưu để xem sau.</p>
						
						<div id="wishlist-tab-content">
							<p style="text-align:center;">Đang tải dữ liệu...</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>
<?php include ROOT . "/includes/footer.php"; ?>
<?php include ROOT . "/includes/signup.php"; ?>
<?php include ROOT . "/includes/signin.php"; ?>
<?php include ROOT . "/includes/write-us.php"; ?>
<div class="modal" id="reviewModal" aria-hidden="true">
	<div class="modal__dialog">
		<button type="button" class="modal__close" id="reviewModalClose" aria-label="Đóng">&times;</button>
		<h3>Đánh giá và nhận xét tour</h3>
		<form id="reviewForm" class="form-stack">
			<input type="hidden" name="booking_id" id="reviewBookingId">
			<input type="hidden" name="package_id" id="reviewPackageId">
			<input type="hidden" name="rating" id="reviewRating" value="">
			<div class="form-group">
				<label>Đánh giá sao</label>
				<div class="rating-stars" id="ratingStars" role="radiogroup" aria-label="Chọn số sao">
					<button type="button" class="rating-star" data-value="1" aria-label="1 sao">&#9733;</button>
					<button type="button" class="rating-star" data-value="2" aria-label="2 sao">&#9733;</button>
					<button type="button" class="rating-star" data-value="3" aria-label="3 sao">&#9733;</button>
					<button type="button" class="rating-star" data-value="4" aria-label="4 sao">&#9733;</button>
					<button type="button" class="rating-star" data-value="5" aria-label="5 sao">&#9733;</button>
				</div>
				<p class="helper-text" id="ratingHint">Chọn số sao bạn muốn đánh giá.</p>
			</div>
			<div class="form-group">
				<label for="reviewComment">Nhận xét</label>
				<textarea id="reviewComment" name="comment" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về chuyến đi..."></textarea>
			</div>
			<button type="submit" class="btn w-100">Gửi đánh giá</button>
		</form>
	</div>
</div>
<script>
    // Pass BASE_URL from PHP to JavaScript
    window.BASE_URL_FROM_PHP = '<?php echo BASE_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>public/js/account.js?v=1.0"></script>
<script src="<?php echo BASE_URL; ?>public/js/wishlist.js?v=1.1"></script>
<script src="<?php echo BASE_URL; ?>public/js/review-modal.js?v=1.0"></script>
<script>
	function showCancelReason(event, reason) {
		event.preventDefault();
		alert('Lý do hủy đơn:\n\n' + reason);
	}
</script>
<script src="<?php echo BASE_URL; ?>assets/js/api/account.js?v=1.0"></script>
</body>
</html>
