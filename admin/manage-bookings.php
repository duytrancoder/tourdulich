<?php
$pageTitle = "GoTravel Admin | Quản lý đặt tour";
$currentPage = 'manage-bookings';
include('includes/layout-start.php');
?>
	<section class="admin-page-head">
		<div>
			<h1>Quản lý đặt tour</h1>
			<p>Cập nhật trạng thái và theo dõi các yêu cầu đặt tour.</p>
		</div>
	</section>

	<div id="booking-alert" style="display:none; margin-bottom: 1rem;"></div>

	<section class="card" style="margin-bottom: 1.5rem;">
		<form id="bookingSearchForm" class="form-stack">
			<div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
				<div class="form-group">
					<label for="filterStatus">Trạng thái</label>
					<select id="filterStatus">
						<option value="">-- Tất cả trạng thái --</option>
						<option value="0">Chờ xử lý</option>
						<option value="1">Đã xác nhận</option>
						<option value="2">Đã hủy</option>
						<option value="3">Đã hoàn thành</option>
					</select>
				</div>
                <div class="form-group" style="display:flex; align-items:flex-end;">
                    <button type="submit" class="btn btn-primary">🔍 Lọc danh sách</button>
                </div>
			</div>
		</form>
	</section>

	<section class="card">
		<div class="table-responsive">
			<table class="table" id="bookingsTable">
				<thead>
					<tr>
						<th>Mã (#ID)</th>
						<th>Khách hàng</th>
						<th>Thông tin Tour</th>
						<th>Ngày khởi hành</th>
						<th>Tổng tiền</th>
						<th>Trạng thái</th>
						<th>Thao tác</th>
					</tr>
				</thead>
				<tbody id="bookingsList">
					<tr><td colspan="7" style="text-align:center;">Đang tải dữ liệu...</td></tr>
				</tbody>
			</table>
		</div>
	</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingsList = document.getElementById('bookingsList');
    const searchForm = document.getElementById('bookingSearchForm');
    const alertBox = document.getElementById('booking-alert');
    const token = localStorage.getItem('jwt_token');

    async function fetchBookings() {
        const status = document.getElementById('filterStatus').value;
        let url = (window.BASE_API_URL || '/tour1/api/') + 'admin/bookings';
        if (status !== '') url += '?status=' + status;

        try {
            const response = await fetch(url, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await response.json();

            if (result.success) {
                renderBookings(result.data);
            } else {
                showError(result.message);
            }
        } catch (error) {
            showError('Lỗi kết nối máy chủ.');
        }
    }

    function renderBookings(bookings) {
        if (bookings.length === 0) {
            bookingsList.innerHTML = '<tr><td colspan="7" style="text-align:center;">Không tìm thấy đặt tour nào.</td></tr>';
            return;
        }

        bookingsList.innerHTML = bookings.map(b => {
            let statusOptions = `
                <option value="0" ${b.status == 0 ? 'selected' : ''}>Chờ xử lý</option>
                <option value="1" ${b.status == 1 ? 'selected' : ''}>Xác nhận</option>
                <option value="2" ${b.status == 2 ? 'selected' : ''}>Hủy bỏ</option>
                <option value="3" ${b.status == 3 ? 'selected' : ''}>Hoàn thành</option>
            `;

            return `
                <tr>
                    <td><strong>#BK${b.BookingId}</strong></td>
                    <td>${b.UserName}<br><small>${b.UserEmail}</small></td>
                    <td>${b.PackageName}</td>
                    <td>${b.FromDateFormatted}</td>
                    <td>${new Intl.NumberFormat('vi-VN').format(b.TotalPrice)} VND</td>
                    <td>
                        <select class="form-control" style="width:auto;" onchange="updateBookingStatus(${b.BookingId}, this.value)">
                            ${statusOptions}
                        </select>
                    </td>
                    <td>
                        <a class="btn btn-ghost" href="view-booking.php?bid=${b.BookingId}">Chi tiết</a>
                    </td>
                </tr>
            `;
        }).join('');
    }

    window.updateBookingStatus = async function(id, newStatus) {
        if (!confirm('Bạn có muốn thay đổi trạng thái đơn hàng này?')) {
            fetchBookings(); // Reset select
            return;
        }

        try {
            const response = await fetch((window.BASE_API_URL || '/tour1/api/') + `admin/bookings/${id}`, {
                method: 'PUT',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token 
                },
                body: JSON.stringify({ status: newStatus })
            });
            const result = await response.json();

            if (result.success) {
                showSuccess(result.message);
                fetchBookings();
            } else {
                showError(result.message);
            }
        } catch (error) {
            showError('Lỗi khi cập nhật trạng thái.');
        }
    };

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

    searchForm.addEventListener('submit', (e) => {
        e.preventDefault();
        fetchBookings();
    });

    fetchBookings();
});
</script>

<?php include('includes/layout-end.php');?>
