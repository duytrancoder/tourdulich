<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{	
header('location:index.php');
exit;
}

$msg = '';
$error = '';
if(isset($_GET['del']))
{
	$delid=intval($_GET['del']);
	$sql = "DELETE FROM tbltourpackages WHERE PackageId=:delid";
	$query = $dbh->prepare($sql);
	$query -> bindParam(':delid',$delid, PDO::PARAM_INT);
	if($query -> execute()){
		$msg="Đã xóa gói tour thành công";
	}else{
		$error="Không thể xóa gói tour. Vui lòng thử lại.";
	}
}

$pageTitle = "GoTravel Admin | Quản lý gói tour";
$currentPage = 'manage-packages';

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchType = isset($_GET['type']) ? trim($_GET['type']) : '';
$searchLocation = isset($_GET['location']) ? trim($_GET['location']) : '';

$sql = "SELECT * FROM tbltourpackages WHERE 1=1";
$params = [];

if (!empty($search)) {
	$sql .= " AND PackageName LIKE :search";
	$params[':search'] = '%' . $search . '%';
}

if (!empty($searchType)) {
	$sql .= " AND PackageType LIKE :type";
	$params[':type'] = '%' . $searchType . '%';
}

if (!empty($searchLocation)) {
	$sql .= " AND PackageLocation LIKE :location";
	$params[':location'] = '%' . $searchLocation . '%';
}

$sql .= " ORDER BY PackageId DESC";

$query = $dbh->prepare($sql);
foreach ($params as $key => $value) {
	$query->bindValue($key, $value, PDO::PARAM_STR);
}
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);

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
			<form method="get" action="" class="form-stack">
				<div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
					<div class="form-group">
						<label for="search">Tìm theo tên</label>
						<input type="text" name="search" id="search" placeholder="Nhập tên gói tour..." value="<?php echo htmlentities($search); ?>">
					</div>
					<div class="form-group">
						<label for="type">Loại gói</label>
						<input type="text" name="type" id="type" placeholder="VD: Gia đình, Cặp đôi..." value="<?php echo htmlentities($searchType); ?>">
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
							<th>#</th>
							<th>Tên gói</th>
							<th>Loại gói</th>
							<th>Địa điểm</th>
							<th>Giá (VNĐ)</th>
							<th>Ngày tạo</th>
							<th>Thao tác</th>
						</tr>
					</thead>
					<tbody>
					<?php
					$cnt=1;
					if($query->rowCount() > 0)
					{
						foreach($results as $result)
						{	?>
						<tr>
							<td><?php echo htmlentities($cnt);?></td>
							<td><?php echo htmlentities($result->PackageName);?></td>
							<td><?php echo htmlentities($result->PackageType);?></td>
							<td><?php echo htmlentities($result->PackageLocation);?></td>
							<td><?php echo number_format($result->PackagePrice, 0, ',', '.') . ' đ';?></td>
							<td><?php echo date('d/m/Y', strtotime($result->Creationdate));?></td>
							<td>
								<div style="display: flex; gap: 0.5rem; align-items: center;">
									<a class="btn btn-primary" href="<?php echo BASE_URL; ?>admin/update-package.php?pid=<?php echo htmlentities($result->PackageId);?>">Sửa</a>
									<a class="btn btn-danger" href="<?php echo BASE_URL; ?>admin/manage-packages.php?del=<?php echo htmlentities($result->PackageId);?>" onclick="return confirm('Bạn có chắc chắn muốn xóa gói tour này không?');">Xóa</a>
								</div>
							</td>
						</tr>
					<?php $cnt++; }} else { ?>
						<tr><td colspan="7"><div class="empty-state">
							<?php if(!empty($search) || !empty($searchType) || !empty($searchLocation)) { ?>
								Không tìm thấy gói tour phù hợp. <a href="<?php echo BASE_URL; ?>admin/manage-packages.php">Xóa bộ lọc</a>
							<?php } else { ?>
								Chưa có gói tour nào.
							<?php } ?>
						</div></td></tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</section>
	</main>
</div>
<?php include('includes/layout-end.php');?>