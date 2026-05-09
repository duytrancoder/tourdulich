<?php
$pageTitle = "GoTravel Admin | Chi tiết đặt tour";
$currentPage = 'manage-bookings';
$bookingId = intval($_GET['bid'] ?? 0);

include('includes/layout-start.php');
?>
	<section class="admin-page-head">
		<div>
			<a href="manage-bookings.php" class="btn btn-ghost" style="margin-bottom:1rem;">← Quay lại danh sách</a>
			<h1>Chi tiết đặt tour #BK<?php echo $bookingId; ?></h1>
		</div>
	</section>

	<div id="view-booking-alert" style="display:none; margin-bottom: 1rem;"></div>

	<div id="booking-details-container">
        <div class="card" style="text-align:center; padding: 3rem;">Đang tải thông tin chi tiết...</div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingId = <?php echo $bookingId; ?>;
    const container = document.getElementById('booking-details-container');
    const alertBox = document.getElementById('view-booking-alert');
    const token = localStorage.getItem('jwt_token');

    if (!bookingId) {
        showError('Mã đặt tour không hợp lệ.');
        return;
    }

    async function fetchBookingDetails() {
        try {
            const response = await fetch((window.BASE_API_URL || '/tour1/api/') + 'admin/bookings/' + bookingId, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await response.json();

            if (result.success) {
                renderDetails(result.data);
            } else {
                showError(result.message);
            }
        } catch (error) {
            showError('Lỗi kết nối máy chủ.');
        }
    }

    function renderDetails(b) {
        let statusText = 'Chờ xử lý';
        let statusClass = 'is-pending';
        if(b.status == 1) { statusText = 'Đã xác nhận'; statusClass = 'is-approved'; }
        if(b.status == 2) { statusText = 'Đã hủy'; statusClass = 'is-cancelled'; }
        if(b.status == 3) { statusText = 'Đã hoàn thành'; statusClass = 'is-completed'; }

        container.innerHTML = `
            <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: flex-start;">
                <div class="card">
                    <h3 style="margin-bottom:1rem; border-bottom:1px solid #eee; padding-bottom:.5rem;">Thông tin khách hàng</h3>
                    <table class="table-details">
                        <tr><th>Họ tên:</th><td>${b.UserName}</td></tr>
                        <tr><th>Email:</th><td>${b.UserEmail}</td></tr>
                        <tr><th>Số điện thoại:</th><td>${b.MobileNumber}</td></tr>
                    </table>
                    
                    <h3 style="margin-bottom:1rem; margin-top:2rem; border-bottom:1px solid #eee; padding-bottom:.5rem;">Thông tin Tour</h3>
                    <table class="table-details">
                        <tr><th>Tên Tour:</th><td>${b.PackageName}</td></tr>
                        <tr><th>Địa điểm:</th><td>${b.PackageLocation}</td></tr>
                        <tr><th>Số lượng khách:</th><td>${b.NumberOfPeople} người</td></tr>
                        <tr><th>Tổng tiền:</th><td><strong>${new Intl.NumberFormat('vi-VN').format(b.TotalPrice)} VND</strong></td></tr>
                    </table>
                </div>

                <div class="card">
                    <h3 style="margin-bottom:1rem; border-bottom:1px solid #eee; padding-bottom:.5rem;">Tình trạng đặt tour</h3>
                    <table class="table-details">
                        <tr><th>Ngày đặt:</th><td>${b.RegDateFormatted}</td></tr>
                        <tr><th>Trạng thái:</th><td><span class="status-chip ${statusClass}">${statusText}</span></td></tr>
                        <tr><th>Ghi chú của khách:</th><td>${b.Comment || '<em>Không có ghi chú</em>'}</td></tr>
                    </table>

                    <div style="margin-top:2rem; padding-top:1rem; border-top:1px solid #eee;">
                        <form id="updateBookingForm" class="form-stack">
                            <div class="form-group">
                                <label for="updateStatus">Cập nhật trạng thái</label>
                                <select id="updateStatus">
                                    <option value="0" ${b.status == 0 ? 'selected' : ''}>Chờ xử lý</option>
                                    <option value="1" ${b.status == 1 ? 'selected' : ''}>Xác nhận đơn hàng</option>
                                    <option value="2" ${b.status == 2 ? 'selected' : ''}>Hủy đơn hàng</option>
                                    <option value="3" ${b.status == 3 ? 'selected' : ''}>Hoàn thành tour</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="adminRemark">Ghi chú của Admin</label>
                                <textarea id="adminRemark" rows="4">${b.AdminRemark || ''}</textarea>
                            </div>
                            <button type="submit" id="saveBtn" class="btn btn-primary w-100">Lưu thay đổi</button>
                        </form>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('updateBookingForm').addEventListener('submit', handleUpdate);
    }

    async function handleUpdate(e) {
        e.preventDefault();
        const saveBtn = document.getElementById('saveBtn');
        const status = document.getElementById('updateStatus').value;
        const adminRemark = document.getElementById('adminRemark').value;

        saveBtn.disabled = true;
        saveBtn.textContent = 'Đang lưu...';

        try {
            const response = await fetch((window.BASE_API_URL || '/tour1/api/') + 'admin/bookings/' + bookingId, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token 
                },
                body: JSON.stringify({ status, adminRemark })
            });
            const result = await response.json();

            if (result.success) {
                showSuccess(result.message);
                fetchBookingDetails();
            } else {
                showError(result.message);
            }
        } catch (error) {
            showError('Lỗi khi cập nhật dữ liệu.');
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Lưu thay đổi';
        }
    }

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

    fetchBookingDetails();
});
</script>
<style>
.table-details { width: 100%; border-collapse: collapse; }
.table-details th { text-align: left; padding: 0.8rem 0; color: #666; width: 40%; font-weight: normal; }
.table-details td { padding: 0.8rem 0; font-weight: 600; color: #333; }
</style>
<?php include('includes/layout-end.php');?>
