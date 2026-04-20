<!DOCTYPE html>
<html lang="vi">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Chi tiết gói tour</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/itinerary-carousel.css?v=14.0">
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
				<?php
				$averageInfo = $data["averageInfo"] ?? null;
				$reviews = $data["reviews"] ?? [];
				$ratingBreakdown = $data["ratingBreakdown"] ?? [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
				$totalReviews = (int)($averageInfo->total ?? 0);
				$avgRating = $totalReviews > 0 ? round((float)$averageInfo->avg_rating, 1) : 0;
				?>
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
							<li><span>Thời gian tour</span><strong><?php echo htmlentities(
																		$data["package"]->TourDuration,
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
							<div class="form-group">
								<label for="departuredate">Ngày khởi hành</label>
								<input type="date" id="departuredate" name="departuredate" required>
							</div>
							<div class="form-group">
								<label for="numberofpeople">Số người</label>
								<input type="number" id="numberofpeople" name="numberofpeople" min="1" max="100" value="1" required>
							</div>
							<div class="form-group">
								<label for="comment">Ghi chú</label>
								<textarea id="comment" name="comment" placeholder="Nêu thêm yêu cầu cụ thể"></textarea>
							</div>
							<?php if (!empty($_SESSION["login"])): ?>
								<button type="submit" name="submit2" class="btn">Đặt tour</button>
							<?php else: ?>
								<a class="btn btn-ghost" href="#" data-modal-target="signin-modal">Đăng nhập để đặt tour</a>
							<?php endif; ?>
						</form>
					</section>
				</div>

				<!-- Itinerary Section - Simple Layout -->
				<?php if (isset($data["itineraries"]) && count($data["itineraries"]) > 0): ?>
					<section class="card itinerary-section">
						<h3>Lộ trình chi tiết</h3>
						<div class="itinerary-list">
							<?php foreach ($data["itineraries"] as $item): ?>
								<div class="itinerary-item">
									<strong><?php echo htmlentities($item->TimeLabel); ?>:</strong>
									<span><?php echo htmlentities($item->Activity); ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endif; ?>

				<section class="card review-summary-card">
					<h3>Tóm tắt đánh giá</h3>
					<div class="review-summary-grid">
						<div class="review-summary-left">
							<div class="review-average-score"><?php echo number_format($avgRating, 1); ?></div>
							<div class="review-average-stars" aria-label="Điểm đánh giá trung bình">
								<?php for ($i = 1; $i <= 5; $i++): ?>
									<span class="review-star <?php echo ($i <= round($avgRating)) ? "is-active" : ""; ?>">&#9733;</span>
								<?php endfor; ?>
							</div>
							<p class="review-total-count"><?php echo $totalReviews; ?> đánh giá</p>
						</div>
						<div class="review-summary-right">
							<?php for ($star = 5; $star >= 1; $star--):
								$count = (int)($ratingBreakdown[$star] ?? 0);
								$percent = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
							?>
								<div class="rating-row">
									<span class="rating-label"><?php echo $star; ?> sao</span>
									<div class="rating-progress">
										<div class="rating-progress-fill" style="width: <?php echo number_format($percent, 2, ".", ""); ?>%;"></div>
									</div>
									<span class="rating-count"><?php echo $count; ?></span>
								</div>
							<?php endfor; ?>
							<button type="button" class="btn btn-ghost btn-compact review-cta" id="openReviewsModal">
								Xem tất cả đánh giá
							</button>
						</div>
					</div>
				</section>

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
	<?php if ($data["package"]): ?>
	<div class="modal review-list-modal" id="reviewsModal" aria-hidden="true">
		<div class="modal__dialog review-modal-dialog">
			<div class="review-modal-header">
				<h3>Đánh giá tour</h3>
				<button type="button" class="modal__close" id="closeReviewsModal" aria-label="Đóng">&times;</button>
			</div>
			<div class="review-filter-bar" id="reviewFilterBar">
				<button type="button" class="review-filter-chip is-active" data-rating="all">Tất cả</button>
				<button type="button" class="review-filter-chip" data-rating="5">5 Sao</button>
				<button type="button" class="review-filter-chip" data-rating="4">4 Sao</button>
				<button type="button" class="review-filter-chip" data-rating="3">3 Sao</button>
				<button type="button" class="review-filter-chip" data-rating="2">2 Sao</button>
				<button type="button" class="review-filter-chip" data-rating="1">1 Sao</button>
			</div>
			<div class="review-list-wrapper">
				<div id="reviewListContainer"></div>
				<div class="review-empty-state" id="reviewEmptyState" style="display:none;">
					<div class="empty-icon">&#128230;</div>
					<p>Chưa có đánh giá cho mức sao này</p>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
	<script>
		// Pass BASE_URL from PHP to JavaScript
		window.BASE_URL_FROM_PHP = '<?php echo BASE_URL; ?>';
		window.PACKAGE_REVIEWS = <?php
			$reviewPayload = [];
			if (!empty($data["package"]) && !empty($reviews)) {
				foreach ($reviews as $review) {
					$fullName = trim((string)($review->FullName ?? ""));
					$reviewPayload[] = [
						"name" => $fullName !== "" ? $fullName : "Khách hàng",
						"initial" => strtoupper(substr($fullName !== "" ? $fullName : "K", 0, 1)),
						"rating" => (int)($review->Rating ?? 0),
						"comment" => (string)($review->Comment ?? ""),
						"createdAt" => (string)($review->CreatedAt ?? "")
					];
				}
			}
			echo json_encode($reviewPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		?>;
	</script>
	<script src="<?php echo BASE_URL; ?>public/js/wishlist-button.js?v=1.1"></script>
	<script src="<?php echo BASE_URL; ?>public/js/package-reviews-modal.js?v=1.0"></script>
</body>

</html>