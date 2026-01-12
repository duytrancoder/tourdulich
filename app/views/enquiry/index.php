<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Liên hệ</title>
	<link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
</head>
<body style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('<?php echo BASE_URL; ?>admin/packageimages/tour_halong.webp') no-repeat center center; background-size: cover;">
<?php include ROOT . "/includes/header.php"; ?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1 style="color: #fff">Liên hệ đội ngũ GoTravel</h1>
			<p style="color: #e5e7eb">Gửi câu hỏi về tour, thanh toán hoặc hợp tác. Chúng tôi phản hồi trong 2 giờ.</p>
		</section>
		<section class="card" style="background: transparent; border: none;">
			<?php if (
       $data["error"]
   ) { ?><div class="alert error"><strong>Lỗi:</strong> <?php echo htmlentities(
    $data["error"],
); ?> </div><?php } elseif (
       $data["msg"]
   ) { ?><div class="alert success"><strong>Thành công:</strong> <?php echo htmlentities(
    $data["msg"],
); ?> </div><?php } ?>
			<form name="enquiry" method="post" class="form-stack" action="<?php echo BASE_URL; ?>enquiry/submit">
				<div class="form-grid">
					<div class="form-group">
						<label for="fname" style="color: #fff">Họ và tên</label>
						<input type="text" name="fname" id="fname" placeholder="Nguyễn Văn A" required>
					</div>
					<div class="form-group">
						<label for="email" style="color: #fff">Email</label>
						<input type="email" name="email" id="email" placeholder="ban@example.com" required>
					</div>
					<div class="form-group">
						<label for="mobileno" style="color: #fff">Số điện thoại</label>
						<input type="text" name="mobileno" id="mobileno" maxlength="10" placeholder="10 chữ số" required>
					</div>
					<div class="form-group">
						<label for="subject" style="color: #fff">Chủ đề</label>
						<input type="text" name="subject" id="subject" placeholder="Ví dụ: Tour Đà Lạt" required>
					</div>
				</div>
				<div class="form-group">
					<label for="description" style="color: #fff">Nội dung</label>
					<textarea name="description" id="description" rows="5" placeholder="Chia sẻ chi tiết nhu cầu của bạn" required></textarea>
				</div>
				<button type="submit" name="submit1" class="btn">Gửi yêu cầu</button>
			</form>
		</section>
	</div>
</main>
<?php include ROOT . "/includes/footer.php"; ?>
<?php include ROOT . "/includes/signup.php"; ?>
<?php include ROOT . "/includes/signin.php"; ?>
<?php include ROOT . "/includes/write-us.php"; ?>
</body>
</html>
