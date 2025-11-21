<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
	header('location:index.php');
	exit;
}

$selectedType = isset($_GET['type']) ? $_GET['type'] : '';
$pageMap = [
	'terms' => 'Điều khoản & Điều kiện',
	'privacy' => 'Chính sách bảo mật',
	'aboutus' => 'Giới thiệu',
	'contact' => 'Liên hệ'
];
$pageTitle = "GoTravel Admin | Quản lý trang";
$currentPage = 'manage-pages';

if(isset($_POST['submit']) && $selectedType)
{
	$pagedetails=$_POST['pgedetails'];
	$sql = "UPDATE tblpages SET detail=:pagedetails WHERE type=:pagetype";
	$query = $dbh->prepare($sql);
	$query -> bindParam(':pagetype',$selectedType, PDO::PARAM_STR);
	$query-> bindParam(':pagedetails',$pagedetails, PDO::PARAM_STR);
	$query -> execute();
	$msg="Cập nhật nội dung trang thành công";
}

$pageContent = '';
if($selectedType){
	$sql = "SELECT detail from tblpages where type=:pagetype";
	$query = $dbh -> prepare($sql);
	$query->bindParam(':pagetype',$selectedType,PDO::PARAM_STR);
	$query->execute();
	$pageContent = $query->fetchColumn();
}

include('includes/layout-start.php');
?>
	<section class="admin-page-head">
		<div>
			<h1>Quản lý trang nội dung</h1>
			<p>Chỉnh sửa nhanh các trang tĩnh hiển thị trên website.</p>
		</div>
	</section>
	<?php if($error){?><div class="alert error"><?php echo htmlentities($error);?></div><?php } ?>
	<?php if($msg){?><div class="alert success"><?php echo htmlentities($msg);?></div><?php } ?>
	<section class="card">
		<form method="post" class="form-stack">
			<div class="form-group">
				<label for="page-selector">Chọn trang</label>
				<select id="page-selector" class="form-control" onchange="if(this.value){ window.location = this.value; }">
					<option value="">-- Chọn trang cần chỉnh sửa --</option>
					<?php foreach($pageMap as $type => $label): ?>
					<option value="manage-pages.php?type=<?php echo $type; ?>" <?php echo $selectedType===$type ? 'selected' : '';?>><?php echo $label;?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php if($selectedType && isset($pageMap[$selectedType])): ?>
			<div class="form-group">
				<label>Trang đã chọn</label>
				<input type="text" value="<?php echo $pageMap[$selectedType];?>" disabled>
			</div>
			<div class="form-group">
				<label for="pgedetails">Nội dung trang</label>
				<textarea name="pgedetails" id="pgedetails" rows="12" required><?php echo htmlentities($pageContent);?></textarea>
			</div>
			<button type="submit" name="submit" value="Update" class="btn btn-primary">Cập nhật</button>
			<?php else: ?>
			<div class="empty-state">Vui lòng chọn một trang ở danh sách trên để bắt đầu chỉnh sửa.</div>
			<?php endif; ?>
		</form>
	</section>
<?php include('includes/layout-end.php');?>
