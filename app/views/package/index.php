<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Danh sách tour</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
</head>
<body>
<?php include ROOT . "/includes/header.php"; ?>
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
					<input type="text" id="keyword" name="keyword" value="<?php echo htmlentities(
         $data["keyword"],
     ); ?>" placeholder="Tên tour, địa điểm">
				</div>
				<div class="form-group">
					<label for="location">Địa điểm</label>
					<select id="location" name="location">
						<option value="">Tất cả</option>
						<?php foreach ($data["locations"] as $loc): ?>
							<option value="<?php echo htmlentities($loc->PackageLocation); ?>" <?php if (
    $data["locationFilter"] === $loc->PackageLocation
) {
    echo "selected";
} ?>><?php echo htmlentities($loc->PackageLocation); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="form-group">
					<label for="price">Ngân sách</label>
					<select id="price" name="price">
						<option value="" <?php if ($data["priceFilter"] === "") {
          echo "selected";
      } ?>>Bất kỳ</option>
						<option value="under-200" <?php if ($data["priceFilter"] === "under-200") {
          echo "selected";
      } ?>>Dưới 4.800.000 đ</option>
						<option value="200-500" <?php if ($data["priceFilter"] === "200-500") {
          echo "selected";
      } ?>>4.800.000 đ - 12.000.000 đ</option>
						<option value="over-500" <?php if ($data["priceFilter"] === "over-500") {
          echo "selected";
      } ?>>Trên 12.000.000 đ</option>
					</select>
				</div>
				<div class="form-group" style="align-self:flex-end;">
					<button class="btn" type="submit">Áp dụng</button>
				</div>
			</form>
		</section>

		<section class="card" style="margin-top:1.5rem;">
			<?php if (count($data["packages"])): ?>
				<div class="tour-grid">
					<?php foreach ($data["packages"] as $package): ?>
						<article class="tour-card">
							<img src="<?php echo BASE_URL; ?>admin/pacakgeimages/<?php echo htmlentities(
    $package->PackageImage,
); ?>" alt="<?php echo htmlentities($package->PackageName); ?>">
							<h4><?php echo htmlentities($package->PackageName); ?></h4>
							<div class="tour-card__meta">
								<span><?php echo htmlentities($package->PackageLocation); ?></span>
								<span>|</span>
								<span><?php echo htmlentities($package->PackageType); ?></span>
							</div>
							<p><?php echo htmlentities($package->PackageFetures); ?></p>
							<div class="tour-card__footer">
								<span class="price"><?php echo Controller::formatVND($package->PackagePrice); ?></span>
								<a class="btn btn-ghost" href="<?php echo BASE_URL; ?>package/details/<?php echo htmlentities(
    $package->PackageId,
); ?>">Chi tiết</a>
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
<?php include ROOT . "/includes/footer.php"; ?>
<?php include ROOT . "/includes/signup.php"; ?>
<?php include ROOT . "/includes/signin.php"; ?>
<?php include ROOT . "/includes/write-us.php"; ?>
</body>
</html>
