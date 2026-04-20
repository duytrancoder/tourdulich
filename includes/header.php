<?php
$isLoggedIn = !empty($_SESSION["login"]);
$userEmail  = $isLoggedIn ? htmlentities($_SESSION["login"]) : ''; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<header class="site-header">
	<div class="nav-bar">
		<div class="container nav-bar__content">
			<!-- Logo -->
			<a class="brand" href="<?php echo BASE_URL; ?>">GoTravel</a>

			<!-- Hamburger (mobile) -->
			<button class="nav-toggle" type="button" aria-label="Mở menu" aria-expanded="false">
				<span></span>
				<span></span>
				<span></span>
			</button>

			<!-- Main nav links -->
			<nav class="site-nav" id="siteNav">
				<a href="<?php echo BASE_URL; ?>">Trang chủ</a>
				<a href="<?php echo BASE_URL; ?>page/aboutus">Giới thiệu</a>
				<a href="<?php echo BASE_URL; ?>package">Gói du lịch</a>
				<a href="<?php echo BASE_URL; ?>page/privacy">Chính sách</a>
				<a href="<?php echo BASE_URL; ?>page/terms">Điều khoản</a>
				<?php if ($isLoggedIn): ?>
					<a href="<?php echo BASE_URL; ?>issue">Yêu cầu hỗ trợ</a>
				<?php else: ?>
					<a href="<?php echo BASE_URL; ?>enquiry">Gửi hỏi đáp</a>
				<?php endif; ?>
			</nav>

			<!-- Account area (right side) -->
			<div class="nav-account">
				<?php if ($isLoggedIn): ?>
					<!-- Logged-in: icon + email + dropdown -->
					<div class="nav-user-menu" id="navUserMenu">
						<button class="nav-user-btn" type="button" id="navUserBtn" aria-expanded="false" aria-haspopup="true">
							<i class="fas fa-user-circle nav-user-icon"></i>
							<span class="nav-user-email"><?php echo $userEmail; ?></span>
							<i class="fas fa-chevron-down nav-user-caret"></i>
						</button>
						<div class="nav-user-dropdown" id="navUserDropdown" role="menu">
							<a class="nav-user-item" href="<?php echo BASE_URL; ?>user/account" role="menuitem">
								<i class="fas fa-id-card"></i> Tài khoản của tôi
							</a>
							<a class="nav-user-item nav-user-item--danger" href="<?php echo BASE_URL; ?>user/logout" role="menuitem">
								<i class="fas fa-sign-out-alt"></i> Đăng xuất
							</a>
						</div>
					</div>
				<?php else: ?>
					<!-- Not logged-in: login button -->
					<button class="nav-login-btn btn" type="button" data-modal-target="signin-modal">
						Đăng nhập
					</button>
				<?php endif; ?>
			</div>
		</div>
	</div>
</header>

<?php include ROOT . "/includes/toast-notifications.php"; ?>
<script src="<?php echo BASE_URL; ?>public/js/toast-notifications.js?v=1.0"></script>
<script>
// Account dropdown toggle
(function() {
	var btn = document.getElementById('navUserBtn');
	var dropdown = document.getElementById('navUserDropdown');
	if (!btn || !dropdown) return;

	btn.addEventListener('click', function(e) {
		e.stopPropagation();
		var open = btn.getAttribute('aria-expanded') === 'true';
		btn.setAttribute('aria-expanded', !open);
		dropdown.classList.toggle('is-open', !open);
	});

	document.addEventListener('click', function() {
		if (btn) btn.setAttribute('aria-expanded', 'false');
		if (dropdown) dropdown.classList.remove('is-open');
	});

	dropdown.addEventListener('click', function(e) { e.stopPropagation(); });
})();
</script>
