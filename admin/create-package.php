<?php
session_start();
error_reporting(0);
include('includes/config.php');
require_once dirname(__DIR__) . '/core/Helper.php';
Helper::requireAdminLogin();

if(isset($_POST['submit']))
{
$pname = trim($_POST['packagename'] ?? '');
$ptype = trim($_POST['packagetype'] ?? '');	
$plocation = trim($_POST['packagelocation'] ?? '');
$pprice = intval($_POST['packageprice'] ?? 0);	
$pfeatures = trim($_POST['packagefeatures'] ?? '');
$pdetails = trim($_POST['packagedetails'] ?? '');	
$pimage = '';

// Validate inputs
if (empty($pname) || empty($ptype) || empty($plocation) || $pprice <= 0 || empty($pfeatures) || empty($pdetails)) {
	$error = "Vui lòng điền đầy đủ thông tin";
} elseif (!isset($_FILES["packageimage"]) || $_FILES["packageimage"]["error"] !== UPLOAD_ERR_OK) {
	$error = "Vui lòng chọn hình ảnh";
} else {
	// Validate file upload using Helper class
	$validation = Helper::validateImage($_FILES["packageimage"]);
	if (!$validation['valid']) {
		$error = $validation['error'];
	} else {
		// Sanitize filename
		$pimage = Helper::sanitizeFilename($_FILES["packageimage"]["name"]);
		$uploadPath = "packageimages/" . $pimage;
		
		if (move_uploaded_file($_FILES["packageimage"]["tmp_name"], $uploadPath)) {
			// File uploaded successfully, continue with database insert
		} else {
			$error = "Không thể tải lên file. Vui lòng thử lại";
		}
	}
}

if (!isset($error)) {
$sql="INSERT INTO tbltourpackages(PackageName,PackageType,PackageLocation,PackagePrice,PackageFetures,PackageDetails,PackageImage) VALUES(:pname,:ptype,:plocation,:pprice,:pfeatures,:pdetails,:pimage)";
$query = $dbh->prepare($sql);
$query->bindParam(':pname',$pname,PDO::PARAM_STR);
$query->bindParam(':ptype',$ptype,PDO::PARAM_STR);
$query->bindParam(':plocation',$plocation,PDO::PARAM_STR);
$query->bindParam(':pprice',$pprice,PDO::PARAM_INT);
$query->bindParam(':pfeatures',$pfeatures,PDO::PARAM_STR);
$query->bindParam(':pdetails',$pdetails,PDO::PARAM_STR);
$query->bindParam(':pimage',$pimage,PDO::PARAM_STR);
	$query->execute();
	$lastInsertId = $dbh->lastInsertId();
	if($lastInsertId)
	{
		$msg="Tạo gói tour thành công";
	}
	else 
	{
		$error="Có lỗi xảy ra. Vui lòng thử lại";
	}
}
}

	$pageTitle = "GoTravel Admin | Tạo gói tour";
	$currentPage = 'create-package';
	include('includes/layout-start.php');
	?>
		<section class="admin-page-head">
			<div>
				<h1>Tạo gói tour</h1>
				<p>Thêm nhanh gói tour mới với đầy đủ thông tin và hình ảnh.</p>
			</div>
		</section>
		<?php if($error){?><div class="alert error"><?php echo htmlentities($error); ?> </div><?php } ?>
		<?php if($msg){?><div class="alert success"><?php echo htmlentities($msg); ?> </div><?php } ?>
		<section class="card">
			<form name="package" method="post" enctype="multipart/form-data" class="form-stack">
				<div class="form-grid">
					<div class="form-group">
						<label for="packagename">Tên gói</label>
						<input type="text" name="packagename" id="packagename" required>
					</div>
					<div class="form-group">
						<label for="packagetype">Loại gói</label>
						<input type="text" name="packagetype" id="packagetype" placeholder="Gia đình / Cặp đôi / ..." required>
					</div>
					<div class="form-group">
						<label for="packagelocation">Địa điểm</label>
						<input type="text" name="packagelocation" id="packagelocation" required>
					</div>
					<div class="form-group">
						<label for="packageprice">Giá gói (VNĐ)</label>
						<input type="number" min="0" step="1000" name="packageprice" id="packageprice" required>
						<small style="color: var(--muted); font-size: 0.85rem;">Nhập giá bằng VNĐ. Ví dụ: 4.800.000</small>
					</div>
				</div>
				<div class="form-group">
					<label for="packagefeatures">Điểm nổi bật</label>
					<input type="text" name="packagefeatures" id="packagefeatures" placeholder="Ví dụ: Đưa đón sân bay miễn phí" required>
				</div>
				<div class="form-group">
					<label for="packagedetails">Chi tiết gói</label>
					<textarea name="packagedetails" id="packagedetails" placeholder="Nhập mô tả chi tiết" required></textarea>
				</div>
				<div class="form-group">
					<label for="packageimage">Hình ảnh gói</label>
					<input type="file" name="packageimage" id="packageimage" accept="image/*" required>
				</div>
				<div>
					<button type="submit" name="submit" class="btn btn-primary">Tạo gói tour</button>
					<button type="reset" class="btn btn-ghost">Làm mới</button>
				</div>
			</form>
		</section>
	<?php include('includes/layout-end.php'); ?>