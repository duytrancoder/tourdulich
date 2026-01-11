<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Chi tiết gói tour</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
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
						<img src="<?php echo BASE_URL; ?>admin/pacakgeimages/<?php echo htmlentities(
    $data["package"]->PackageImage,
); ?>" alt="<?php echo htmlentities($data["package"]->PackageName); ?>">
						<h2><?php echo htmlentities($data["package"]->PackageName); ?></h2>
						<p class="badge">#PKG-<?php echo htmlentities(
          $data["package"]->PackageId,
      ); ?></p>
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
</body>
</html>
