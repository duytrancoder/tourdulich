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
$sql = "SELECT * from tblusers";
$query = $dbh -> prepare($sql);
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
							<td><a class="btn btn-danger" href="manage-users.php?del=<?php echo htmlentities($result->id);?>" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này không?');">Xóa</a></td>
						</tr>
						<?php $cnt++; }} else { ?>
						<tr><td colspan="7"><div class="empty-state">Chưa có người dùng nào.</div></td></tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</section>
<?php include('includes/layout-end.php'); ?>
<?php } ?>