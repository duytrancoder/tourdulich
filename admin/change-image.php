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

$pimage=$_FILES["packageimage"]["name"];
move_uploaded_file($_FILES["packageimage"]["tmp_name"],"pacakgeimages/".$_FILES["packageimage"]["name"]);
$sql="update TblTourPackages set PackageImage=:pimage where PackageId=:imgid";
$query = $dbh->prepare($sql);

$query->bindParam(':imgid',$imgid,PDO::PARAM_STR);
$query->bindParam(':pimage',$pimage,PDO::PARAM_STR);
$query->execute();
$msg="Cập nhật hình ảnh gói tour thành công";



}

	$pageTitle = "GoTravel Admin | Cập nhật hình ảnh";
	$currentPage = 'manage-packages';
	$sql = "SELECT PackageImage from TblTourPackages where PackageId=:imgid";
	$query = $dbh -> prepare($sql);
	$query -> bindParam(':imgid', $imgid, PDO::PARAM_STR);
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
					<img src="pacakgeimages/<?php echo htmlentities($package->PackageImage);?>" alt="Ảnh gói tour" style="width:200px;border-radius:0.75rem;">
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