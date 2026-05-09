<header class="admin-header">
	<button class="admin-menu-btn" type="button" data-sidebar-toggle aria-expanded="false">
		<span>☰</span>
		Menu
	</button>
	<div class="admin-header__brand">GoTravel Admin</div>
	<div class="admin-header__actions">
		<div class="admin-profile">
			<button class="admin-profile__toggle" type="button" data-profile-toggle aria-expanded="false">
				<img src="<?php echo BASE_URL; ?>admin/images/User-icon.png" alt="Avatar quản trị">
				<div class="admin-profile__meta">
					<span>Quản trị viên</span>
					<small>Xin chào</small>
				</div>
			</button>
			<div class="admin-profile__menu" id="adminProfileMenu">
				<a href="<?php echo BASE_URL; ?>admin/change-password.php">Hồ sơ &amp; bảo mật</a>
				<a href="javascript:void(0);" onclick="adminLogout()">Đăng xuất</a>
			</div>
		</div>
	</div>
</header>