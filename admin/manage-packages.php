<?php
session_start();
error_reporting(0);
include('includes/config.php');
require_once dirname(__DIR__) . '/core/Helper.php';
if(strlen($_SESSION['alogin'])==0)
{	
header('location:index.php');
exit;
}

// PHP Query đã được gỡ bỏ. Dữ liệu sẽ được load qua REST API bằng Javascript.

include('includes/layout-start.php');
?>
		<section class="admin-page-head">
			<div>
				<h1>Quản lý gói tour</h1>
				<p>Danh sách tất cả gói tour đang có trên hệ thống.</p>
			</div>
			<a class="btn btn-primary" href="<?php echo BASE_URL; ?>admin/create-package.php">+ Tạo gói tour</a>
		</section>
		<?php if($error){?><div class="alert error"><?php echo htmlentities($error); ?></div><?php } ?>
		<?php if($msg){?><div class="alert success"><?php echo htmlentities($msg); ?></div><?php } ?>
		
		<!-- Search Form -->
		<section class="card" style="margin-bottom: 1.5rem;">
			<form id="search-form-admin" class="form-stack">
				<div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
					<div class="form-group">
						<label for="search">Tìm theo mã/ tên gói</label>
						<input type="text" name="search" id="search" placeholder="Nhập mã hoặc tên gói tour..." value="<?php echo htmlentities($search); ?>">
					</div>
					<div class="form-group">
						<label for="type">Loại gói</label>
						<select name="type" id="type">
							<option value="">-- Tất cả loại gói --</option>
							<option value="Tour tiết kiệm" <?php if($searchType == 'Tour tiết kiệm') echo 'selected'; ?>>Tour tiết kiệm</option>
							<option value="Tour tiêu chuẩn" <?php if($searchType == 'Tour tiêu chuẩn') echo 'selected'; ?>>Tour tiêu chuẩn</option>
							<option value="Tour cao cấp" <?php if($searchType == 'Tour cao cấp') echo 'selected'; ?>>Tour cao cấp</option>
							<option value="Tour riêng" <?php if($searchType == 'Tour riêng') echo 'selected'; ?>>Tour riêng</option>
						</select>
					</div>
					<div class="form-group">
						<label for="location">Địa điểm</label>
						<input type="text" name="location" id="location" placeholder="VD: Hà Nội, Đà Nẵng..." value="<?php echo htmlentities($searchLocation); ?>">
					</div>
				</div>
				<div style="display: flex; gap: 0.5rem;">
					<button type="submit" class="btn btn-primary">🔍 Tìm kiếm</button>
					<a href="<?php echo BASE_URL; ?>admin/manage-packages.php" class="btn btn-ghost">Xóa bộ lọc</a>
				</div>
			</form>
		</section>
		
		<section class="card">
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>Mã gói tour</th>
							<th>Tên gói</th>
							<th>Loại gói</th>
							<th>Địa điểm</th>
							<th>Thời gian tour</th>
							<th>Giá (VNĐ)</th>
							<th>Ngày tạo</th>
							<th>Thao tác</th>
						</tr>
					</thead>
					<tbody>
					<!-- Nội dung bảng sẽ được render bởi Javascript (admin-tours.js) -->
					</tbody>
				</table>
			</div>
		</section>
	</main>
</div>
<script src="<?php echo BASE_URL; ?>assets/js/api/admin-tours.js"></script>
<?php include('includes/layout-end.php');?>