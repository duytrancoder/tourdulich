<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Lịch sử tour</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
</head>
<body>
<?php include ROOT . "/includes/header.php"; ?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1>Lịch sử tour của tôi</h1>
			<p>Theo dõi toàn bộ đặt chỗ và trạng thái xử lý cập nhật theo thời gian thực.</p>
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
		<section class="card">
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>#</th>
							<th>Mã đặt tour</th>
							<th>Tên gói</th>
							<th>Ngày khởi hành</th>
							<th>Ghi chú</th>
							<th>Trạng thái</th>
							<th>Ngày đặt</th>
							<th>Thao tác</th>
						</tr>
					</thead>
					<tbody>
					<?php
     $cnt = 1;
     if (count($data["bookings"]) > 0) {
         foreach ($data["bookings"] as $result) {

             $statusText = "Đang chờ xử lý";
             $statusClass = "is-pending";
             if ($result->status == 1) {
                 $statusText = "Đã xác nhận";
                 $statusClass = "is-approved";
             }
             if ($result->status == 3) {
                 $statusText = "Đã hoàn thành";
                 $statusClass = "is-completed";
             }
             if ($result->status == 2 && $result->cancelby == "u") {
                 $statusText = "Bạn đã hủy vào " . $result->upddate;
                 $statusClass = "is-cancelled";
             }
             if ($result->status == 2 && $result->cancelby == "a") {
                 $statusText = "Quản trị viên đã hủy vào " . $result->upddate;
                 $statusClass = "is-cancelled";
             }
             ?>
						<tr>
							<td><?php echo htmlentities($cnt); ?></td>
							<td>#BK<?php echo htmlentities($result->bookid); ?></td>
							<td><a href="<?php echo BASE_URL; ?>package/details/<?php echo htmlentities(
    $result->pkgid,
); ?>"><?php echo htmlentities($result->packagename); ?></a></td>
							<td><?php echo htmlentities($result->fromdate); ?></td>
							<td><?php echo htmlentities($result->comment); ?></td>
							<td><span class="status-chip <?php echo $statusClass; ?>"><?php echo htmlentities(
    $statusText,
); ?></span></td>
							<td><?php echo htmlentities($result->regdate); ?></td>
							<td>
								<?php if ($result->status == 2) { ?>
									<span class="link-muted">Đã hủy</span>
								<?php } elseif ($result->status == 3) { ?>
									<?php if (!empty($result->hasreview)) { ?>
										<span class="link-muted">Đã đánh giá</span>
									<?php } else { ?>
										<button
											type="button"
											class="btn btn-secondary btn-compact js-open-review"
											data-booking-id="<?php echo htmlentities($result->bookid); ?>"
											data-package-id="<?php echo htmlentities($result->pkgid); ?>"
										>Đánh giá và nhận xét</button>
									<?php } ?>
								<?php } elseif ((int) $result->status === 0) { ?>
									<a class="btn-link" href="<?php echo BASE_URL; ?>tour/cancel/<?php echo htmlentities(
    $result->bookid,
); ?>" onclick="return confirm('Bạn có chắc chắn muốn hủy đặt tour này không?');">Hủy</a>
								<?php } else { ?>
									<span class="link-muted">Không khả dụng</span>
								<?php } ?>
							</td>
						</tr>
						<?php if (!empty($result->customermessage)): ?>
						<tr style="background-color: #FFF4F0; border-top: 1px solid #FFE0CC;">
							<td colspan="8">
								<div style="padding: 1rem 1rem 1rem 2rem;">
									<strong style="color: #FF7F50; font-size: 1rem;">📨 Lời nhắn từ GoTravel</strong>
									<p style="margin: 0.75rem 0 0 0; color: #333; line-height: 1.5;">
										<?php echo nl2br(htmlentities($result->customermessage)); ?>
									</p>
								</div>
							</td>
						</tr>
						<?php endif; ?>
						<?php $cnt++;
         }
     } else {
          ?>
						<tr><td colspan="8"><div class="empty-state">Bạn chưa có đặt tour nào.</div></td></tr>
						<?php
     }
     ?>
					</tbody>
				</table>
			</div>
		</section>
	</div>
