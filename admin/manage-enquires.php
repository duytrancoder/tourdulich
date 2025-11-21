<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
	header('location:index.php');
	exit;
}

if(isset($_REQUEST['eid']))
{
	$eid=intval($_GET['eid']);
	$status=1;
	$sql = "UPDATE tblenquiry SET Status=:status WHERE  id=:eid";
	$query = $dbh->prepare($sql);
	$query -> bindParam(':status',$status, PDO::PARAM_STR);
	$query-> bindParam(':eid',$eid, PDO::PARAM_STR);
	$query -> execute();
	$msg="Đã đánh dấu liên hệ";
}

if(isset($_GET['del']))
{
	$delid=intval($_GET['del']);
	$sql = "DELETE FROM tblenquiry WHERE id=:delid";
	$query = $dbh->prepare($sql);
	$query -> bindParam(':delid',$delid, PDO::PARAM_STR);
	$query -> execute();
	$msg="Đã xóa liên hệ";
}

$pageTitle = "GoTravel Admin | Liên hệ khách hàng";
$currentPage = 'manage-enquiries';
$sql = "SELECT * from tblenquiry";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
include('includes/layout-start.php');
?>
	<section class="admin-page-head">
		<div>
			<h1>Liên hệ khách hàng</h1>
			<p>Ghi nhận và xử lý các thông tin khách hàng gửi từ trang Liên hệ.</p>
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
						<th>Email</th>
						<th>Số điện thoại</th>
						<th>Chủ đề</th>
						<th>Mô tả</th>
						<th>Ngày gửi</th>
						<th>Trạng thái</th>
						<th>Thao tác</th>
					</tr>
				</thead>
				<tbody>
				<?php if($query->rowCount() > 0) { $cnt=1; foreach($results as $result) { ?>
				<tr>
					<td>#ENQ<?php echo htmlentities($result->id);?></td>
					<td><?php echo htmlentities($result->FullName);?></td>
					<td><?php echo htmlentities($result->EmailId);?></td>
					<td><?php echo htmlentities($result->MobileNumber);?></td>
					<td><?php echo htmlentities($result->Subject);?></td>
					<td><?php echo htmlentities($result->Description);?></td>
					<td><?php echo htmlentities($result->PostingDate);?></td>
					<td>
						<?php if($result->Status==1){ ?>
							<span class="status-chip is-approved">Đã xử lý</span>
						<?php } else { ?>
							<span class="status-chip is-pending">Chưa xử lý</span>
						<?php } ?>
					</td>
					<td>
						<?php if($result->Status!=1){ ?>
							<a class="btn btn-primary" href="manage-enquires.php?eid=<?php echo htmlentities($result->id);?>">Đánh dấu đã xử lý</a>
						<?php } ?>
						<a class="btn btn-ghost" href="manage-enquires.php?del=<?php echo htmlentities($result->id);?>" onclick="return confirm('Bạn có chắc chắn muốn xóa liên hệ này không?');">Xóa</a>
					</td>
				</tr>
				<?php $cnt++; } } else { ?>
				<tr><td colspan="9"><div class="empty-state">Chưa có liên hệ nào.</div></td></tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</section>
<?php include('includes/layout-end.php');?>
