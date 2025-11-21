<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Kiểm tra đã đăng nhập
if(strlen($_SESSION['login'])==0)
{
	header('location:index.php');
	exit;
}

if(isset($_POST['submit']))
{
	$issue=trim($_POST['issue']);
	$description=trim($_POST['description']);	
	$email=$_SESSION['login'];
	
	if(!empty($issue) && !empty($description)) {
		try {
			$sql="INSERT INTO tblissues(UserEmail,Issue,Description) VALUES(:email,:issue,:description)";
			$query = $dbh->prepare($sql);
			$query->bindParam(':email',$email,PDO::PARAM_STR);
			$query->bindParam(':issue',$issue,PDO::PARAM_STR);
			$query->bindParam(':description',$description,PDO::PARAM_STR);
			$query->execute();
			$lastInsertId = $dbh->lastInsertId();
			if($lastInsertId)
			{
				$_SESSION['msg']="Yêu cầu hỗ trợ của bạn đã được gửi. Chúng tôi sẽ phản hồi sớm nhất có thể.";
				header('location:issuetickets.php');
				exit;
			} else {
				$_SESSION['error']="Không thể lưu yêu cầu. Vui lòng thử lại.";
				header('location:issuetickets.php');
				exit;
			}
		} catch(PDOException $e) {
			$_SESSION['error']="Có lỗi xảy ra: " . $e->getMessage();
			header('location:issuetickets.php');
			exit;
		}
	} else {
		$_SESSION['error']="Vui lòng điền đầy đủ thông tin.";
		header('location:issuetickets.php');
		exit;
	}
} else {
	header('location:index.php');
	exit;
}
?>

