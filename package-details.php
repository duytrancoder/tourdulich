<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(isset($_POST['submit2']))
{
	$pid=intval($_GET['pkgid']);
	$useremail=$_SESSION['login'];
	$fromdate=$_POST['fromdate'];
	$todate=$_POST['todate'];
	$comment=$_POST['comment'];
	$status=0;
	$sql="INSERT INTO tblbooking(PackageId,UserEmail,FromDate,ToDate,Comment,status) VALUES(:pid,:useremail,:fromdate,:todate,:comment,:status)";
	$query = $dbh->prepare($sql);
	$query->bindParam(':pid',$pid,PDO::PARAM_STR);
	$query->bindParam(':useremail',$useremail,PDO::PARAM_STR);
	$query->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
	$query->bindParam(':todate',$todate,PDO::PARAM_STR);
	$query->bindParam(':comment',$comment,PDO::PARAM_STR);
	$query->bindParam(':status',$status,PDO::PARAM_STR);
	$query->execute();
	$lastInsertId = $dbh->lastInsertId();
	if($lastInsertId)
	{
		$msg="Đặt tour thành công.";
	}
	else 
	{
		$error="Có lỗi xảy ra. Vui lòng thử lại";
	}
}

$pid=intval($_GET['pkgid']);
$sql = "SELECT * from tbltourpackages where PackageId=:pid";
$query = $dbh->prepare($sql);
$query -> bindParam(':pid', $pid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Chi tiết gói tour</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include('includes/header.php');?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1>Chi tiết gói tour</h1>
			<p>Thông tin rõ ràng giúp bạn quyết định dễ dàng.</p>
		</section>
		<?php if($error){?><div class="alert error"><strong>Lỗi:</strong> <?php echo htmlentities($error); ?></div><?php } elseif($msg){?><div class="alert success"><strong>Thành công:</strong> <?php echo htmlentities($msg); ?></div><?php } ?>

		<?php if($query->rowCount() > 0): ?>
			<?php foreach($results as $result): ?>
				<div class="grid-two">
					<section class="card">
						<img src="admin/pacakgeimages/<?php echo htmlentities($result->PackageImage);?>" alt="<?php echo htmlentities($result->PackageName);?>">
						<h2><?php echo htmlentities($result->PackageName);?></h2>
						<p class="badge">#PKG-<?php echo htmlentities($result->PackageId);?></p>
						<ul class="summary-list">
							<li><span>Loại gói</span><strong><?php echo htmlentities($result->PackageType);?></strong></li>
							<li><span>Địa điểm</span><strong><?php echo htmlentities($result->PackageLocation);?></strong></li>
							<li><span>Giá</span><strong>USD <?php echo htmlentities($result->PackagePrice);?></strong></li>
						</ul>
						<p><?php echo htmlentities($result->PackageFetures);?></p>
					</section>
					<section class="card">
						<h3>Đặt tour</h3>
						<form name="book" method="post" class="form-stack">
							<div class="form-grid">
								<div class="form-group">
									<label for="fromdate">Từ ngày</label>
									<input type="date" id="fromdate" name="fromdate" required>
								</div>
								<div class="form-group">
									<label for="todate">Đến ngày</label>
									<input type="date" id="todate" name="todate" required>
								</div>
							</div>
							<div class="form-group">
								<label for="comment">Ghi chú</label>
								<textarea id="comment" name="comment" required placeholder="Nêu thêm yêu cầu cụ thể"></textarea>
							</div>
							<?php if($_SESSION['login']): ?>
								<button type="submit" name="submit2" class="btn">Đặt tour</button>
							<?php else: ?>
								<a class="btn btn-ghost" href="#" data-modal-target="signin-modal">Đăng nhập để đặt tour</a>
							<?php endif; ?>
						</form>
					</section>
				</div>
				<section class="card">
					<h3>Thông tin chi tiết</h3>
					<p><?php echo nl2br(htmlentities($result->PackageDetails));?></p>
				</section>
			<?php endforeach; ?>
		<?php else: ?>
			<div class="empty-state">Không tìm thấy gói tour.</div>
		<?php endif; ?>
	</div>
</main>
<?php include('includes/footer.php');?>
<?php include('includes/signup.php');?>
<?php include('includes/signin.php');?>
<?php include('includes/write-us.php');?>
</body>
</html>
