<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{	
header('location:index.php');
}
else{ 
$msg = '';
$error = '';
if(isset($_GET['del']))
{
$delid=intval($_GET['del']);
$sql = "DELETE FROM tblusers WHERE id=:delid";
$query = $dbh->prepare($sql);
$query -> bindParam(':delid',$delid, PDO::PARAM_STR);
if($query -> execute()){
	$msg="Đã xóa người dùng thành công";
}else{
	$error="Không thể xóa người dùng.";
}
}

$pageTitle = "GoTravel Admin | Quản lý người dùng";
$currentPage = 'manage-users';

// Search functionality
$searchName = isset($_GET['name']) ? trim($_GET['name']) : '';
$searchPhone = isset($_GET['phone']) ? trim($_GET['phone']) : '';
$searchEmail = isset($_GET['email']) ? trim($_GET['email']) : '';

$sql = "SELECT * FROM tblusers WHERE 1=1";
$params = [];

if (!empty($searchName)) {
	$sql .= " AND FullName LIKE :name";
	$params[':name'] = '%' . $searchName . '%';
}

if (!empty($searchPhone)) {
	$sql .= " AND MobileNumber LIKE :phone";
	$params[':phone'] = '%' . $searchPhone . '%';
}

if (!empty($searchEmail)) {
	$sql .= " AND EmailId LIKE :email";
	$params[':email'] = '%' . $searchEmail . '%';
}

$sql .= " ORDER BY id DESC";

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
				<h1>Quản lý người dùng</h1>
				<p>Danh sách tài khoản khách hàng đăng ký trên hệ thống.</p>
			</div>
		</section>
		<?php if($error){?><div class="alert error"><?php echo htmlentities($error);?></div><?php } ?>
		<?php if($msg){?><div class="alert success"><?php echo htmlentities($msg);?></div><?php } ?>
		
		<!-- Search Form -->
		<section class="card" style="margin-bottom: 1.5rem;">
			<form method="get" action="" class="form-stack">
				<div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
					<div class="form-group">
						<label for="name">Họ tên</label>
						<input type="text" name="name" id="name" placeholder="Nhập họ tên..." value="<?php echo htmlentities($searchName); ?>">
					</div>
					<div class="form-group">
						<label for="phone">Số điện thoại</label>
						<input type="text" name="phone" id="phone" placeholder="Nhập số điện thoại..." value="<?php echo htmlentities($searchPhone); ?>">
					</div>
					<div class="form-group">
						<label for="email">Email</label>
						<input type="text" name="email" id="email" placeholder="Nhập email..." value="<?php echo htmlentities($searchEmail); ?>">
					</div>
				</div>
				<div style="display: flex; gap: 0.5rem;">
					<button type="submit" class="btn btn-primary">🔍 Tìm kiếm</button>
					<a href="<?php echo BASE_URL; ?>admin/manage-users.php" class="btn btn-ghost">Xóa bộ lọc</a>
				</div>
			</form>
		</section>
		
		<section class="card">
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>#</th>
							<th>Họ tên</th>
							<th>Số điện thoại</th>
							<th>Email</th>
							<th>Ngày đăng ký</th>
							<th>Ngày cập nhật</th>
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
							<td><?php echo htmlentities($result->FullName);?></td>
							<td><?php echo htmlentities($result->MobileNumber);?></td>
							<td><?php echo htmlentities($result->EmailId);?></td>
							<td><?php echo htmlentities($result->RegDate);?></td>
							<td>
								<?php
								$lastUpdate = ($result->UpdationDate && $result->UpdationDate !== '0000-00-00 00:00:00')
									? $result->UpdationDate
									: 'Chưa cập nhật';
								echo htmlentities($lastUpdate);
								?>
							</td>
							<td><a class="btn btn-danger" href="<?php echo BASE_URL; ?>admin/manage-users.php?del=<?php echo htmlentities($result->id);?>" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này không?');">Xóa</a></td>
						</tr>
						<?php $cnt++; }} else { ?>
						<tr><td colspan="7"><div class="empty-state">
							<?php if(!empty($searchName) || !empty($searchPhone) || !empty($searchEmail)) { ?>
								Không tìm thấy người dùng phù hợp. <a href="<?php echo BASE_URL; ?>admin/manage-users.php">Xóa bộ lọc</a>
							<?php } else { ?>
								Chưa có người dùng nào.
							<?php } ?>
						</div></td></tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</section>
<?php include('includes/layout-end.php'); ?>
<?php } ?>