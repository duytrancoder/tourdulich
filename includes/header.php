<?php
// Trạng thái đăng nhập giờ đây do Frontend Javascript (JWT) quyết định.
?>
<script>
// Dynamic base URLs — injected by PHP, removes hardcoded /tour1/ dependency
window.BASE_URL_FROM_PHP = '<?php echo BASE_URL; ?>';
window.BASE_API_URL = '<?php echo rtrim(BASE_URL, "/"); ?>/api/';
</script>

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
				<a href="<?php echo BASE_URL; ?>issue" id="nav-link-issue" style="display:none;">Yêu cầu hỗ trợ</a>
				<a href="<?php echo BASE_URL; ?>enquiry" id="nav-link-enquiry">Gửi hỏi đáp</a>
			</nav>

			<!-- Account area (right side) -->
			<div class="nav-account" id="auth-container">
				<!-- Not logged-in: login button (Default state) -->
				<button class="nav-login-btn btn" type="button" data-modal-target="signin-modal" id="btn-show-login">
					Đăng nhập
				</button>

				<!-- Logged-in state (Hidden by default, shown via JS) -->
				<div class="nav-user-menu" id="navUserMenu" style="display: none;">
					<button class="nav-user-btn" type="button" id="navUserBtn" aria-expanded="false" aria-haspopup="true">
						<i class="fas fa-user-circle nav-user-icon"></i>
						<span class="nav-user-email" id="user-display-name">Loading...</span>
						<i class="fas fa-chevron-down nav-user-caret"></i>
					</button>
					<div class="nav-user-dropdown" id="navUserDropdown" role="menu">
						<a class="nav-user-item" href="<?php echo BASE_URL; ?>user/account" role="menuitem">
							<i class="fas fa-id-card"></i> Tài khoản của tôi
						</a>
						<a class="nav-user-item nav-user-item--danger" href="#" id="btn-logout" role="menuitem">
							<i class="fas fa-sign-out-alt"></i> Đăng xuất
						</a>
					</div>
				</div>
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
