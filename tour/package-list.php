<?php
session_start();
error_reporting(0);
include('includes/config.php');

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$locationFilter = isset($_GET['location']) ? trim($_GET['location']) : '';
$priceFilter = isset($_GET['price']) ? $_GET['price'] : '';

$sql = "SELECT * FROM tbltourpackages WHERE 1=1";
if($keyword !== '') {
	$sql .= " AND PackageName LIKE :keyword";
}
if($locationFilter !== '') {
	$sql .= " AND PackageLocation = :location";
}
if($priceFilter === 'under-200') {
	$sql .= " AND PackagePrice < 200";
} elseif($priceFilter === '200-500') {
	$sql .= " AND PackagePrice BETWEEN 200 AND 500";
} elseif($priceFilter === 'over-500') {
	$sql .= " AND PackagePrice > 500";
}
$sql .= " ORDER BY Creationdate DESC";

$query = $dbh->prepare($sql);
if($keyword !== '') {
	$likeKeyword = "%".$keyword."%";
	$query->bindParam(':keyword', $likeKeyword, PDO::PARAM_STR);
}
if($locationFilter !== '') {
	$query->bindParam(':location', $locationFilter, PDO::PARAM_STR);
}
$query->execute();
$packages = $query->fetchAll(PDO::FETCH_OBJ);

$locationSql = "SELECT DISTINCT PackageLocation FROM tbltourpackages WHERE PackageLocation <> '' ORDER BY PackageLocation";
$locationStmt = $dbh->prepare($locationSql);
$locationStmt->execute();
$locations = $locationStmt->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Danh sách tour</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include('includes/header.php');?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1>Gói du lịch</h1>
			<p>Chọn tour phù hợp với ngân sách và lịch trình của bạn.</p>
		</section>

		<section class="card">
			<form class="form-grid" method="get">
				<div class="form-group">
					<label for="keyword">Từ khóa</label>
					<input type="text" id="keyword" name="keyword" value="<?php echo htmlentities($keyword);?>" placeholder="Tên tour, địa điểm">
				</div>
				<div class="form-group">
					<label for="location">Địa điểm</label>
					<select id="location" name="location">
						<option value="">Tất cả</option>
						<?php foreach($locations as $loc): ?>
							<option value="<?php echo htmlentities($loc->PackageLocation);?>" <?php if($locationFilter === $loc->PackageLocation) echo 'selected';?>><?php echo htmlentities($loc->PackageLocation);?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group">
					<label for="price">Ngân sách</label>
					<select id="price" name="price">
						<option value="" <?php if($priceFilter==='') echo 'selected';?>>Bất kỳ</option>
						<option value="under-200" <?php if($priceFilter==='under-200') echo 'selected';?>>Dưới 4.800.000 VND</option>
						<option value="200-500" <?php if($priceFilter==='200-500') echo 'selected';?>>4.800.000 - 12.000.000 VND</option>
						<option value="over-500" <?php if($priceFilter==='over-500') echo 'selected';?>>Trên 12.000.000 VND</option>
					</select>
				</div>
				<div class="form-group" style="align-self:flex-end;">
					<button class="btn" type="submit">Áp dụng</button>
				</div>
			</form>
		</section>

		<section class="card" style="margin-top:1.5rem;">
			<?php if(count($packages)): ?>
				<div class="tour-grid">
					<?php foreach($packages as $package): ?>
						<article class="tour-card">
							<img src="admin/pacakgeimages/<?php echo htmlentities($package->PackageImage);?>" alt="<?php echo htmlentities($package->PackageName);?>">
							<h4><?php echo htmlentities($package->PackageName);?></h4>
							<div class="tour-card__meta">
								<span><?php echo htmlentities($package->PackageLocation);?></span>
								<span>|</span>
								<span><?php echo htmlentities($package->PackageType);?></span>
							</div>
							<p><?php echo htmlentities($package->PackageFetures);?></p>
							<div class="tour-card__footer">
								<span class="price"><?php echo number_format($package->PackagePrice * 24000, 0, ',', '.');?> VND</span>
								<a class="btn btn-ghost" href="package-details.php?pkgid=<?php echo htmlentities($package->PackageId);?>">Chi tiết</a>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<div class="empty-state">Không tìm thấy tour phù hợp. Hãy thử thay đổi bộ lọc.</div>
			<?php endif; ?>
		</section>
	</div>
</main>
<?php include('includes/footer.php');?>
<?php include('includes/signup.php');?>
<?php include('includes/signin.php');?>
<?php include('includes/write-us.php');?>
</body>
</html>
