<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['login'])==0)
{
	header('location:index.php');
}
else{
if(isset($_POST['submit']))
{
$name=$_POST['name'];
$mobileno=$_POST['mobileno'];
$email=$_SESSION['login'];
$sql="update tblusers set FullName=:name,MobileNumber=:mobileno where EmailId=:email";
$query = $dbh->prepare($sql);
$query-> bindParam(':name',$name,PDO::PARAM_STR);
$query-> bindParam(':mobileno',$mobileno,PDO::PARAM_STR);
$query-> bindParam(':email',$email,PDO::PARAM_STR);
$query->execute();
$msg="Hồ sơ đã được cập nhật";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel | Hồ sơ của tôi</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include('includes/header.php');?>
<main class="page">
	<div class="container">
		<section class="page-head">
			<h1>Thông tin tài khoản</h1>
			<p>Cập nhật họ tên, số điện thoại và xem email đang sử dụng.</p>
		</section>
		<?php if($error){?><div class="alert error"><strong>Lỗi:</strong> <?php echo htmlentities($error); ?></div><?php } elseif($msg){?><div class="alert success"><strong>Thành công:</strong> <?php echo htmlentities($msg); ?></div><?php }?>
		<section class="card">
		<?php 
		$useremail=$_SESSION['login'];
		$sql = "SELECT * from tblusers where EmailId=:useremail";
		$query = $dbh -> prepare($sql);
		$query -> bindParam(':useremail',$useremail, PDO::PARAM_STR);
		$query->execute();
		$results=$query->fetchAll(PDO::FETCH_OBJ);
		if($query->rowCount() > 0)
		{
			foreach($results as $result)
			{
		?>
			<form name="profile" method="post" class="form-stack">
				<div class="form-group">
					<label for="name">Họ và tên</label>
					<input type="text" name="name" id="name" value="<?php echo htmlentities($result->FullName);?>" required>
				</div>
				<div class="form-group">
					<label for="mobileno">Số điện thoại</label>
					<input type="text" name="mobileno" id="mobileno" maxlength="10" value="<?php echo htmlentities($result->MobileNumber);?>" required>
				</div>
				<div class="form-group">
					<label>Email</label>
					<input type="email" value="<?php echo htmlentities($result->EmailId);?>" disabled>
				</div>
				<div class="form-group">
					<label>Ngày đăng ký</label>
					<input type="text" value="<?php echo htmlentities($result->RegDate);?>" disabled>
				</div>
				<?php if($result->UpdationDate){?>
				<div class="form-group">
					<label>Ngày cập nhật</label>
					<input type="text" value="<?php echo htmlentities($result->UpdationDate);?>" disabled>
				</div>
				<?php } ?>
				<button type="submit" name="submit" class="btn">Lưu thay đổi</button>
			</form>
		<?php }} ?>
		</section>
	</div>
</main>
<?php include('includes/footer.php');?>
<?php include('includes/signup.php');?>
<?php include('includes/signin.php');?>
<?php include('includes/write-us.php');?>
</body>
</html>
<?php } ?>
