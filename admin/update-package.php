<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
$pid=intval($_GET['pid']);	
if(isset($_POST['submit']))
{
$pname = trim($_POST['packagename'] ?? '');
$ptype = trim($_POST['packagetype'] ?? '');	
$plocation = trim($_POST['packagelocation'] ?? '');
$pprice = intval($_POST['packageprice'] ?? 0);	
$pfeatures = trim($_POST['packagefeatures'] ?? '');
$pdetails = trim($_POST['packagedetails'] ?? '');	

// Validate inputs
if (empty($pname) || empty($ptype) || empty($plocation) || $pprice <= 0 || empty($pfeatures) || empty($pdetails)) {
	$error = "Vui lòng điền đầy đủ thông tin";
} else {
	$sql="update tbltourpackages set PackageName=:pname,PackageType=:ptype,PackageLocation=:plocation,PackagePrice=:pprice,PackageFetures=:pfeatures,PackageDetails=:pdetails where PackageId=:pid";
$query = $dbh->prepare($sql);
$query->bindParam(':pname',$pname,PDO::PARAM_STR);
$query->bindParam(':ptype',$ptype,PDO::PARAM_STR);
$query->bindParam(':plocation',$plocation,PDO::PARAM_STR);
$query->bindParam(':pprice',$pprice,PDO::PARAM_INT);
$query->bindParam(':pfeatures',$pfeatures,PDO::PARAM_STR);
$query->bindParam(':pdetails',$pdetails,PDO::PARAM_STR);
	$query->bindParam(':pid',$pid,PDO::PARAM_INT);
	$query->execute();
	$msg="Cập nhật gói tour thành công";
}
}

	$pageTitle = "GoTravel Admin | Cập nhật gói tour";
	$currentPage = 'manage-packages';
	$sql = "SELECT * from tbltourpackages where PackageId=:pid";
	$query = $dbh -> prepare($sql);
	$query -> bindParam(':pid', $pid, PDO::PARAM_INT);
	$query->execute();
	$package = $query->fetch(PDO::FETCH_OBJ);
	include('includes/layout-start.php');
	?>
		<section class="admin-page-head">
			<div>
				<h1>Cập nhật gói tour</h1>
				<p>Điều chỉnh thông tin gói tour và quản lý hình ảnh.</p>
			</div>
		</section>
		<?php if(!$package){?><div class="alert error">Không tìm thấy gói tour.</div><?php } ?>
		<?php if($msg){?><div class="alert success"><?php echo htmlentities($msg);?></div><?php } ?>
		<?php if($package): ?>
		<section class="card">
			<form name="package" method="post" class="form-stack">
				<div class="form-grid">
					<div class="form-group">
						<label for="packagename">Tên gói</label>
						<input type="text" name="packagename" id="packagename" value="<?php echo htmlentities($package->PackageName);?>" required>
					</div>
					<div class="form-group">
						<label for="packagetype">Loại gói</label>
						<input type="text" name="packagetype" id="packagetype" value="<?php echo htmlentities($package->PackageType);?>" required>
					</div>
					<div class="form-group">
						<label for="packagelocation">Địa điểm</label>
						<input type="text" name="packagelocation" id="packagelocation" value="<?php echo htmlentities($package->PackageLocation);?>" required>
					</div>
					<div class="form-group">
						<label for="packageprice">Giá gói (VNĐ)</label>
						<input type="number" min="0" step="1000" name="packageprice" id="packageprice" value="<?php echo htmlentities($package->PackagePrice);?>" required>
						<small style="color: var(--muted); font-size: 0.85rem;">Giá hiện tại: <?php echo number_format($package->PackagePrice, 0, ',', '.') . ' đ'; ?></small>
					</div>
				</div>
				<div class="form-group">
					<label for="packagefeatures">Điểm nổi bật</label>
					<input type="text" name="packagefeatures" id="packagefeatures" value="<?php echo htmlentities($package->PackageFetures);?>" required>
				</div>
				<div class="form-group">
					<label for="packagedetails">Chi tiết gói</label>
					<textarea name="packagedetails" id="packagedetails" required><?php echo htmlentities($package->PackageDetails);?></textarea>
				</div>
				<div class="form-group">
					<label>Hình ảnh hiện tại</label>
					<div style="display:flex;gap:1rem;align-items:center;">
						<img src="<?php echo BASE_URL; ?>admin/packageimages/<?php echo htmlentities($package->PackageImage);?>" alt="Ảnh gói tour" style="width:120px;border-radius:0.5rem;">
						<a class="btn btn-ghost" href="<?php echo BASE_URL; ?>admin/change-image.php?imgid=<?php echo htmlentities($package->PackageId);?>">Thay đổi hình</a>
					</div>
				</div>
				<div class="form-group">
					<label>Ngày cập nhật gần nhất</label>
					<?php
						$lastUpdate = ($package->UpdationDate && $package->UpdationDate !== '0000-00-00 00:00:00')
							? $package->UpdationDate
							: 'Chưa cập nhật';
					?>
					<input type="text" value="<?php echo htmlentities($lastUpdate);?>" disabled>
				</div>
				<button type="submit" name="submit" class="btn btn-primary">Lưu thay đổi</button>
			</form>
		</section>
		<?php endif; ?>
	<?php include('includes/layout-end.php'); ?>
<?php } ?>