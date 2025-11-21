<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(isset($_POST['submit1']))
{
$fname=$_POST['fname'];
$email=$_POST['email'];	
$mobile=$_POST['mobileno'];
$subject=$_POST['subject'];	
$description=$_POST['description'];
$sql="INSERT INTO  tblenquiry(FullName,EmailId,MobileNumber,Subject,Description) VALUES(:fname,:email,:mobile,:subject,:description)";
$query = $dbh->prepare($sql);
$query->bindParam(':fname',$fname,PDO::PARAM_STR);
$query->bindParam(':email',$email,PDO::PARAM_STR);
$query->bindParam(':mobile',$mobile,PDO::PARAM_STR);
$query->bindParam(':subject',$subject,PDO::PARAM_STR);
$query->bindParam(':description',$description,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
$msg="Bạn đã gửi yêu cầu thành công";
}
else 
{
$error="Có lỗi xảy ra. Vui lòng thử lại";
}
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Liên hệ</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include('includes/header.php');?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1>Liên hệ đội ngũ GoTravel</h1>
			<p>Gửi câu hỏi về tour, thanh toán hoặc hợp tác. Chúng tôi phản hồi trong 2 giờ.</p>
		</section>
		<section class="card">
			<?php if($error){?><div class="alert error"><strong>Lỗi:</strong> <?php echo htmlentities($error); ?> </div><?php } elseif($msg){?><div class="alert success"><strong>Thành công:</strong> <?php echo htmlentities($msg); ?> </div><?php }?>
			<form name="enquiry" method="post" class="form-stack">
				<div class="form-grid">
					<div class="form-group">
						<label for="fname">Họ và tên</label>
						<input type="text" name="fname" id="fname" placeholder="Nguyễn Văn A" required>
					</div>
					<div class="form-group">
						<label for="email">Email</label>
						<input type="email" name="email" id="email" placeholder="ban@example.com" required>
					</div>
					<div class="form-group">
						<label for="mobileno">Số điện thoại</label>
						<input type="text" name="mobileno" id="mobileno" maxlength="10" placeholder="10 chữ số" required>
					</div>
					<div class="form-group">
						<label for="subject">Chủ đề</label>
						<input type="text" name="subject" id="subject" placeholder="Ví dụ: Tour Đà Lạt" required>
					</div>
				</div>
				<div class="form-group">
					<label for="description">Nội dung</label>
					<textarea name="description" id="description" rows="5" placeholder="Chia sẻ chi tiết nhu cầu của bạn" required></textarea>
				</div>
				<button type="submit" name="submit1" class="btn">Gửi yêu cầu</button>
			</form>
		</section>
	</div>
</main>
<?php include('includes/footer.php');?>
<?php include('includes/signup.php');?>
<?php include('includes/signin.php');?>
<?php include('includes/write-us.php');?>
</body>
</html>
