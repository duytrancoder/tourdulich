<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0)
{
	header('location:index.php');
}
else{
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Yêu cầu hỗ trợ</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include('includes/header.php');?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1>Yêu cầu đã gửi</h1>
			<p>Theo dõi trạng thái phản hồi từ đội GoTravel.</p>
		</section>
		<?php if(isset($_SESSION['msg'])){?><div class="alert success"><?php echo htmlentities($_SESSION['msg']); unset($_SESSION['msg']);?></div><?php } ?>
		<?php if(isset($_SESSION['error'])){?><div class="alert error"><?php echo htmlentities($_SESSION['error']); unset($_SESSION['error']);?></div><?php } ?>
		<section class="card">
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>#</th>
							<th>Mã vé</th>
							<th>Vấn đề</th>
							<th>Mô tả</th>
							<th>Ghi chú quản trị</th>
							<th>Ngày tạo</th>
							<th>Ngày cập nhật</th>
						</tr>
					</thead>
					<tbody>
					<?php 
					$uemail=$_SESSION['login'];
					$sql = "SELECT * from tblissues where UserEmail=:uemail ORDER BY PostingDate DESC";
					$query = $dbh->prepare($sql);
					$query -> bindParam(':uemail', $uemail, PDO::PARAM_STR);
					$query->execute();
					$results=$query->fetchAll(PDO::FETCH_OBJ);
					$cnt=1;
					if($query->rowCount() > 0):
						foreach($results as $result): ?>
						<tr>
							<td><?php echo htmlentities($cnt);?></td>
							<td>#TKT-<?php echo htmlentities($result->id);?></td>
							<td><?php echo htmlentities($result->Issue);?></td>
							<td><?php echo htmlentities($result->Description);?></td>
							<td><?php echo $result->AdminRemark ? htmlentities($result->AdminRemark) : 'Chưa có phản hồi';?></td>
							<td><?php echo htmlentities($result->PostingDate);?></td>
							<td><?php echo htmlentities($result->AdminremarkDate);?></td>
						</tr>
						<?php $cnt++; endforeach; else: ?>
						<tr><td colspan="7"><div class="empty-state">Bạn chưa gửi yêu cầu nào.</div></td></tr>
					<?php endif; ?>
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
