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
	<title>GoTravel | Giới thiệu</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include('includes/header.php');?>
<main class="page">
	<div class="container">
		<?php 
$pagetype=$_GET['type'];
$titleMap = array(
	'aboutus' => 'Giới thiệu',
	'privacy' => 'Chính sách bảo mật',
	'terms' => 'Điều khoản sử dụng',
	'contact' => 'Liên hệ',
);
$titleText = isset($titleMap[$pagetype]) ? $titleMap[$pagetype] : ucfirst($pagetype);
$sql = "SELECT type,detail from tblpages where type=:pagetype";
$query = $dbh -> prepare($sql);
$query->bindParam(':pagetype',$pagetype,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
?>
		<section class="card">
			<h1><?php echo htmlentities($titleText); ?></h1>
			<?php if($query->rowCount() > 0): ?>
				<?php foreach($results as $result): 
					
					$content = $result->detail;
					$content = preg_replace('/<FONT[^>]*>/i', '', $content);
					$content = str_replace('</FONT>', '', $content);
					$content = preg_replace('/<P align=[^>]*>/i', '<p>', $content);
					$content = str_replace('</P>', '</p>', $content);
					$content = preg_replace('/<STRONG>/i', '<strong>', $content);
					$content = str_replace('</STRONG>', '</strong>', $content);
				?>
					<div class="page-content"><?php echo $content; ?></div>
				<?php endforeach; ?>
			<?php else: ?>
				<p>Chưa có nội dung cho trang này.</p>
			<?php endif; ?>
		</section>
	</div>
</main>
<?php include('includes/footer.php');?>
<?php include('includes/signup.php');?>
<?php include('includes/signin.php');?>
<?php include('includes/write-us.php');?>
</body>
</html>
