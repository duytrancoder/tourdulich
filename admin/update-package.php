<?php
$pid = intval($_GET['pid'] ?? 0);
$pageTitle = "GoTravel Admin | Cập nhật gói tour";
$currentPage = 'manage-packages';
include('includes/layout-start.php');
?>
	<section class="admin-page-head">
		<div>
			<a href="manage-packages.php" class="btn btn-ghost" style="margin-bottom:1rem;">← Quay lại danh sách</a>
			<h1>Cập nhật gói tour #<?php echo $pid; ?></h1>
			<p>Điều chỉnh thông tin gói tour và quản lý lịch trình chi tiết.</p>
		</div>
	</section>

	<div id="package-alert" style="display:none; margin-bottom: 1.5rem;"></div>

	<form id="updatePackageForm" class="form-stack">
        <input type="hidden" name="_method" value="PUT">
		<div class="form-grid-2-1" style="display:grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; align-items: flex-start;">
			
			<div class="left-col">
				<section class="card">
					<h3>Thông tin cơ bản</h3>
					<div class="form-grid" style="grid-template-columns: 1fr 1fr; margin-top: 1rem;">
						<div class="form-group">
							<label for="packagename">Tên gói tour</label>
							<input type="text" name="packagename" id="packagename" required>
						</div>
						<div class="form-group">
							<label for="packagetype">Loại gói</label>
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
						<div class="form-group">
							<label for="tourduration">Thời gian tour</label>
							<input type="text" name="tourduration" id="tourduration" placeholder="VD: 3 Ngày 2 Đêm" required>
						</div>
						<div class="form-group">
							<label for="packageprice">Giá gói (VNĐ)</label>
							<input type="number" name="packageprice" id="packageprice" required>
						</div>
						<div class="form-group">
							<label for="packagefeatures">Điểm nổi bật (tóm tắt)</label>
							<input type="text" name="packagefeatures" id="packagefeatures" required>
						</div>
					</div>
					<div class="form-group">
						<label for="packagedetails">Chi tiết gói tour</label>
						<textarea name="packagedetails" id="packagedetails" rows="8" required></textarea>
					</div>
				</section>

				<section class="card" style="margin-top: 1.5rem;">
					<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
						<h3>Lịch trình chi tiết (Itinerary)</h3>
						<button type="button" id="btnAddDay" class="btn btn-secondary btn-small">+ Thêm ngày/mốc thời gian</button>
					</div>
					<div id="itineraryContainer">
						<!-- Dynamic itinerary blocks will be injected here -->
					</div>
				</section>
			</div>

			<div class="right-col">
				<section class="card">
					<h3>Hình ảnh tour</h3>
					<div id="currentImageContainer" style="margin: 1rem 0; text-align:center;">
						<img id="packagePreview" src="" alt="Preview" style="max-width:100%; border-radius:8px; display:none;">
						<div id="noImage" class="empty-state">Chưa có ảnh</div>
					</div>
					<div class="form-group">
						<label for="packageimage">Thay đổi ảnh mới</label>
						<input type="file" name="packageimage" id="packageimage" accept="image/*">
					</div>
				</section>

				<section class="card" style="margin-top: 1.5rem; position: sticky; top: 1rem;">
					<h3>Thao tác</h3>
					<p style="font-size:0.9rem; color:#666; margin-bottom:1.5rem;">Đảm bảo các thông tin đã chính xác trước khi cập nhật.</p>
					<button type="submit" id="saveBtn" class="btn btn-primary w-100" style="padding:1rem;">Cập nhật gói tour</button>
				</section>
			</div>
		</div>
	</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pid = <?php echo $pid; ?>;
    const form = document.getElementById('updatePackageForm');
    const itineraryContainer = document.getElementById('itineraryContainer');
    const alertBox = document.getElementById('package-alert');
    const saveBtn = document.getElementById('saveBtn');
    const token = localStorage.getItem('jwt_token');

    if (!pid) {
        showError('Mã gói tour không hợp lệ.');
        return;
    }

    // Load data from API
    async function fetchPackageData() {
        try {
            const response = await fetch((window.BASE_API_URL || '/tour1/api/') + 'admin/packages/' + pid, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await response.json();

            if (result.success) {
                fillForm(result.data);
            } else {
                showError(result.message);
            }
        } catch (error) {
            showError('Lỗi kết nối máy chủ.');
        }
    }

    function fillForm(data) {
        document.getElementById('packagename').value = data.PackageName;
        document.getElementById('packagetype').value = data.PackageType;
        document.getElementById('packagelocation').value = data.PackageLocation;
        document.getElementById('tourduration').value = data.TourDuration;
        document.getElementById('packageprice').value = data.PackagePrice;
        document.getElementById('packagefeatures').value = data.PackageFetures;
        document.getElementById('packagedetails').value = data.PackageDetails;

        if (data.PackageImage) {
            const img = document.getElementById('packagePreview');
            img.src = '/tour1/public/packageimages/' + data.PackageImage;
            img.style.display = 'block';
            document.getElementById('noImage').style.display = 'none';
        }

        // Render itineraries
        itineraryContainer.innerHTML = '';
        if (data.itineraries && data.itineraries.length > 0) {
            data.itineraries.forEach(item => addItineraryRow(item.TimeLabel, item.Activity));
        } else {
            addItineraryRow(); // Add one empty row if none
        }
    }

    function addItineraryRow(timeLabel = '', activity = '') {
        const div = document.createElement('div');
        div.className = 'itinerary-block card';
        div.style.marginBottom = '1rem';
        div.style.background = '#f9fafb';
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.5rem;">
                <h4 class="day-label">Mốc thời gian</h4>
                <button type="button" class="btn btn-danger btn-small remove-day">Xóa</button>
            </div>
            <div class="form-group">
                <input type="text" class="it-time" value="${timeLabel}" placeholder="VD: Ngày 1 - Sáng" required>
            </div>
            <div class="form-group">
                <textarea class="it-activity" rows="3" placeholder="Hoạt động..." required>${activity}</textarea>
            </div>
        `;
        
        div.querySelector('.remove-day').addEventListener('click', () => {
            div.remove();
            updateDayLabels();
        });
        
        itineraryContainer.appendChild(div);
        updateDayLabels();
    }

    function updateDayLabels() {
        const labels = itineraryContainer.querySelectorAll('.day-label');
        labels.forEach((label, index) => {
            label.textContent = `Lịch trình #${index + 1}`;
        });
    }

    document.getElementById('btnAddDay').addEventListener('click', () => addItineraryRow());

    // Submit Form
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        saveBtn.disabled = true;
        saveBtn.textContent = 'Đang lưu...';
        alertBox.style.display = 'none';

        const formData = new FormData(form);
        
        // Collect itineraries
        const itineraries = [];
        const blocks = itineraryContainer.querySelectorAll('.itinerary-block');
        blocks.forEach(block => {
            itineraries.push({
                timeLabel: block.querySelector('.it-time').value,
                activity: block.querySelector('.it-activity').value
            });
        });
        formData.append('itineraryData', JSON.stringify(itineraries));

        try {
            const response = await fetch((window.BASE_API_URL || '/tour1/api/') + 'admin/packages/' + pid, {
                method: 'POST', // Use POST + _method: PUT for multipart support in PHP
                headers: { 'Authorization': 'Bearer ' + token },
                body: formData
            });
            const result = await response.json();

            if (result.success) {
                showSuccess(result.message);
                setTimeout(() => { window.location.reload(); }, 1500);
            } else {
                showError(result.message);
                saveBtn.disabled = false;
                saveBtn.textContent = 'Cập nhật gói tour';
            }
        } catch (error) {
            showError('Lỗi kết nối máy chủ.');
            saveBtn.disabled = false;
            saveBtn.textContent = 'Cập nhật gói tour';
        }
    });

    function showError(msg) {
        alertBox.className = 'alert error';
        alertBox.textContent = msg;
        alertBox.style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function showSuccess(msg) {
        alertBox.className = 'alert success';
        alertBox.textContent = msg;
        alertBox.style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    fetchPackageData();
});
</script>
<style>
.itinerary-block { border: 1px solid #e5e7eb; transition: all 0.2s; }
.itinerary-block:hover { border-color: var(--primary); }
.w-100 { width: 100%; }
</style>
<?php include('includes/layout-end.php'); ?>