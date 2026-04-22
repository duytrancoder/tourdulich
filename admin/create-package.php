<?php
session_start();
error_reporting(0);
include('includes/config.php');
require_once dirname(__DIR__) . '/core/Helper.php';
Helper::requireAdminLogin();

$packageCreated = false;
$newPackageId = null;

// Handle Itinerary Add (only if package exists)
if(isset($_POST['addItinerary']) && isset($_GET['pid'])) {
	$pid = intval($_GET['pid']);
	$timeLabel = $_POST['timeLabel'];
	$activity = $_POST['activity'];
	
	$sql = "SELECT COALESCE(MAX(SortOrder), 0) + 1 as NextOrder FROM tblitinerary WHERE PackageId = :pid";
	$query = $dbh->prepare($sql);
	$query->bindParam(':pid', $pid, PDO::PARAM_INT);
	$query->execute();
	$result = $query->fetch(PDO::FETCH_OBJ);
	$sortOrder = $result->NextOrder;
	
	$sql = "INSERT INTO tblitinerary (PackageId, TimeLabel, Activity, SortOrder) VALUES (:pid, :timeLabel, :activity, :sortOrder)";
	$query = $dbh->prepare($sql);
	$query->bindParam(':pid', $pid, PDO::PARAM_INT);
	$query->bindParam(':timeLabel', $timeLabel, PDO::PARAM_STR);
	$query->bindParam(':activity', $activity, PDO::PARAM_STR);
	$query->bindParam(':sortOrder', $sortOrder, PDO::PARAM_INT);
	$query->execute();
	
	$itineraryMsg = "Đã thêm lộ trình thành công";
}

// Handle Itinerary Update
if(isset($_POST['updateItinerary']) && isset($_GET['pid'])) {
	$pid = intval($_GET['pid']);
	$id = intval($_POST['itineraryId']);
	$timeLabel = $_POST['timeLabel'];
	$activity = $_POST['activity'];
	$sortOrder = intval($_POST['sortOrder']);
	
	$sql = "UPDATE tblitinerary SET TimeLabel = :timeLabel, Activity = :activity, SortOrder = :sortOrder WHERE ItineraryId = :id";
	$query = $dbh->prepare($sql);
	$query->bindParam(':id', $id, PDO::PARAM_INT);
	$query->bindParam(':timeLabel', $timeLabel, PDO::PARAM_STR);
	$query->bindParam(':activity', $activity, PDO::PARAM_STR);
	$query->bindParam(':sortOrder', $sortOrder, PDO::PARAM_INT);
	$query->execute();
	
	$itineraryMsg = "Đã cập nhật lộ trình thành công";
}

// Handle Itinerary Delete
if(isset($_GET['delItinerary']) && isset($_GET['pid'])) {
	$pid = intval($_GET['pid']);
	$id = intval($_GET['delItinerary']);
	$sql = "DELETE FROM tblitinerary WHERE ItineraryId = :id";
	$query = $dbh->prepare($sql);
	$query->bindParam(':id', $id, PDO::PARAM_INT);
	$query->execute();
	
	$itineraryMsg = "Đã xóa lộ trình thành công";
	header('Location: ' . BASE_URL . 'admin/create-package.php?pid=' . $pid);
	exit;
}

