<?php
session_start();
error_reporting(0);
include('includes/config.php');
if (strlen($_SESSION['alogin']) == 0) {
	header('location:index.php');
	exit;
}

$pageTitle = "GoTravel Admin | Chi tiết người dùng";
$currentPage = 'manage-users';

$userId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$user = null;

if ($userId <= 0) {
	$error = "Người dùng không tồn tại.";
} else {
	$sql = "SELECT id, FullName, MobileNumber, EmailId, Address, DateOfBirth, Gender, RegDate FROM tblusers WHERE id = :id LIMIT 1";
	$query = $dbh->prepare($sql);
	$query->bindParam(':id', $userId, PDO::PARAM_INT);
	$query->execute();
	$user = $query->fetch(PDO::FETCH_OBJ);
	if (!$user) {
		$error = "Người dùng không tồn tại.";
	}
}

include('includes/layout-start.php');

function renderField($value) {
	$text = trim((string)$value);
	return $text !== '' ? htmlentities($text) : '<span class="detail-empty">Trống</span>';
}
?>
	<section class="admin-page-head">
		<div>
			<h1>Chi tiết người dùng</h1>
			<p>Thông tin cá nhân do người dùng đã điền.</p>
		</div>
		<a class="btn btn-ghost" href="<?php echo BASE_URL; ?>admin/manage-users.php">← Quay lại</a>
	</section>

	<?php if ($error) { ?>
		<div class="alert error"><?php echo htmlentities($error); ?></div>
	<?php } else { ?>
		<section class="card detail-shell">
			<div class="detail-header">
				<div class="detail-title">
					<h2>
						<?php echo $user->FullName ? htmlentities($user->FullName) : 'Người dùng #' . htmlentities($user->id); ?>
					</h2>
					<div class="detail-subtitle">
						ID: <?php echo htmlentities($user->id); ?> · Ngày đăng ký: <?php echo $user->RegDate ? htmlentities($user->RegDate) : 'Trống'; ?>
					</div>
				</div>
			</div>

			<div class="detail-grid">
				<div class="detail-item">
					<div class="detail-label">Họ và tên</div>
					<div class="detail-value"><?php echo renderField($user->FullName); ?></div>
				</div>
				<div class="detail-item">
					<div class="detail-label">Số điện thoại</div>
					<div class="detail-value"><?php echo renderField($user->MobileNumber); ?></div>
				</div>
				<div class="detail-item">
					<div class="detail-label">Email</div>
					<div class="detail-value"><?php echo renderField($user->EmailId); ?></div>
				</div>
				<div class="detail-item" style="grid-column: 1 / -1;">
					<div class="detail-label">Địa chỉ</div>
					<div class="detail-value"><?php echo renderField($user->Address); ?></div>
				</div>
				<div class="detail-item">
					<div class="detail-label">Ngày sinh</div>
					<div class="detail-value"><?php echo renderField($user->DateOfBirth); ?></div>
				</div>
				<div class="detail-item">
					<div class="detail-label">Giới tính</div>
					<div class="detail-value"><?php echo renderField($user->Gender); ?></div>
				</div>
			</div>
		</section>
	<?php } ?>

<?php include('includes/layout-end.php'); ?>