</main>
<?php include ROOT . "/includes/footer.php"; ?>
<?php include ROOT . "/includes/signup.php"; ?>
<?php include ROOT . "/includes/signin.php"; ?>
<?php include ROOT . "/includes/write-us.php"; ?>
<div class="modal" id="reviewModal" aria-hidden="true">
	<div class="modal__dialog">
		<button type="button" class="modal__close" id="reviewModalClose" aria-label="Đóng">&times;</button>
		<h3>Đánh giá và nhận xét tour</h3>
		<form id="reviewForm" class="form-stack">
			<input type="hidden" name="booking_id" id="reviewBookingId">
			<input type="hidden" name="package_id" id="reviewPackageId">
			<input type="hidden" name="rating" id="reviewRating" value="">
			<div class="form-group">
				<label>Đánh giá sao</label>
				<div class="rating-stars" id="ratingStars" role="radiogroup" aria-label="Chọn số sao">
					<button type="button" class="rating-star" data-value="1" aria-label="1 sao">&#9733;</button>
					<button type="button" class="rating-star" data-value="2" aria-label="2 sao">&#9733;</button>
					<button type="button" class="rating-star" data-value="3" aria-label="3 sao">&#9733;</button>
					<button type="button" class="rating-star" data-value="4" aria-label="4 sao">&#9733;</button>
					<button type="button" class="rating-star" data-value="5" aria-label="5 sao">&#9733;</button>
				</div>
				<p class="helper-text" id="ratingHint">Chọn số sao bạn muốn đánh giá.</p>
			</div>
			<div class="form-group">
				<label for="reviewComment">Nhận xét</label>
				<textarea id="reviewComment" name="comment" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về chuyến đi..."></textarea>
			</div>
			<button type="submit" class="btn w-100">Gửi đánh giá</button>
		</form>
	</div>
</div>
<script>
(function () {
	var modal = document.getElementById('reviewModal');
	var closeBtn = document.getElementById('reviewModalClose');
	var form = document.getElementById('reviewForm');
	var bookingInput = document.getElementById('reviewBookingId');
	var packageInput = document.getElementById('reviewPackageId');
	var ratingInput = document.getElementById('reviewRating');
	var stars = Array.prototype.slice.call(document.querySelectorAll('#ratingStars .rating-star'));
	var ratingHint = document.getElementById('ratingHint');
	var ratingLabels = {
		1: '1 sao - Chưa hài lòng',
		2: '2 sao - Tạm ổn',
		3: '3 sao - Tốt',
		4: '4 sao - Rất tốt',
		5: '5 sao - Tuyệt vời'
	};

	function updateStars(value) {
		var selected = parseInt(value || 0, 10);
		stars.forEach(function (star, index) {
			if (index < selected) {
				star.classList.add('is-active');
			} else {
				star.classList.remove('is-active');
			}
		});
		ratingHint.textContent = selected ? ratingLabels[selected] : 'Chọn số sao bạn muốn đánh giá.';
	}

	function closeModal() {
		modal.classList.remove('is-visible');
		modal.setAttribute('aria-hidden', 'true');
	}

	document.querySelectorAll('.js-open-review').forEach(function (btn) {
		btn.addEventListener('click', function () {
			bookingInput.value = btn.getAttribute('data-booking-id') || '';
			packageInput.value = btn.getAttribute('data-package-id') || '';
			form.reset();
			ratingInput.value = '';
			updateStars(0);
			modal.classList.add('is-visible');
			modal.setAttribute('aria-hidden', 'false');
		});
	});
	stars.forEach(function (star) {
		star.addEventListener('click', function () {
			var value = star.getAttribute('data-value');
			ratingInput.value = value;
			updateStars(value);
		});
	});

	closeBtn.addEventListener('click', closeModal);
	modal.addEventListener('click', function (event) {
		if (event.target === modal) {
			closeModal();
		}
	});

	form.addEventListener('submit', function (event) {
		event.preventDefault();
		if (!ratingInput.value) {
			alert('Vui lòng chọn số sao trước khi gửi đánh giá.');
			return;
		}
		var submitBtn = form.querySelector('button[type="submit"]');
		submitBtn.disabled = true;
		submitBtn.textContent = 'Đang gửi...';

		var payload = new URLSearchParams(new FormData(form));
		fetch('<?php echo BASE_URL; ?>review/submit', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			body: payload.toString()
		})
			.then(function (response) { return response.json(); })
			.then(function (result) {
				alert(result.message || 'Đã xử lý yêu cầu.');
				if (result.status === 'success') {
					window.location.reload();
				}
			})
			.catch(function () {
				alert('Không thể gửi đánh giá. Vui lòng thử lại.');
			})
			.finally(function () {
				submitBtn.disabled = false;
				submitBtn.textContent = 'Gửi đánh giá';
			});
	});
})();
</script>
</body>
</html>
