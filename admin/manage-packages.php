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

$sql = "SELECT * from tbltourpackages";
$query = $dbh -> prepare($sql);
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
								<a class="btn btn-ghost" href="<?php echo BASE_URL; ?>admin/update-package.php?pid=<?php echo htmlentities($result->PackageId);?>">Xem chi tiết</a>
								<a class="btn" href="<?php echo BASE_URL; ?>admin/manage-itinerary.php?pid=<?php echo htmlentities($result->PackageId);?>" style="background: var(--brand); color: white;">Quản lý lộ trình</a>
								<a class="btn btn-danger" href="<?php echo BASE_URL; ?>admin/manage-packages.php?del=<?php echo htmlentities($result->PackageId);?>" onclick="return confirm('Bạn có chắc chắn muốn xóa gói tour này không?');">Xóa</a>
							</td>
						</tr>
					<?php $cnt++; }} else { ?>
						<tr><td colspan="7"><div class="empty-state">Chưa có gói tour nào.</div></td></tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</section>
	</main>
</div>
<?php include('includes/layout-end.php');?>