<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])==0)
{
	header('location:index.php');
	exit;
}

if(isset($_REQUEST['eid']))
{
	$eid=intval($_GET['eid']);
	$status=1;
	$sql = "UPDATE tblenquiry SET Status=:status WHERE  id=:eid";
	$query = $dbh->prepare($sql);
	$query -> bindParam(':status',$status, PDO::PARAM_STR);
	$query-> bindParam(':eid',$eid, PDO::PARAM_STR);
	$query -> execute();
	$msg="Đã đánh dấu yêu cầu hỗ trợ";
}

if(isset($_GET['del']))
{
	$delid=intval($_GET['del']);
	$sql = "DELETE FROM tblissues WHERE id=:delid";
	$query = $dbh->prepare($sql);
	$query -> bindParam(':delid',$delid, PDO::PARAM_STR);
	$query -> execute();
	$msg="Đã xóa yêu cầu hỗ trợ";
}

$pageTitle = "GoTravel Admin | Yêu cầu hỗ trợ";
$currentPage = 'manage-issues';
$sql = "SELECT tblissues.id as id,tblusers.FullName as fname,tblusers.MobileNumber as mnumber,tblusers.EmailId as email,tblissues.Issue as issue,tblissues.Description as Description,tblissues.PostingDate as PostingDate from tblissues join tblusers on tblusers.EmailId=tblissues.UserEmail";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
include('includes/layout-start.php');
?>
	<section class="admin-page-head">
		<div>
			<h1>Yêu cầu hỗ trợ</h1>
			<p>Theo dõi các vấn đề khách hàng gửi lên và phản hồi kịp thời.</p>
		</div>
	</section>
	<?php if($error){?><div class="alert error"><?php echo htmlentities($error);?></div><?php } ?>
	<?php if($msg){?><div class="alert success"><?php echo htmlentities($msg);?></div><?php } ?>
	<section class="card">
		<div class="table-responsive">
			<table class="table">
				<thead>
					<tr>
						<th>#</th>
						<th>Khách hàng</th>
						<th>Liên hệ</th>
						<th>Chủ đề</th>
						<th>Mô tả</th>
						<th>Ngày gửi</th>
						<th>Thao tác</th>
					</tr>
				</thead>
				<tbody>
				<?php if($query->rowCount() > 0) { $cnt=1; foreach($results as $result) { ?>
				<tr>
					<td>#00<?php echo htmlentities($result->id);?></td>
					<td><?php echo htmlentities($result->fname);?></td>
					<td>
						<div><?php echo htmlentities($result->mnumber);?></div>
						<div class="helper-text"><?php echo htmlentities($result->email);?></div>
					</td>
					<td><?php echo htmlentities($result->issue);?></td>
					<td><?php echo htmlentities($result->Description);?></td>
					<td><?php echo htmlentities($result->PostingDate);?></td>
					<td>
						<a class="btn btn-ghost" href="updateissue.php?iid=<?php echo htmlentities($result->id);?>" target="_blank">Xem &amp; ghi chú</a>
						<a class="btn btn-danger" href="manageissues.php?del=<?php echo htmlentities($result->id);?>" onclick="return confirm('Bạn có chắc chắn muốn xóa yêu cầu này không?');">Xóa</a>
					</td>
				</tr>
				<?php $cnt++; } } else { ?>
				<tr><td colspan="7"><div class="empty-state">Chưa có yêu cầu hỗ trợ nào.</div></td></tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</section>
<?php include('includes/layout-end.php');?>
