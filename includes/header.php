<?php
$isLoggedIn = !empty($_SESSION['login']);
$currentPage = basename($_SERVER['SCRIPT_NAME']);
$pageType = isset($_GET['type']) ? $_GET['type'] : '';
?>
<header class="site-header">
	<div class="top-bar">
		<div class="container top-bar__content">
			<div class="top-bar__links">
				<?php if($isLoggedIn): ?>
					<span class="link-muted">Xin chào, <?php echo htmlentities($_SESSION['login']);?></span>
					<a class="link-muted" href="profile.php">Hồ sơ</a>
					<a class="link-muted" href="change-password.php">Đổi mật khẩu</a>
					<a class="link-muted" href="tour-history.php">Lịch sử tour</a>
					<a class="btn btn-ghost btn-compact" href="logout.php">Đăng xuất</a>
				<?php else: ?>
					<a class="link-muted" href="admin/index.php">Quản trị</a>
					<a class="link-muted" href="#" data-modal-target="signup-modal">Đăng ký</a>
					<a class="btn btn-ghost btn-compact" href="#" data-modal-target="signin-modal">Đăng nhập</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="nav-bar">
		<div class="container nav-bar__content">
			<a class="brand" href="index.php">GoTravel</a>
			<button class="nav-toggle" type="button" aria-label="Mở menu" aria-expanded="false">
				<span></span>
				<span></span>
				<span></span>
			</button>
			<nav class="site-nav" id="siteNav">
				<a class="<?php echo $currentPage === 'index.php' ? 'is-active' : '';?>" href="index.php">Trang chủ</a>
				<a class="<?php echo $currentPage === 'page.php' && $pageType==='aboutus' ? 'is-active' : '';?>" href="page.php?type=aboutus">Giới thiệu</a>
				<a class="<?php echo $currentPage === 'package-list.php' ? 'is-active' : '';?>" href="package-list.php">Gói du lịch</a>
				<a class="<?php echo $currentPage === 'page.php' && $pageType==='privacy' ? 'is-active' : '';?>" href="page.php?type=privacy">Chính sách</a>
				<a class="<?php echo $currentPage === 'page.php' && $pageType==='terms' ? 'is-active' : '';?>" href="page.php?type=terms">Điều khoản</a>
				<a class="<?php echo $currentPage === 'page.php' && $pageType==='contact' ? 'is-active' : '';?>" href="page.php?type=contact">Liên hệ</a>
				<?php if($isLoggedIn): ?>
					<a href="#" data-modal-target="support-modal">Yêu cầu hỗ trợ</a>
				<?php else: ?>
					<a class="<?php echo $currentPage === 'enquiry.php' ? 'is-active' : '';?>" href="enquiry.php">Gửi hỏi đáp</a>
				<?php endif; ?>
			</nav>
		</div>
	</div>
</header>