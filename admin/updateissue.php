<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{ 
	header('location:index.php');
	exit;
}
$iid=intval($_GET['iid']);
if(isset($_POST['submit2']))
{
	$remark=$_POST['remark'];
	$sql = "UPDATE tblissues SET AdminRemark=:remark WHERE  id=:iid";
	$query = $dbh->prepare($sql);
	$query -> bindParam(':remark',$remark, PDO::PARAM_STR);
	$query-> bindParam(':iid',$iid, PDO::PARAM_STR);
	$query -> execute();
	$msg="Đã cập nhật ghi chú thành công";
}
$sql = "SELECT * from tblissues where id=:iid";
$query = $dbh -> prepare($sql);
$query-> bindParam(':iid',$iid, PDO::PARAM_STR);
$query->execute();
$issue=$query->fetch(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Cập nhật ghi chú</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body style="padding:2rem; background:#f5f7fb;">
	<div class="card" style="max-width:600px;margin:0 auto;">
		<h2>Cập nhật ghi chú yêu cầu</h2>
		<?php if($msg){?><div class="alert success" style="margin-top:1rem;"><?php echo htmlentities($msg);?></div><?php } ?>
		<?php if($issue && empty($issue->AdminRemark)): ?>
		<form method="post" class="form-stack" style="margin-top:1rem;">
			<div class="form-group">
				<label for="remark">Ghi chú</label>
				<textarea name="remark" id="remark" rows="6" required></textarea>
			</div>
			<button type="submit" name="submit2" class="btn btn-primary">Lưu ghi chú</button>
			<button type="button" class="btn btn-ghost" onclick="window.close();">Đóng</button>
		</form>
		<?php elseif($issue): ?>
		<p><strong>Ghi chú:</strong> <?php echo htmlentities($issue->AdminRemark);?></p>
		<p><strong>Ngày ghi chú:</strong> <?php echo htmlentities($issue->AdminremarkDate);?></p>
		<button type="button" class="btn btn-ghost" onclick="window.close();">Đóng</button>
		<?php else: ?>
		<div class="empty-state">Không tìm thấy yêu cầu.</div>
		<button type="button" class="btn btn-ghost" onclick="window.close();">Đóng</button>
		<?php endif; ?>
	</div>
</body>
</html>
