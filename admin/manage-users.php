<?php
$pageTitle = "GoTravel Admin | Quản lý người dùng";
$currentPage = 'manage-users';
include('includes/layout-start.php');
?>
		<section class="admin-page-head">
			<div>
				<h1>Quản lý người dùng</h1>
				<p>Danh sách tài khoản khách hàng đăng ký trên hệ thống.</p>
			</div>
		</section>

		<div id="user-alert" style="display:none; margin-bottom: 1rem;"></div>
		
		<!-- Search Form -->
		<section class="card" style="margin-bottom: 1.5rem;">
			<form id="userSearchForm" class="form-stack">
				<div class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
					<div class="form-group">
						<label for="name">Mã/ họ tên người dùng</label>
						<input type="text" id="searchName" placeholder="Nhập mã hoặc họ tên người dùng...">
					</div>
					<div class="form-group">
						<label for="phone">Số điện thoại</label>
						<input type="text" id="searchPhone" placeholder="Nhập số điện thoại...">
					</div>
					<div class="form-group">
						<label for="email">Email</label>
						<input type="text" id="searchEmail" placeholder="Nhập email...">
					</div>
				</div>
				<div style="display: flex; gap: 0.5rem;">
					<button type="submit" class="btn btn-primary">🔍 Tìm kiếm</button>
					<button type="button" id="clearSearch" class="btn btn-ghost">Xóa bộ lọc</button>
				</div>
			</form>
		</section>
		
		<section class="card">
			<div class="table-responsive">
				<table class="table" id="usersTable">
					<thead>
						<tr>
							<th>Mã người dùng</th>
							<th>Họ tên</th>
							<th>Số điện thoại</th>
							<th>Email</th>
							<th>Ngày đăng ký</th>
							<th>Thao tác</th>
						</tr>
					</thead>
					<tbody id="usersList">
						<tr><td colspan="6" style="text-align:center;">Đang tải dữ liệu...</td></tr>
					</tbody>
				</table>
			</div>
		</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const usersList = document.getElementById('usersList');
    const searchForm = document.getElementById('userSearchForm');
    const alertBox = document.getElementById('user-alert');
    const token = localStorage.getItem('jwt_token');

    async function fetchUsers() {
        const name = document.getElementById('searchName').value;
        const phone = document.getElementById('searchPhone').value;
        const email = document.getElementById('searchEmail').value;
        
        let url = (window.BASE_API_URL || '/tour1/api/') + 'admin/users?';
        if (name) url += `name=${encodeURIComponent(name)}&`;
        if (phone) url += `phone=${encodeURIComponent(phone)}&`;
        if (email) url += `email=${encodeURIComponent(email)}&`;

        try {
            const response = await fetch(url, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await response.json();

            if (result.success) {
                renderUsers(result.data);
            } else {
                showError(result.message);
            }
        } catch (error) {
            showError('Lỗi kết nối máy chủ.');
        }
    }

    function renderUsers(users) {
        if (users.length === 0) {
            usersList.innerHTML = '<tr><td colspan="6" style="text-align:center;">Không tìm thấy người dùng nào.</td></tr>';
            return;
        }

        usersList.innerHTML = users.map(user => `
            <tr>
                <td>#${user.id}</td>
                <td>${user.FullName}</td>
                <td>${user.MobileNumber}</td>
                <td>${user.EmailId}</td>
                <td>${user.RegDate}</td>
                <td style="white-space:nowrap;">
                    <a class="btn btn-ghost" href="user-details.php?id=${user.id}">Xem chi tiết</a>
                    <button class="btn btn-danger" onclick="deleteUser(${user.id})">Xóa</button>
                </td>
            </tr>
        `).join('');
    }

    window.deleteUser = async function(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa người dùng này không?')) return;

        try {
            const response = await fetch((window.BASE_API_URL || '/tour1/api/') + `admin/users/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await response.json();

            if (result.success) {
                showSuccess(result.message);
                fetchUsers();
            } else {
                showError(result.message);
            }
        } catch (error) {
            showError('Lỗi khi xóa người dùng.');
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
        fetchUsers();
    });

    document.getElementById('clearSearch').addEventListener('click', () => {
        searchForm.reset();
        fetchUsers();
    });

    // Initial load
    fetchUsers();
});
</script>

<?php include('includes/layout-end.php'); ?>