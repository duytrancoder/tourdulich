<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Danh sách tour</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?v=3.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/premium-cards.css?v=5.1">
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
				<div class="premium-tour-grid">
					<?php foreach ($data["packages"] as $package): ?>
						<div class="premium-card-container"
						     style="background-image: url('<?php echo BASE_URL; ?>admin/packageimages/<?php echo htmlentities($package->PackageImage); ?>');"
						     data-tilt data-tilt-max="10" data-tilt-speed="500" data-tilt-perspective="1800" data-tilt-glare data-tilt-max-glare="0.1" data-tilt-scale="1.03" data-tilt-reset="true">

							<div class="premium-inner-border" data-tilt-transform-element></div>

							<div class="premium-content-area" data-tilt-transform-element>
								<div class="premium-gradient-overlay"></div>

								<div class="premium-type-badge" data-tilt-transform-element>
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
										<path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
									</svg>
									<?php echo htmlentities($package->PackageType); ?>
								</div>

								<div class="premium-text-block" data-tilt-transform-element>
									<h3><?php echo htmlentities($package->PackageName); ?></h3>
									<p class="location">
										<svg class="location-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
											<circle cx="12" cy="10" r="3"></circle>
										</svg>
										<?php echo htmlentities($package->PackageLocation); ?>
									</p>
									<p class="features"><?php echo htmlentities($package->PackageFetures); ?></p>
								</div>

								<div class="premium-price-tag" data-tilt-transform-element>
									<?php echo Controller::formatVND($package->PackagePrice); ?>
								</div>

								<a href="<?php echo BASE_URL; ?>package/details/<?php echo htmlentities($package->PackageId); ?>" 
								   class="premium-tour-button" data-tilt-transform-element>
									Xem chi tiết
									<svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<path d="M12 5l7 7-7 7"></path>
										<path d="M5 12h14"></path>
									</svg>
								</a>
							</div>
						</div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>
<script>
	VanillaTilt.init(document.querySelectorAll("[data-tilt]"), {});
</script>
</body>
</html>
