<?php
$pageMap = [
	'terms' => 'Điều khoản & Điều kiện',
	'privacy' => 'Chính sách bảo mật',
	'aboutus' => 'Giới thiệu',
	'contact' => 'Liên hệ'
];
$selectedType = isset($_GET['type']) ? $_GET['type'] : '';
$pageTitle = "GoTravel Admin | Quản lý trang";
$currentPage = 'manage-pages';

include('includes/layout-start.php');
?>
	<section class="admin-page-head">
		<div>
			<h1>Quản lý trang nội dung</h1>
			<p>Chỉnh sửa nhanh các trang tĩnh hiển thị trên website.</p>
		</div>
	</section>

    <div id="page-alert" style="display:none; margin-bottom: 1rem;"></div>

	<section class="card">
		<form id="pageForm" class="form-stack">
			<div class="form-group">
				<label for="page-selector">Chọn trang</label>
				<select id="page-selector" class="form-control" onchange="if(this.value){ window.location.href = 'manage-pages.php?type=' + this.value; }">
					<option value="">-- Chọn trang cần chỉnh sửa --</option>
					<?php foreach($pageMap as $type => $label): ?>
					<option value="<?php echo $type; ?>" <?php echo $selectedType===$type ? 'selected' : '';?>><?php echo $label;?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<?php if($selectedType && isset($pageMap[$selectedType])): ?>

			<div class="form-group">
				<label for="pgedetails">Nội dung trang</label>
				<textarea id="pgedetails" rows="12" required>Đang tải nội dung...</textarea>
			</div>
			<button type="submit" id="saveBtn" class="btn btn-primary">Cập nhật</button>
			<?php else: ?>
			<div class="empty-state">Vui lòng chọn một trang ở danh sách trên để bắt đầu chỉnh sửa.</div>
			<?php endif; ?>
		</form>
	</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectedType = '<?php echo $selectedType; ?>';
    const form = document.getElementById('pageForm');
    const alertBox = document.getElementById('page-alert');
    const textArea = document.getElementById('pgedetails');
    const btn = document.getElementById('saveBtn');
    const token = localStorage.getItem('jwt_token');

    if (!selectedType || !textArea) return;

    // Load page content
    async function fetchPageContent() {
        try {
            const response = await fetch((window.BASE_API_URL || '/tour1/api/') + 'admin/pages/' + selectedType, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await response.json();
            if (result.success) {
                textArea.value = result.data.detail;
            } else {
                showError(result.message);
            }
        } catch (error) {
            showError('Lỗi kết nối máy chủ.');
        }
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        btn.disabled = true;
        btn.textContent = 'Đang lưu...';
        alertBox.style.display = 'none';

        try {
            const response = await fetch((window.BASE_API_URL || '/tour1/api/') + 'admin/pages/' + selectedType, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token 
                },
                body: JSON.stringify({ detail: textArea.value })
            });
            const result = await response.json();

            if (result.success) {
                showSuccess(result.message);
            } else {
                showError(result.message);
            }
        } catch (error) {
            showError('Lỗi khi lưu dữ liệu.');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Cập nhật';
        }
    });

    function showError(msg) {
        alertBox.className = 'alert error';
        alertBox.textContent = msg;
        alertBox.style.display = 'block';
    }

    function showSuccess(msg) {
        alertBox.className = 'alert success';
        alertBox.textContent = msg;
        alertBox.style.display = 'block';
        setTimeout(() => { alertBox.style.display = 'none'; }, 3000);
    }

    fetchPageContent();
});
</script>

<?php include('includes/layout-end.php');?>
