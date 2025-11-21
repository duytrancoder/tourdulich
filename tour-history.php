<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0)
{
	header('location:index.php');
}
else{
	if(isset($_REQUEST['bkid']))
	{
		$bid=intval($_GET['bkid']);
		$email=$_SESSION['login'];
		$sql = "SELECT FromDate FROM tblbooking WHERE UserEmail=:email and BookingId=:bid";
		$query= $dbh -> prepare($sql);
		$query-> bindParam(':email', $email, PDO::PARAM_STR);
		$query-> bindParam(':bid', $bid, PDO::PARAM_STR);
		$query-> execute();
		$results = $query -> fetchAll(PDO::FETCH_OBJ);
		if($query->rowCount() > 0)
		{
			foreach($results as $result)
			{
				$fdate=$result->FromDate;
				$a=explode("/",$fdate);
				$val=array_reverse($a);
				$mydate =implode("/",$val);
				$cdate=date('Y/m/d');
				$date1=date_create($cdate);
				$date2=date_create($fdate);
				$diff=date_diff($date1,$date2);
				$df=$diff->format("%a");
				if($df>1)
				{
					$status=2;
					$cancelby='u';
					$sql = "UPDATE tblbooking SET status=:status,CancelledBy=:cancelby WHERE UserEmail=:email and BookingId=:bid";
					$query = $dbh->prepare($sql);
					$query -> bindParam(':status',$status, PDO::PARAM_STR);
					$query -> bindParam(':cancelby',$cancelby , PDO::PARAM_STR);
					$query-> bindParam(':email',$email, PDO::PARAM_STR);
					$query-> bindParam(':bid',$bid, PDO::PARAM_STR);
					$query -> execute();
					$msg="Hủy đặt tour thành công";
				}
				else
				{
					$error="Bạn không thể hủy đặt tour trước 24 giờ";
				}
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Lịch sử tour</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include('includes/header.php');?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1>Lịch sử tour của tôi</h1>
			<p>Theo dõi toàn bộ đặt chỗ và trạng thái xử lý cập nhật theo thời gian thực.</p>
		</section>
		<?php if($error){?><div class="alert error"><strong>Lỗi:</strong> <?php echo htmlentities($error); ?></div><?php } elseif($msg){?><div class="alert success"><strong>Thành công:</strong> <?php echo htmlentities($msg); ?></div><?php }?>
		<section class="card">
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>#</th>
							<th>Mã đặt tour</th>
							<th>Tên gói</th>
							<th>Từ ngày</th>
							<th>Đến ngày</th>
							<th>Ghi chú</th>
							<th>Trạng thái</th>
							<th>Ngày đặt</th>
							<th>Thao tác</th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$uemail=$_SESSION['login'];
					$sql = "SELECT tblbooking.BookingId as bookid,tblbooking.PackageId as pkgid,tbltourpackages.PackageName as packagename,tblbooking.FromDate as fromdate,tblbooking.ToDate as todate,tblbooking.Comment as comment,tblbooking.status as status,tblbooking.RegDate as regdate,tblbooking.CancelledBy as cancelby,tblbooking.UpdationDate as upddate from tblbooking join tbltourpackages on tbltourpackages.PackageId=tblbooking.PackageId where UserEmail=:uemail";
					$query = $dbh->prepare($sql);
					$query -> bindParam(':uemail', $uemail, PDO::PARAM_STR);
					$query->execute();
					$results=$query->fetchAll(PDO::FETCH_OBJ);
					$cnt=1;
					if($query->rowCount() > 0)
					{
						foreach($results as $result)
						{
							$statusText = "Đang chờ xử lý";
							$statusClass = "is-pending";
							if($result->status==1){
								$statusText = "Đã xác nhận";
								$statusClass = "is-approved";
							}
							if($result->status==2 && $result->cancelby=='u'){
								$statusText = "Bạn đã hủy vào ".$result->upddate;
								$statusClass = "is-cancelled";
							}
							if($result->status==2 && $result->cancelby=='a'){
								$statusText = "Quản trị viên đã hủy vào ".$result->upddate;
								$statusClass = "is-cancelled";
							}
						?>
						<tr>
							<td><?php echo htmlentities($cnt);?></td>
							<td>#BK<?php echo htmlentities($result->bookid);?></td>
							<td><a href="package-details.php?pkgid=<?php echo htmlentities($result->pkgid);?>"><?php echo htmlentities($result->packagename);?></a></td>
							<td><?php echo htmlentities($result->fromdate);?></td>
							<td><?php echo htmlentities($result->todate);?></td>
							<td><?php echo htmlentities($result->comment);?></td>
							<td><span class="status-chip <?php echo $statusClass;?>"><?php echo htmlentities($statusText);?></span></td>
							<td><?php echo htmlentities($result->regdate);?></td>
							<td>
								<?php if($result->status==2){ ?>
									<span class="link-muted">Đã hủy</span>
								<?php } else { ?>
									<a class="btn-link" href="tour-history.php?bkid=<?php echo htmlentities($result->bookid);?>" onclick="return confirm('Bạn có chắc chắn muốn hủy đặt tour này không?');">Hủy</a>
								<?php } ?>
							</td>
						</tr>
						<?php $cnt++; }} else { ?>
						<tr><td colspan="9"><div class="empty-state">Bạn chưa có đặt tour nào.</div></td></tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</section>
	</div>
</main>
<?php include('includes/footer.php');?>
<?php include('includes/signup.php');?>
<?php include('includes/signin.php');?>
<?php include('includes/write-us.php');?>
</body>
</html>
<?php } ?>
