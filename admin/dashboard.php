<?php
session_start();
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{	
header('location:index.php');
exit;
}

$pageTitle = "GoTravel Admin | Bảng điều khiển";
$currentPage = 'dashboard';

$userCount = $dbh->query("SELECT COUNT(*) FROM tblusers")->fetchColumn();
$bookingCount = $dbh->query("SELECT COUNT(*) FROM tblbooking")->fetchColumn();
$enquiryCount = $dbh->query("SELECT COUNT(*) FROM tblenquiry")->fetchColumn();
$packageCount = $dbh->query("SELECT COUNT(*) FROM tbltourpackages")->fetchColumn();
$issueCount = $dbh->query("SELECT COUNT(*) FROM tblissues")->fetchColumn();

$latestBookingsSql = "SELECT tblbooking.BookingId, tblbooking.RegDate, tblbooking.status, tbltourpackages.PackageName 
	FROM tblbooking 
	JOIN tbltourpackages ON tbltourpackages.PackageId = tblbooking.PackageId 
	ORDER BY tblbooking.RegDate DESC LIMIT 5";
$latestBookings = $dbh->query($latestBookingsSql)->fetchAll(PDO::FETCH_OBJ);

include('includes/layout-start.php');
?>
		<section class="admin-page-head">
			<div>
				<h1>Bảng điều khiển</h1>
				<p>Theo dõi nhanh tình trạng hoạt động của hệ thống GoTravel.</p>
			</div>
		</section>
		<section class="stats-grid">
			<div class="stat-card">
				<p>Người dùng</p>
				<h3><?php echo number_format($userCount);?></h3>
			</div>
			<div class="stat-card">
				<p>Lượt đặt tour</p>
				<h3><?php echo number_format($bookingCount);?></h3>
			</div>
			<div class="stat-card">
				<p>Liên hệ</p>
				<h3><?php echo number_format($enquiryCount);?></h3>
			</div>
			<div class="stat-card">
				<p>Gói tour</p>
				<h3><?php echo number_format($packageCount);?></h3>
			</div>
			<div class="stat-card">
				<p>Yêu cầu hỗ trợ</p>
				<h3><?php echo number_format($issueCount);?></h3>
			</div>
		</section>

		<section class="card">
			<div class="admin-page-head" style="margin-bottom:1rem;">
				<h2>Đặt tour mới nhất</h2>
				<a class="btn btn-ghost" href="manage-bookings.php">Xem tất cả</a>
			</div>
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>#</th>
							<th>Gói tour</th>
							<th>Ngày đặt</th>
							<th>Trạng thái</th>
						</tr>
					</thead>
					<tbody>
						<?php if($latestBookings): ?>
							<?php foreach($latestBookings as $booking): 
								$statusClass = 'is-pending';
								$statusText = 'Đang chờ xử lý';
								if($booking->status==1){ $statusClass='is-approved'; $statusText='Đã xác nhận'; }
								if($booking->status==2){ $statusClass='is-cancelled'; $statusText='Đã hủy'; }
							?>
							<tr>
								<td>#BK<?php echo htmlentities($booking->BookingId);?></td>
								<td><?php echo htmlentities($booking->PackageName);?></td>
								<td><?php echo htmlentities($booking->RegDate);?></td>
								<td><span class="status-chip <?php echo $statusClass;?>"><?php echo htmlentities($statusText);?></span></td>
							</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr><td colspan="4"><div class="empty-state">Chưa có đặt tour nào.</div></td></tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</section>

		<section class="card">
			<h2>Thao tác nhanh</h2>
			<div class="form-grid">
				<a class="btn btn-primary" href="create-package.php">Tạo gói tour</a>
				<a class="btn btn-secondary" href="manage-packages.php">Quản lý gói tour</a>
				<a class="btn btn-ghost" href="manageissues.php">Xem yêu cầu hỗ trợ</a>
				<a class="btn btn-ghost" href="manage-enquires.php">Hộp thư liên hệ</a>
			</div>
		</section>
	</main>
</div>
<?php include('includes/layout-end.php');?>