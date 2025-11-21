<?php $activePage = isset($currentPage) ? $currentPage : ''; ?>
<aside class="admin-sidebar" id="adminSidebar">
	<p class="admin-sidebar__title">Điều hướng</p>
	<nav class="admin-nav">
		<a href="dashboard.php" class="<?php echo $activePage==='dashboard' ? 'is-active' : ''; ?>">Bảng điều khiển</a>
		<a href="create-package.php" class="<?php echo $activePage==='create-package' ? 'is-active' : ''; ?>">Tạo gói tour</a>
		<a href="manage-packages.php" class="<?php echo $activePage==='manage-packages' ? 'is-active' : ''; ?>">Quản lý gói tour</a>
		<a href="manage-users.php" class="<?php echo $activePage==='manage-users' ? 'is-active' : ''; ?>">Quản lý người dùng</a>
		<a href="manage-bookings.php" class="<?php echo $activePage==='manage-bookings' ? 'is-active' : ''; ?>">Quản lý đặt tour</a>
		<a href="manageissues.php" class="<?php echo $activePage==='manage-issues' ? 'is-active' : ''; ?>">Yêu cầu hỗ trợ</a>
		<a href="manage-enquires.php" class="<?php echo $activePage==='manage-enquiries' ? 'is-active' : ''; ?>">Liên hệ khách hàng</a>
		<a href="manage-pages.php" class="<?php echo $activePage==='manage-pages' ? 'is-active' : ''; ?>">Trang nội dung</a>
	</nav>
</aside>