// Handle Package Creation
if(isset($_POST['submit']))
{
$pname = trim($_POST['packagename'] ?? '');
$ptype = trim($_POST['packagetype'] ?? '');	
$plocation = trim($_POST['packagelocation'] ?? '');
$tourduration = trim($_POST['tourduration'] ?? '');
$pprice = intval($_POST['packageprice'] ?? 0);	
$pfeatures = trim($_POST['packagefeatures'] ?? '');
$pdetails = trim($_POST['packagedetails'] ?? '');	
$pimage = '';

// Get itinerary data from hidden field
$itineraryData = isset($_POST['itineraryData']) ? $_POST['itineraryData'] : '';

// Validate inputs
if (empty($pname) || empty($ptype) || empty($plocation) || empty($tourduration) || $pprice <= 0 || empty($pfeatures) || empty($pdetails)) {
	$error = "Vui lòng điền đầy đủ thông tin";
} elseif (!isset($_FILES["packageimage"]) || $_FILES["packageimage"]["error"] !== UPLOAD_ERR_OK) {
	$error = "Vui lòng chọn hình ảnh";
} else {
	// Validate file upload using Helper class
	$validation = Helper::validateImage($_FILES["packageimage"]);
	if (!$validation['valid']) {
		$error = $validation['error'];
	} else {
		// Sanitize filename
		$pimage = Helper::sanitizeFilename($_FILES["packageimage"]["name"]);
		$uploadPath = "packageimages/" . $pimage;
		
		if (move_uploaded_file($_FILES["packageimage"]["tmp_name"], $uploadPath)) {
			// File uploaded successfully, continue with database insert
		} else {
			$error = "Không thể tải lên file. Vui lòng thử lại";
		}
	}
}

if (!isset($error)) {
	try {
		// Start transaction
		$dbh->beginTransaction();
		
		// Insert package
		$sql="INSERT INTO tbltourpackages(PackageName,PackageType,PackageLocation,TourDuration,PackagePrice,PackageFetures,PackageDetails,PackageImage) VALUES(:pname,:ptype,:plocation,:tourduration,:pprice,:pfeatures,:pdetails,:pimage)";
		$query = $dbh->prepare($sql);
		$query->bindParam(':pname',$pname,PDO::PARAM_STR);
		$query->bindParam(':ptype',$ptype,PDO::PARAM_STR);
		$query->bindParam(':plocation',$plocation,PDO::PARAM_STR);
		$query->bindParam(':tourduration',$tourduration,PDO::PARAM_STR);
		$query->bindParam(':pprice',$pprice,PDO::PARAM_INT);
		$query->bindParam(':pfeatures',$pfeatures,PDO::PARAM_STR);
		$query->bindParam(':pdetails',$pdetails,PDO::PARAM_STR);
		$query->bindParam(':pimage',$pimage,PDO::PARAM_STR);
		$query->execute();
		$lastInsertId = $dbh->lastInsertId();
		
		// Insert itineraries if any
		if($lastInsertId && !empty($itineraryData)) {
			$itineraries = json_decode($itineraryData, true);
			if(is_array($itineraries) && count($itineraries) > 0) {
				$sqlItinerary = "INSERT INTO tblitinerary (PackageId, TimeLabel, Activity, SortOrder) VALUES (:pid, :timeLabel, :activity, :sortOrder)";
				$queryItinerary = $dbh->prepare($sqlItinerary);
				
				foreach($itineraries as $item) {
					$queryItinerary->bindParam(':pid', $lastInsertId, PDO::PARAM_INT);
					$queryItinerary->bindParam(':timeLabel', $item['timeLabel'], PDO::PARAM_STR);
					$queryItinerary->bindParam(':activity', $item['activity'], PDO::PARAM_STR);
					$queryItinerary->bindParam(':sortOrder', $item['sortOrder'], PDO::PARAM_INT);
					$queryItinerary->execute();
				}
			}
		}
		
		// Commit transaction
		$dbh->commit();
		
		if($lastInsertId) {
			$packageCreated = true;
			$newPackageId = $lastInsertId;
			$itineraryCount = is_array(json_decode($itineraryData, true)) ? count(json_decode($itineraryData, true)) : 0;
			$msg = "Tạo gói tour thành công! " . ($itineraryCount > 0 ? "Đã thêm $itineraryCount lộ trình." : "Bạn có thể thêm lộ trình bên dưới.");
			// Redirect to same page with package ID to enable itinerary management
			header('Location: ' . BASE_URL . 'admin/create-package.php?pid=' . $lastInsertId . '&created=1');
			exit;
		} else {
			$error="Có lỗi xảy ra. Vui lòng thử lại";
		}
	} catch(Exception $e) {
		// Rollback on error
		$dbh->rollBack();
		$error = "Có lỗi xảy ra: " . $e->getMessage();
	}
}
}

