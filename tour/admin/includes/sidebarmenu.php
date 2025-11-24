<?php $activePage = isset($currentPage) ? $currentPage : ''; ?>
<aside class="admin-sidebar" id="adminSidebar">
	<p class="admin-sidebar__title">Điều hướng</p>
	<nav class="admin-nav">
		<a href="dashboard.php" class="<?php echo $activePage==='dashboard' ? 'is-active' : ''; ?>"><i class="fa fa-dashboard"></i> Bảng điều khiển</a>
		<a href="create-package.php" class="<?php echo $activePage==='create-package' ? 'is-active' : ''; ?>"><i class="fa fa-plus"></i> Tạo gói tour</a>
		<a href="manage-packages.php" class="<?php echo $activePage==='manage-packages' ? 'is-active' : ''; ?>"><i class="fa fa-list"></i> Quản lý gói tour</a>
		<a href="manage-users.php" class="<?php echo $activePage==='manage-users' ? 'is-active' : ''; ?>"><i class="fa fa-users"></i> Quản lý người dùng</a>
		<a href="manage-bookings.php" class="<?php echo $activePage==='manage-bookings' ? 'is-active' : ''; ?>"><i class="fa fa-book"></i> Quản lý đặt tour</a>
		<a href="manageissues.php" class="<?php echo $activePage==='manage-issues' ? 'is-active' : ''; ?>"><i class="fa fa-ticket"></i> Yêu cầu hỗ trợ</a>
		<a href="manage-enquires.php" class="<?php echo $activePage==='manage-enquiries' ? 'is-active' : ''; ?>"><i class="fa fa-envelope"></i> Liên hệ khách hàng</a>
		<a href="manage-pages.php" class="<?php echo $activePage==='manage-pages' ? 'is-active' : ''; ?>"><i class="fa fa-file"></i> Trang nội dung</a>
	</nav>
</aside>