<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
	header('location:index.php');
	exit;
}

if(isset($_REQUEST['bkid']))
{
	$bid=intval($_GET['bkid']);
	$status=2;
	$cancelby='a';
	$sql = "UPDATE tblbooking SET status=:status,CancelledBy=:cancelby WHERE  BookingId=:bid";
	$query = $dbh->prepare($sql);
	$query -> bindParam(':status',$status, PDO::PARAM_STR);
	$query -> bindParam(':cancelby',$cancelby , PDO::PARAM_STR);
	$query-> bindParam(':bid',$bid, PDO::PARAM_STR);
	$query -> execute();
	$msg="Hủy đặt tour thành công";
}

if(isset($_REQUEST['bckid']))
{
	$bcid=intval($_GET['bckid']);
	$status=1;
	$sql = "UPDATE tblbooking SET status=:status WHERE BookingId=:bcid";
	$query = $dbh->prepare($sql);
	$query -> bindParam(':status',$status, PDO::PARAM_STR);
	$query->bindParam(':bcid',$bcid, PDO::PARAM_STR);
	$query -> execute();
	$msg="Xác nhận đặt tour thành công";
}

if(isset($_GET['del']))
{
	$delid=intval($_GET['del']);
	$sql = "DELETE FROM tblbooking WHERE BookingId=:delid";
	$query = $dbh->prepare($sql);
	$query -> bindParam(':delid',$delid, PDO::PARAM_STR);
	$query -> execute();
	$msg="Đã xóa bản ghi đặt tour";
}

$pageTitle = "GoTravel Admin | Quản lý đặt tour";
$currentPage = 'manage-bookings';
$sql = "SELECT tblbooking.BookingId as bookid,tblusers.FullName as fname,tblusers.MobileNumber as mnumber,tblusers.EmailId as email,tbltourpackages.PackageName as pckname,tblbooking.PackageId as pid,tblbooking.FromDate as fdate,tblbooking.ToDate as tdate,tblbooking.Comment as comment,tblbooking.status as status,tblbooking.CancelledBy as cancelby,tblbooking.UpdationDate as upddate from tblusers join  tblbooking on  tblbooking.UserEmail=tblusers.EmailId join tbltourpackages on tbltourpackages.PackageId=tblbooking.PackageId";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
include('includes/layout-start.php');
?>
	<section class="admin-page-head">
		<div>
			<h1>Quản lý đặt tour</h1>
			<p>Cập nhật trạng thái và theo dõi các yêu cầu đặt tour.</p>
		</div>
	</section>
	<?php if($error){?><div class="alert error"><?php echo htmlentities($error);?></div><?php } ?>
	<?php if($msg){?><div class="alert success"><?php echo htmlentities($msg);?></div><?php } ?>
	<section class="card">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>Mã đặt tour</th>
						<th>Khách hàng</th>
						<th>Liên hệ</th>
						<th>Gói tour</th>
						<th>Từ / Đến</th>
						<th>Ghi chú</th>
						<th>Trạng thái</th>
						<th>Thao tác</th>
						<th>Xóa</th>
					</tr>
				</thead>
				<tbody>
				<?php if($query->rowCount() > 0) {
					foreach($results as $result) {
						$statusClass = 'is-pending';
						$statusText = 'Đang chờ xử lý';
						$lastUpdate = ($result->upddate && $result->upddate !== '0000-00-00 00:00:00') ? $result->upddate : 'Chưa cập nhật';
						if($result->status==1){ $statusClass='is-approved'; $statusText='Đã xác nhận'; }
						if($result->status==2 && $result->cancelby=='a'){ $statusClass='is-cancelled'; $statusText='Quản trị viên đã hủy '.$lastUpdate; }
						if($result->status==2 && $result->cancelby=='u'){ $statusClass='is-cancelled'; $statusText='Người dùng đã hủy '.$lastUpdate; }
				?>
				<tr>
					<td>#BK-<?php echo htmlentities($result->bookid);?></td>
					<td><?php echo htmlentities($result->fname);?></td>
					<td>
						<div><?php echo htmlentities($result->mnumber);?></div>
						<div class="helper-text"><?php echo htmlentities($result->email);?></div>
					</td>
					<td><a href="update-package.php?pid=<?php echo htmlentities($result->pid);?>"><?php echo htmlentities($result->pckname);?></a></td>
					<td><?php echo htmlentities($result->fdate);?> → <?php echo htmlentities($result->tdate);?></td>
					<td><?php echo htmlentities($result->comment);?></td>
					<td><span class="status-chip <?php echo $statusClass;?>"><?php echo htmlentities($statusText);?></span></td>
					<td>
						<?php if($result->status==2){ ?>
							<span class="link-muted">Đã hủy</span>
						<?php } else { ?>
							<a class="btn btn-danger" href="manage-bookings.php?bkid=<?php echo htmlentities($result->bookid);?>" onclick="return confirm('Bạn có chắc chắn muốn hủy đặt tour này không?')">Hủy</a>
							<a class="btn btn-primary" href="manage-bookings.php?bckid=<?php echo htmlentities($result->bookid);?>" onclick="return confirm('Bạn có chắc chắn muốn xác nhận đặt tour này không?')">Xác nhận</a>
						<?php } ?>
					</td>
					<td><a class="btn btn-ghost" href="manage-bookings.php?del=<?php echo htmlentities($result->bookid);?>" onclick="return confirm('Bạn có chắc chắn muốn xóa bản ghi này không?');">Xóa</a></td>
				</tr>
				<?php }
				} else { ?>
				<tr><td colspan="9"><div class="empty-state">Hiện chưa có đặt tour nào.</div></td></tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</section>
<?php include('includes/layout-end.php');?>