// Check if we're viewing a created package
if(isset($_GET['pid'])) {
	$pid = intval($_GET['pid']);
	$packageCreated = true;
	$newPackageId = $pid;
	
	// Get package info
	$sql = "SELECT * FROM tbltourpackages WHERE PackageId = :pid";
	$query = $dbh->prepare($sql);
	$query->bindParam(':pid', $pid, PDO::PARAM_INT);
	$query->execute();
	$package = $query->fetch(PDO::FETCH_OBJ);
	
	// Get itineraries
	$sql = "SELECT * FROM tblitinerary WHERE PackageId = :pid ORDER BY SortOrder ASC, ItineraryId ASC";
	$query = $dbh->prepare($sql);
	$query->bindParam(':pid', $pid, PDO::PARAM_INT);
	$query->execute();
	$itineraries = $query->fetchAll(PDO::FETCH_OBJ);
	
	if(isset($_GET['created'])) {
		$msg = "Tạo gói tour thành công! Bây giờ bạn có thể thêm lộ trình chi tiết bên dưới.";
	}
}

	$pageTitle = "GoTravel Admin | Tạo gói tour";
	$currentPage = 'create-package';
	include('includes/layout-start.php');
	?>
		<section class="admin-page-head">
			<div>
				<h1><?php echo $packageCreated ? 'Hoàn thiện gói tour' : 'Tạo gói tour'; ?></h1>
				<p><?php echo $packageCreated ? 'Gói tour đã được tạo. Thêm lộ trình chi tiết để hoàn thiện.' : 'Thêm nhanh gói tour mới với đầy đủ thông tin và hình ảnh.'; ?></p>
			</div>
			<?php if($packageCreated) { ?>
				<a class="btn btn-ghost" href="<?php echo BASE_URL; ?>admin/manage-packages.php">← Quay lại danh sách</a>
			<?php } ?>
		</section>
		<?php if($error){?><div class="alert error"><?php echo htmlentities($error); ?> </div><?php } ?>
		<?php if($msg){?><div class="alert success"><?php echo htmlentities($msg); ?> </div><?php } ?>
		<?php if(isset($itineraryMsg)){?><div class="alert success"><?php echo htmlentities($itineraryMsg); ?></div><?php } ?>
		
		<?php if(!$packageCreated) { ?>
		<!-- Package Creation Form -->
		<section class="card">
			<h3>Thông tin gói tour</h3>
			<form name="package" method="post" enctype="multipart/form-data" class="form-stack" id="packageForm">
				<input type="hidden" name="itineraryData" id="itineraryDataInput" value="">
				<div class="form-grid">
					<div class="form-group">
						<label for="packagename">Tên gói</label>
						<input type="text" name="packagename" id="packagename" required>
					</div>
					<div class="form-group">
						<label for="packagetype" style="display: flex; align-items: center; gap: 0.5rem;">
							Loại gói
							<span class="tooltip-icon" onclick="openPackageTypeModal()" title="Xem chi tiết các loại gói" style="cursor: pointer; display: inline-flex; justify-content: center; align-items: center; width: 18px; height: 18px; border-radius: 50%; background: var(--primary); color: white; font-size: 0.8rem; font-weight: bold;">?</span>
						</label>
						<select name="packagetype" id="packagetype" required>
							<option value="">-- Chọn loại gói --</option>
							<option value="Tour tiết kiệm">Tour tiết kiệm</option>
							<option value="Tour tiêu chuẩn">Tour tiêu chuẩn</option>
							<option value="Tour cao cấp">Tour cao cấp</option>
							<option value="Tour riêng">Tour riêng</option>
						</select>
					</div>
					<div class="form-group">
						<label for="packagelocation">Địa điểm</label>
						<input type="text" name="packagelocation" id="packagelocation" required>
					</div>
					<div class="form-group">					<label for="tourduration">Thời gian tour</label>
					<input type="text" name="tourduration" id="tourduration" placeholder="VD: 2 Ngày 1 Đêm / 5 Ngày 4 Đêm / Trong ngày" required>
				</div>
				<div class="form-group">						<label for="packageprice">Giá gói (VNĐ)</label>
						<input type="number" min="0" step="1000" name="packageprice" id="packageprice" required>
						<small style="color: var(--muted); font-size: 0.85rem;">Nhập giá bằng VNĐ. Ví dụ: 4.800.000</small>
					</div>
				</div>
				<div class="form-group">
					<label for="packagefeatures">Điểm nổi bật</label>
					<input type="text" name="packagefeatures" id="packagefeatures" placeholder="Ví dụ: Đưa đón sân bay miễn phí" required>
				</div>
				<div class="form-group">
					<label for="packagedetails">Chi tiết gói</label>
					<textarea name="packagedetails" id="packagedetails" placeholder="Nhập mô tả chi tiết" required></textarea>
				</div>
				<div class="form-group">
					<label for="packageimage">Hình ảnh gói</label>
					<input type="file" name="packageimage" id="packageimage" accept="image/*" required>
				</div>
				<div>
					<button type="submit" name="submit" class="btn btn-primary">Tạo gói tour</button>
					<button type="reset" class="btn btn-ghost">Làm mới</button>
				</div>
			</form>
		</section>
		
		<!-- Itinerary Management Section (Pre-Creation) -->
		<section class="card" style="margin-top: 2rem;">
			<h3>Lộ trình chi tiết (Tùy chọn)</h3>
			<p style="color: var(--muted); margin-bottom: 1.5rem;">Thêm các điểm trong lộ trình tour. Bạn có thể thêm sau khi tạo gói tour.</p>
			
			<div id="itineraryPreviewTable" style="display: none; overflow-x: auto; margin-bottom: 2rem;">
				<table class="table">
					<thead>
						<tr>
							<th style="width: 50px;">STT</th>
							<th style="width: 200px;">Thời gian</th>
							<th>Hoạt động</th>
							<th style="width: 80px;">Thứ tự</th>
							<th style="width: 150px;">Thao tác</th>
						</tr>
					</thead>
					<tbody id="itineraryPreviewBody">
					</tbody>
				</table>
			</div>
			
			<p id="emptyItineraryMsg" style="text-align: center; padding: 2rem; color: var(--muted);">Chưa có lộ trình nào. Hãy thêm lộ trình bên dưới.</p>
			
			<!-- Add Itinerary Form -->
			<div style="background: var(--bg); padding: 1.5rem; border-radius: 8px;">
				<h4 style="margin-bottom: 1rem;">Thêm lộ trình mới</h4>
				<div class="form-stack">
					<div class="form-grid">
						<div class="form-group">
							<label for="newTimeLabel">Thời gian *</label>
							<input type="text" id="newTimeLabel" placeholder="VD: Ngày 1 - Sáng, 08:00 - 10:00">
						</div>
					</div>
					
					<div class="form-group">
						<label for="newActivity">Hoạt động *</label>
						<textarea id="newActivity" placeholder="Mô tả chi tiết hoạt động trong thời gian này..."></textarea>
					</div>
					
					<div style="display: flex; gap: 1rem;">
						<button type="button" onclick="addItineraryItem()" class="btn">Thêm lộ trình</button>
						<button type="button" onclick="clearItineraryForm()" class="btn btn-ghost">Làm mới</button>
					</div>
				</div>
			</div>
		</section>
		<?php } else { ?>
		
		<!-- Package Created - Show Summary -->
		<section class="card">
			<h3>✅ Gói tour đã tạo</h3>
			<div class="form-grid">
				<div><strong>Tên gói:</strong> <?php echo htmlentities($package->PackageName); ?></div>
				<div><strong>Loại:</strong> <?php echo htmlentities($package->PackageType); ?></div>
				<div><strong>Địa điểm:</strong> <?php echo htmlentities($package->PackageLocation); ?></div>
				<div><strong>Thời gian:</strong> <?php echo htmlentities($package->TourDuration); ?></div>
				<div><strong>Giá:</strong> <?php echo number_format($package->PackagePrice, 0, ',', '.') . ' đ'; ?></div>
			</div>
			<div style="margin-top: 1rem;">
				<a href="<?php echo BASE_URL; ?>admin/update-package.php?pid=<?php echo $newPackageId; ?>" class="btn btn-ghost">Chỉnh sửa thông tin gói</a>
			</div>
		</section>
		
		<!-- Itinerary Management Section -->
		<section class="card" style="margin-top: 2rem;">
			<h3>Quản lý lộ trình chi tiết</h3>
			<p style="color: var(--muted); margin-bottom: 1.5rem;">Thêm các điểm trong lộ trình tour của bạn.</p>
			
			<?php if(count($itineraries) > 0) { ?>
				<div style="overflow-x: auto; margin-bottom: 2rem;">
					<table class="table">
						<thead>
							<tr>
								<th style="width: 50px;">STT</th>
								<th style="width: 200px;">Thời gian</th>
								<th>Hoạt động</th>
								<th style="width: 80px;">Thứ tự</th>
								<th style="width: 150px;">Thao tác</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$cnt = 1;
							foreach($itineraries as $item) { ?>
								<tr data-id="<?php echo $item->ItineraryId; ?>" 
								    data-time="<?php echo htmlspecialchars($item->TimeLabel, ENT_QUOTES); ?>" 
								    data-activity="<?php echo htmlspecialchars($item->Activity, ENT_QUOTES); ?>" 
								    data-sort="<?php echo $item->SortOrder; ?>">
									<td><?php echo $cnt++; ?></td>
									<td><?php echo htmlentities($item->TimeLabel); ?></td>
									<td><?php echo htmlentities($item->Activity); ?></td>
									<td><?php echo $item->SortOrder; ?></td>
									<td>
										<div style="display: flex; gap: 0.5rem;">
											<button type="button" class="btn btn-primary btn-small btn-edit-itinerary">Sửa</button>
											<a href="?pid=<?php echo $newPackageId; ?>&delItinerary=<?php echo $item->ItineraryId; ?>" 
											   class="btn btn-danger btn-small" 
											   onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
										</div>
									</td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			<?php } else { ?>
				<p style="text-align: center; padding: 2rem; color: var(--muted);">Chưa có lộ trình nào. Hãy thêm lộ trình bên dưới.</p>
			<?php } ?>
			
			<!-- Add/Edit Itinerary Form -->
			<div style="background: var(--bg); padding: 1.5rem; border-radius: 8px;">
				<h4 style="margin-bottom: 1rem;" id="itineraryFormTitle">Thêm lộ trình mới</h4>
				<form method="post" id="itineraryForm" class="form-stack">
					<input type="hidden" name="itineraryId" id="itineraryId" value="">
					<input type="hidden" name="sortOrder" id="sortOrder" value="0">
					
					<div class="form-grid">
						<div class="form-group">
							<label for="timeLabel">Thời gian *</label>
							<input type="text" name="timeLabel" id="timeLabel" required 
							       placeholder="VD: Ngày 1 - Sáng, 08:00 - 10:00">
						</div>
					</div>
					
					<div class="form-group">
						<label for="activity">Hoạt động *</label>
						<textarea name="activity" id="activity" required 
						          placeholder="Mô tả chi tiết hoạt động trong thời gian này..."></textarea>
					</div>
					
					<div style="display: flex; gap: 1rem;">
						<button type="submit" name="addItinerary" id="btnAddItinerary" class="btn">Thêm lộ trình</button>
						<button type="submit" name="updateItinerary" id="btnUpdateItinerary" class="btn" style="display: none; background: var(--accent);">Cập nhật</button>
						<button type="button" onclick="resetItineraryForm()" class="btn btn-ghost">Hủy / Làm mới</button>
						<a href="<?php echo BASE_URL; ?>admin/manage-packages.php" class="btn btn-ghost">Hoàn tất & Quay lại</a>
					</div>
				</form>
			</div>
		</section>

		<!-- Package Type Modal -->
		<div id="packageTypeModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
			<div class="modal-content" style="background-color: var(--card-bg, #fff); margin: 10% auto; padding: 2rem; border-radius: 8px; width: 80%; max-width: 600px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
				<span class="close" onclick="closePackageTypeModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
				<h3 style="margin-bottom: 1.5rem; color: var(--text-primary);">Chi tiết các loại gói tour</h3>
				
				<div style="margin-bottom: 1.5rem;">
					<h4 style="color: var(--primary);">1. Gói Tiết kiệm (Economy)</h4>
					<p style="margin-bottom: 0.5rem; line-height: 1.5;"><strong>Mô tả:</strong> "Hành trình tối ưu chi phí nhưng vẫn đảm bảo trải nghiệm trọn vẹn những điểm đến tiêu biểu nhất. Gói này sử dụng hệ thống khách sạn 2-3 sao sạch sẽ, phương tiện di chuyển đời mới và các bữa ăn đặc sản địa phương cơ bản. Lựa chọn hoàn hảo cho các bạn trẻ ưa khám phá hoặc nhóm khách muốn tiết kiệm ngân sách."</p>
					<p style="color: var(--accent); font-weight: 500;">Thông điệp chính: Tiết kiệm tối đa - Khám phá trọn vẹn.</p>
				</div>
				
				<div style="margin-bottom: 1.5rem;">
					<h4 style="color: var(--primary);">2. Gói Tiêu chuẩn (Standard)</h4>
					<p style="margin-bottom: 0.5rem; line-height: 1.5;"><strong>Mô tả:</strong> "Sự kết hợp hoàn hảo giữa chất lượng dịch vụ và mức giá hợp lý. Quý khách sẽ được lưu trú tại hệ thống khách sạn 3-4 sao tiện nghi, thực đơn ăn uống đa dạng và lịch trình được thiết kế cân bằng giữa tham quan và nghỉ ngơi. Đây là dòng tour 'quốc dân' được 80% gia đình và nhân viên văn phòng lựa chọn."</p>
					<p style="color: var(--accent); font-weight: 500;">Thông điệp chính: Dịch vụ chỉn chu - Giá cả hợp lý.</p>
				</div>
				
				<div style="margin-bottom: 1.5rem;">
					<h4 style="color: var(--primary);">3. Gói Cao cấp (Premium)</h4>
					<p style="margin-bottom: 0.5rem; line-height: 1.5;"><strong>Mô tả:</strong> "Nâng tầm trải nghiệm kỳ nghỉ với những dịch vụ đẳng cấp nhất. Quý khách sẽ tận hưởng không gian nghỉ dưỡng tại các resort/khách sạn 4-5 sao sang trọng, di chuyển bằng xe Limousine đời mới hoặc vé máy bay giờ đẹp. Lịch trình bao gồm các điểm tham quan độc quyền, tiệc tối cao cấp và hướng dẫn viên chuyên nghiệp suốt tuyến."</p>
					<p style="color: var(--accent); font-weight: 500;">Thông điệp chính: Đẳng cấp thượng lưu - Trải nghiệm độc bản.</p>
				</div>
				
				<div style="margin-bottom: 1.5rem;">
					<h4 style="color: var(--primary);">4. Gói Tour riêng (Private)</h4>
					<p style="margin-bottom: 0.5rem; line-height: 1.5;"><strong>Mô tả:</strong> "Hoàn toàn riêng tư và linh hoạt theo ý muốn của bạn. Không còn cảnh phải chờ đợi đoàn đông, hành trình này được thiết kế dành riêng cho gia đình hoặc nhóm bạn của bạn. Bạn có thể tự do thay đổi thời gian khởi hành, yêu cầu thêm các điểm check-in yêu thích và có xe cùng hướng dẫn viên phục vụ riêng biệt."</p>
					<p style="color: var(--accent); font-weight: 500;">Thông điệp chính: Tự do tối đa - Cá nhân hóa hành trình.</p>
				</div>
			</div>
		</div>
		
		<script>
		// Modal controls
		function openPackageTypeModal() {
			document.getElementById('packageTypeModal').style.display = 'block';
		}
		
		function closePackageTypeModal() {
			document.getElementById('packageTypeModal').style.display = 'none';
		}
		
		window.onclick = function(event) {
			var modal = document.getElementById('packageTypeModal');
			if (event.target == modal) {
				modal.style.display = 'none';
			}
		}

		// Itinerary management
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.btn-edit-itinerary').forEach(btn => {
				btn.addEventListener('click', function(e) {
					e.stopPropagation();
					const row = this.closest('tr');
					const id = row.dataset.id;
					const timeLabel = row.dataset.time;
					const activity = row.dataset.activity;
					const sortOrder = row.dataset.sort;
					
					editItinerary(id, timeLabel, activity, sortOrder);
				});
			});
		});
		
		function editItinerary(id, timeLabel, activity, sortOrder) {
			document.getElementById('itineraryFormTitle').textContent = 'Chỉnh sửa lộ trình';
			document.getElementById('itineraryId').value = id;
			document.getElementById('timeLabel').value = timeLabel;
			document.getElementById('activity').value = activity;
			document.getElementById('sortOrder').value = sortOrder;
			document.getElementById('btnAddItinerary').style.display = 'none';
			document.getElementById('btnUpdateItinerary').style.display = 'inline-block';
			
			// Scroll to form
			document.getElementById('itineraryForm').scrollIntoView({ behavior: 'smooth', block: 'center' });
		}
		
		function resetItineraryForm() {
			document.getElementById('itineraryFormTitle').textContent = 'Thêm lộ trình mới';
			document.getElementById('itineraryId').value = '';
			document.getElementById('timeLabel').value = '';
			document.getElementById('activity').value = '';
			document.getElementById('sortOrder').value = '0';
			document.getElementById('btnAddItinerary').style.display = 'inline-block';
			document.getElementById('btnUpdateItinerary').style.display = 'none';
		}
		
		// Pre-creation itinerary management
		let tempItineraries = [];
		
		function addItineraryItem() {
			const timeLabel = document.getElementById('newTimeLabel').value.trim();
			const activity = document.getElementById('newActivity').value.trim();
			
			if(!timeLabel || !activity) {
				alert('Vui lòng điền đầy đủ thông tin lộ trình');
				return;
			}
			
			const newItem = {
				timeLabel: timeLabel,
				activity: activity,
				sortOrder: tempItineraries.length + 1
			};
			
			tempItineraries.push(newItem);
			updateItineraryPreview();
			clearItineraryForm();
			
			// Update hidden field
			document.getElementById('itineraryDataInput').value = JSON.stringify(tempItineraries);
		}
		
		function removeItineraryItem(index) {
			if(confirm('Bạn có chắc chắn muốn xóa lộ trình này?')) {
				tempItineraries.splice(index, 1);
				// Update sort order
				tempItineraries.forEach((item, idx) => {
					item.sortOrder = idx + 1;
				});
				updateItineraryPreview();
				document.getElementById('itineraryDataInput').value = JSON.stringify(tempItineraries);
			}
		}
		
		function updateItineraryPreview() {
			const tbody = document.getElementById('itineraryPreviewBody');
			const table = document.getElementById('itineraryPreviewTable');
			const emptyMsg = document.getElementById('emptyItineraryMsg');
			
			if(tempItineraries.length === 0) {
				table.style.display = 'none';
				emptyMsg.style.display = 'block';
				return;
			}
			
			table.style.display = 'block';
			emptyMsg.style.display = 'none';
			
			tbody.innerHTML = '';
			tempItineraries.forEach((item, index) => {
				const row = tbody.insertRow();
				row.innerHTML = `
					<td>${index + 1}</td>
					<td>${escapeHtml(item.timeLabel)}</td>
					<td>${escapeHtml(item.activity)}</td>
					<td>${item.sortOrder}</td>
					<td>
						<button type="button" class="btn btn-danger btn-small" onclick="removeItineraryItem(${index})">Xóa</button>
					</td>
				`;
			});
		}
		
		function clearItineraryForm() {
			document.getElementById('newTimeLabel').value = '';
			document.getElementById('newActivity').value = '';
		}
		
		function escapeHtml(text) {
			const map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			};
			return text.replace(/[&<>"']/g, m => map[m]);
		}
		
		// Handle form reset
		document.addEventListener('DOMContentLoaded', function() {
			const form = document.getElementById('packageForm');
			if(form) {
				form.addEventListener('reset', function() {
					tempItineraries = [];
					updateItineraryPreview();
					document.getElementById('itineraryDataInput').value = '';
				});
			}
		});
		</script>
		<?php } ?>
	<?php include('includes/layout-end.php'); ?>