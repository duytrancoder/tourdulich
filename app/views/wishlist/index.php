<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Tour yêu thích</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?v=5.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/modern-tour-cards.css?v=1.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/tour-card-sample.css?v=1.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/enhanced-forms.css?v=5.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/theme-colors.css?v=5.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/wishlist-button.css?v=1.0">
</head>
<body>
<?php include ROOT . "/includes/header.php"; ?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1>Tour yêu thích của tôi</h1>
			<p>Quản lý các tour mà bạn yêu thích và sẵn sàng đặt.</p>
		</section>

		<?php if ($data["error"]): ?>
		<div class="alert error"><strong>Lỗi:</strong> <?php echo htmlentities($data["error"]); ?></div>
		<?php elseif ($data["msg"]): ?>
		<div class="alert success"><strong>Thành công:</strong> <?php echo htmlentities($data["msg"]); ?></div>
		<?php endif; ?>

		<?php if (count($data["wishlistItems"])): ?>
		<div class="tour-grid">
			<?php foreach ($data["wishlistItems"] as $package): ?>
				<div class="tour-card">
					<div class="badge"><?php echo htmlentities($package->PackageType); ?></div>

					<div class="tour-card__media">
						<img src="<?php echo BASE_URL; ?>admin/packageimages/<?php echo htmlentities($package->PackageImage); ?>"
							alt="<?php echo htmlentities($package->PackageName); ?>">

						<button class="wishlist-heart active" type="button" data-package-id="<?php echo htmlentities($package->PackageId); ?>"
							aria-label="Xóa khỏi danh sách yêu thích" title="Xóa khỏi danh sách yêu thích">
							<i class="fas fa-heart"></i>
						</button>

						<div class="tour-card__duration">
							<i class="fas fa-calendar-alt"></i>
							<span><?php echo htmlentities($package->TourDuration); ?></span>
						</div>
					</div>

					<div class="tour-card__content">
						<div class="tour-card__location">
							<i class="fas fa-map-marker-alt"></i>
							<span><?php echo htmlentities($package->PackageLocation); ?></span>
						</div>

						<h3 class="tour-card__title"><?php echo htmlentities($package->PackageName); ?></h3>
						<p class="tour-card__desc"><?php echo htmlentities($package->PackageFetures); ?></p>

						<div class="tour-card__footer">
							<div>
								<span class="tour-card__price-label">GIÁ TỪ</span>
								<div class="tour-card__price"><?php echo Controller::formatVND($package->PackagePrice); ?></div>
							</div>
							<a class="tour-card__btn" href="<?php echo BASE_URL; ?>package/details/<?php echo htmlentities($package->PackageId); ?>">
								Chi tiết
							</a>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php else: ?>
		<div class="empty-state">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 64px; height: 64px; margin: 0 auto 1rem; color: var(--muted);">
				<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
			</svg>
			<h2>Chưa có tour yêu thích</h2>
			<p>Hãy khám phá những tour tuyệt vời và thêm chúng vào danh sách yêu thích của bạn.</p>
			<a href="<?php echo BASE_URL; ?>package" class="btn">Khám phá tour</a>
		</div>
		<?php endif; ?>
	</div>
</main>
<?php include ROOT . "/includes/footer.php"; ?>
<?php include ROOT . "/includes/signup.php"; ?>
<?php include ROOT . "/includes/signin.php"; ?>
<?php include ROOT . "/includes/write-us.php"; ?>
<script>
    // Pass BASE_URL from PHP to JavaScript
    window.BASE_URL_FROM_PHP = '<?php echo BASE_URL; ?>';
</script>
<script src="<?php echo BASE_URL; ?>public/js/wishlist.js?v=1.1"></script>
</body>
</html>
