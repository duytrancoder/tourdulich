<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
	header('location:index.php');
	exit;
}

if(isset($_REQUEST['bkid']) && isset($_POST['cancel_reason']))
{
	$bid=intval($_GET['bkid']);
	$status=2;
	$cancelby='a';
	$cancelReason = $_POST['cancel_reason'];
	// TODO: Send cancellation reason to user (will be implemented later)
	$sql = "UPDATE tblbooking SET status=:status,CancelledBy=:cancelby,CancelReason=:cancelReason WHERE BookingId=:bid";
	$query = $dbh->prepare($sql);
	$query -> bindParam(':status',$status, PDO::PARAM_STR);
	$query -> bindParam(':cancelby',$cancelby , PDO::PARAM_STR);
	$query -> bindParam(':cancelReason',$cancelReason , PDO::PARAM_STR);
	$query-> bindParam(':bid',$bid, PDO::PARAM_STR);
	$query -> execute();
	$msg="Hủy đặt tour thành công. Lý do: " . htmlentities($cancelReason);
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

// Status 3: Admin mark booking as Completed
if(isset($_GET['complete']))
{
	$cmpid = intval($_GET['complete']);
	$sql = "UPDATE tblbooking SET status=3 WHERE BookingId=:cmpid";
	$query = $dbh->prepare($sql);
	$query->bindParam(':cmpid', $cmpid, PDO::PARAM_INT);
	$query->execute();
	$msg = "Tour đã được đánh dấu là Hoàn thành.";
}

$pageTitle = "GoTravel Admin | Quản lý đặt tour";
$currentPage = 'manage-bookings';
$searchBookingId = trim($_GET['booking_id'] ?? '');
$searchCustomer = trim($_GET['customer'] ?? '');
$searchTourInfo = trim($_GET['tour_info'] ?? '');

$sql = "SELECT tblbooking.BookingId as bookid,tblusers.FullName as fname,tblusers.MobileNumber as mnumber,tblusers.EmailId as email,tbltourpackages.PackageName as pckname,tbltourpackages.TourDuration as tourduration,tblbooking.PackageId as pid,tblbooking.FromDate as fdate,tblbooking.ToDate as tdate,tblbooking.Comment as comment,tblbooking.NumberOfPeople as numppl,tblbooking.TotalPrice as totalprice,tblbooking.status as status,tblbooking.CancelledBy as cancelby,tblbooking.UpdationDate as upddate,tblbooking.RegDate as regdate from tblusers join  tblbooking on  tblbooking.UserEmail=tblusers.EmailId join tbltourpackages on tbltourpackages.PackageId=tblbooking.PackageId WHERE 1=1";

if ($searchBookingId !== '') {
	if (ctype_digit($searchBookingId)) {
		$sql .= " AND tblbooking.BookingId = :booking_id";
	} else {
		$sql .= " AND 1=0";
	}
}

if ($searchCustomer !== '') {
	$sql .= " AND (tblusers.FullName LIKE :customer_name OR tblusers.EmailId LIKE :customer_email)";
}

if ($searchTourInfo !== '') {
	$sql .= " AND (tbltourpackages.PackageName LIKE :tour_name OR tbltourpackages.TourDuration LIKE :tour_duration)";
}

$sql .= " ORDER BY tblbooking.RegDate DESC";

$query = $dbh -> prepare($sql);

if ($searchBookingId !== '' && ctype_digit($searchBookingId)) {
	$query->bindValue(':booking_id', (int)$searchBookingId, PDO::PARAM_INT);
}

if ($searchCustomer !== '') {
	$query->bindValue(':customer_name', '%' . $searchCustomer . '%', PDO::PARAM_STR);
	$query->bindValue(':customer_email', '%' . $searchCustomer . '%', PDO::PARAM_STR);
}

if ($searchTourInfo !== '') {
	$query->bindValue(':tour_name', '%' . $searchTourInfo . '%', PDO::PARAM_STR);
	$query->bindValue(':tour_duration', '%' . $searchTourInfo . '%', PDO::PARAM_STR);
}

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
	<section class="card" style="margin-bottom: 1.5rem;">
		<form method="get" class="form-stack">
			<div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
				<div class="form-group">
					<label for="booking_id">Mã (#ID)</label>
					<input type="text" id="booking_id" name="booking_id" placeholder="Nhập mã tour" value="<?php echo htmlentities($_GET['booking_id'] ?? ''); ?>">
				</div>
				<div class="form-group">
					<label for="customer">Khách hàng</label>
					<input type="text" id="customer" name="customer" placeholder="Tên hoặc email khách hàng" value="<?php echo htmlentities($searchCustomer); ?>">
				</div>
				<div class="form-group">
					<label for="tour_info">Thông tin Tour</label>
					<input type="text" id="tour_info" name="tour_info" placeholder="Tên tour hoặc thời gian tour" value="<?php echo htmlentities($searchTourInfo); ?>">
				</div>
			</div>
			<div style="display:flex; gap:.5rem; flex-wrap:wrap;">
				<button type="submit" class="btn btn-primary">Tìm kiếm</button>
				<a class="btn btn-ghost" href="<?php echo BASE_URL; ?>admin/manage-bookings.php">Xóa bộ lọc</a>
			</div>
		</form>
	</section>
	<section class="card">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>Mã (#ID)</th>
						<th>Khách hàng</th>
						<th>Thông tin Tour</th>
						<th>Thời gian tour</th>
						<th>Ngày khởi hành</th>
						<th>Số lượng khách</th>
						<th>Tổng tiền</th>
						<th>Trạng thái</th>
						<th>Thao tác</th>
					</tr>
				</thead>
				<tbody>
				<?php if($query->rowCount() > 0) {
					foreach($results as $result) {
						$statusClass = 'is-pending';
					$statusText = 'Chờ xử lý';
					if($result->status==1){ $statusClass='is-approved'; $statusText='Đã xác nhận'; }
					if($result->status==2 && $result->cancelby=='a'){ $statusClass='is-cancelled'; $statusText='Quản trị viên hủy'; }
					if($result->status==2 && $result->cancelby=='u'){ $statusClass='is-cancelled'; $statusText='Người dùng hủy'; }
					if($result->status==3){ $statusClass='is-completed'; $statusText='✓ Đã hoàn thành'; }
				?>
				<tr>
					<td><strong><?php echo htmlentities($result->bookid);?></strong></td>
					<td><?php echo htmlentities($result->fname);?></td>
					<td><?php echo htmlentities($result->pckname);?></td>
					<td><?php echo htmlentities($result->tourduration);?></td>
					<td><?php echo date('d/m/Y', strtotime($result->fdate));?></td>
					<td><?php echo htmlentities($result->numppl);?> người</td>
					<td><?php echo number_format($result->totalprice, 0, ',', '.') . ' VND';?></td>
					<td><span class="status-chip <?php echo $statusClass;?>"><?php echo htmlentities($statusText);?></span></td>
					<td>
						<div style="display:flex; gap:.5rem; flex-wrap:wrap;">
							<a class="btn btn-ghost" href="<?php echo BASE_URL; ?>admin/view-booking.php?bid=<?php echo htmlentities($result->bookid);?>">Chi tiết</a>
							<?php if($result->status == 1): ?>
								<a class="btn btn-success" href="<?php echo BASE_URL; ?>admin/manage-bookings.php?complete=<?php echo htmlentities($result->bookid);?>" onclick="return confirm('Xác nhận hoàn thành tour #<?php echo htmlentities($result->bookid);?>?');">Hoàn thành</a>
							<?php elseif($result->status == 3): ?>
								<span class="btn btn-locked" style="cursor:default;opacity:0.6;">&#128274; Đã khóa</span>
							<?php else: ?>
								<a class="btn btn-danger" href="<?php echo BASE_URL; ?>admin/manage-bookings.php?del=<?php echo htmlentities($result->bookid);?>" onclick="return confirm('Bạn có chắc chắn muốn xóa đặt tour #<?php echo htmlentities($result->bookid);?>?');">Xóa</a>
							<?php endif; ?>
						</div>
					</td>
				</tr>
				<?php }
				} else { ?>
				<tr><td colspan="9"><div class="empty-state"><?php echo (!empty($_GET['booking_id']) || !empty($searchCustomer) || !empty($searchTourInfo)) ? 'Không tìm thấy đặt tour phù hợp.' : 'Hiện chưa có đặt tour nào.'; ?></div></td></tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</section>
	<script>
	function promptCancelReason(form) {
		var reason = prompt('Vui lòng nhập lý do hủy tour:');
		if (reason === null || reason.trim() === '') {
			alert('Bạn phải nhập lý do hủy tour.');
			return false;
		}
		form.querySelector('input[name="cancel_reason"]').value = reason;
		return true;
	}
	</script>
<?php include('includes/layout-end.php');?>
