<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Chi tiết gói tour</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/itinerary-carousel.css?v=11.0">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/wishlist-button.css?v=1.0">
</head>
<body>
<?php include ROOT . "/includes/header.php"; ?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1>Chi tiết gói tour</h1>
			<p>Thông tin rõ ràng giúp bạn quyết định dễ dàng.</p>
		</section>
		<?php if (
      $data["error"]
  ) { ?><div class="alert error"><strong>Lỗi:</strong> <?php echo htmlentities(
    $data["error"],
); ?></div><?php } elseif (
      $data["msg"]
  ) { ?><div class="alert success"><strong>Thành công:</strong> <?php echo htmlentities(
    $data["msg"],
); ?></div><?php } ?>

		<?php if ($data["package"]): ?>
				<div class="grid-two">
					<section class="card">
						<img src="<?php echo BASE_URL; ?>admin/packageimages/<?php echo htmlentities(
    $data["package"]->PackageImage,
); ?>" alt="<?php echo htmlentities($data["package"]->PackageName); ?>">
						<h2><?php echo htmlentities($data["package"]->PackageName); ?></h2>
						<p class="badge">#PKG-<?php echo htmlentities(
          $data["package"]->PackageId,
      ); ?></p>
						
						<!-- Wishlist Button - Đẹp mắt và rõ ràng -->
						<button class="wishlist-btn" data-package-id="<?php echo htmlentities($data["package"]->PackageId); ?>" id="wishlistBtn">
							<i class="fas fa-heart"></i>
							<span class="wishlist-text">Lưu tour yêu thích</span>
						</button>
						<ul class="summary-list">
							<li><span>Loại gói</span><strong><?php echo htmlentities(
           $data["package"]->PackageType,
       ); ?></strong></li>
							<li><span>Địa điểm</span><strong><?php echo htmlentities(
           $data["package"]->PackageLocation,
       ); ?></strong></li>
							<li><span>Giá</span><strong><?php echo Controller::formatVND($data["package"]->PackagePrice); ?></strong></li>
						</ul>
						<p><?php echo htmlentities($data["package"]->PackageFetures); ?></p>
					</section>
					<section class="card">
						<h3>Đặt tour</h3>
						<form name="book" method="post" class="form-stack" action="<?php echo BASE_URL; ?>package/book/<?php echo htmlentities(
    $data["package"]->PackageId,
); ?>">
							<div class="form-grid">
								<div class="form-group">
									<label for="fromdate">Từ ngày</label>
									<input type="date" id="fromdate" name="fromdate" required>
								</div>
								<div class="form-group">
									<label for="todate">Đến ngày</label>
									<input type="date" id="todate" name="todate" required>
								</div>
							</div>
							<div class="form-group">
								<label for="comment">Ghi chú</label>
								<textarea id="comment" name="comment" required placeholder="Nêu thêm yêu cầu cụ thể"></textarea>
							</div>
							<?php if (!empty($_SESSION["login"])): ?>
								<button type="submit" name="submit2" class="btn">Đặt tour</button>
							<?php else: ?>
								<a class="btn btn-ghost" href="#" data-modal-target="signin-modal">Đăng nhập để đặt tour</a>
							<?php endif; ?>
						</form>
					</section>
				</div>
				
			<!-- Itinerary Carousel -->
			<?php if (isset($data["itineraries"]) && count($data["itineraries"]) > 0): ?>
				<section class="itinerary-section">
					<div class="itinerary-wrapper">
						<header class="itinerary-header">
							<h2 class="itinerary-headline">Lộ trình chi tiết</h2>
						</header>
						<ul class="itinerary-cards">
							<?php foreach ($data["itineraries"] as $item): ?>
								<li class="itinerary-card">
									<article class="itinerary-article">
										<div class="itinerary-content">
											<p class="itinerary-time"><?php echo htmlentities($item->TimeLabel); ?></p>
											<h3 class="itinerary-activity"><?php echo htmlentities($item->Activity); ?></h3>
										</div>
									</article>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</section>
			<?php endif; ?>
				
				<section class="card">
					<h3>Thông tin chi tiết</h3>
					<div class="package-details"><?php echo nl2br(htmlspecialchars($data["package"]->PackageDetails, ENT_QUOTES, 'UTF-8')); ?></div>
				</section>
		<?php else: ?>
			<div class="empty-state">Không tìm thấy gói tour.</div>
		<?php endif; ?>
	</div>
</main>
<?php include ROOT . "/includes/footer.php"; ?>
<?php include ROOT . "/includes/signup.php"; ?>
<?php include ROOT . "/includes/signin.php"; ?>
<?php include ROOT . "/includes/write-us.php"; ?>
<script src="<?php echo BASE_URL; ?>public/js/itinerary-carousel.js?v=8.0"></script>
<script src="<?php echo BASE_URL; ?>public/js/wishlist-button.js?v=1.0"></script>
</body>
</html>
