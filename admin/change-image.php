<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
	$imgid=intval($_GET['imgid']);
if(isset($_POST['submit']))
{
$pimage = '';
$error = '';

// Validate file upload
if (!isset($_FILES["packageimage"]) || $_FILES["packageimage"]["error"] !== UPLOAD_ERR_OK) {
	$error = "Vui lòng chọn hình ảnh";
} else {
	$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
	$fileType = $_FILES["packageimage"]["type"];
	$fileSize = $_FILES["packageimage"]["size"];
	
	if (!in_array($fileType, $allowedTypes)) {
		$error = "Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WEBP)";
	} elseif ($fileSize > 5 * 1024 * 1024) { // 5MB limit
		$error = "Kích thước file không được vượt quá 5MB";
	} else {
		// Sanitize filename
		$pimage = basename($_FILES["packageimage"]["name"]);
		$pimage = preg_replace('/[^a-zA-Z0-9._-]/', '_', $pimage);
		$uploadPath = "pacakgeimages/" . $pimage;
		
		if (move_uploaded_file($_FILES["packageimage"]["tmp_name"], $uploadPath)) {
			$sql="update tbltourpackages set PackageImage=:pimage where PackageId=:imgid";
			$query = $dbh->prepare($sql);
			$query->bindParam(':imgid',$imgid,PDO::PARAM_INT);
			$query->bindParam(':pimage',$pimage,PDO::PARAM_STR);
			$query->execute();
			$msg="Cập nhật hình ảnh gói tour thành công";
		} else {
			$error = "Không thể tải lên file. Vui lòng thử lại";
		}
	}
}
}

	$pageTitle = "GoTravel Admin | Cập nhật hình ảnh";
	$currentPage = 'manage-packages';
	$sql = "SELECT PackageImage from tbltourpackages where PackageId=:imgid";
	$query = $dbh -> prepare($sql);
	$query -> bindParam(':imgid', $imgid, PDO::PARAM_INT);
	$query->execute();
	$package = $query->fetch(PDO::FETCH_OBJ);
	include('includes/layout-start.php');
	?>
		<section class="admin-page-head">
			<div>
				<h1>Cập nhật hình ảnh</h1>
				<p>Thay đổi hình ảnh đại diện cho gói tour.</p>
			</div>
		</section>
		<?php if($msg){?><div class="alert success"><?php echo htmlentities($msg);?></div><?php } ?>
		<section class="card">
			<?php if($package): ?>
			<form method="post" enctype="multipart/form-data" class="form-stack">
				<div class="form-group">
					<label>Hình ảnh hiện tại</label>
					<img src="<?php echo BASE_URL; ?>admin/pacakgeimages/<?php echo htmlentities($package->PackageImage);?>" alt="Ảnh gói tour" style="width:200px;border-radius:0.75rem;">
				</div>
				<div class="form-group">
					<label for="packageimage">Hình ảnh mới</label>
					<input type="file" name="packageimage" id="packageimage" accept="image/*" required>
				</div>
				<button type="submit" name="submit" class="btn btn-primary">Cập nhật</button>
			</form>
			<?php else: ?>
			<div class="empty-state">Không tìm thấy gói tour.</div>
			<?php endif; ?>
		</section>
	<?php include('includes/layout-end.php'); ?>
<?php } ?>