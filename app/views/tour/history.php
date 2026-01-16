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
								<?php } else { ?>
									<a class="btn-link" href="<?php echo BASE_URL; ?>tour/cancel/<?php echo htmlentities(
    $result->bookid,
); ?>" onclick="return confirm('Bạn có chắc chắn muốn hủy đặt tour này không?');">Hủy</a>
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
</body>
</html>
