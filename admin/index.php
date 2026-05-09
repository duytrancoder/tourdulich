<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GoTravel Admin | Đăng nhập</title>
    <!-- CSS Injection from PHP BASE_URL if possible, or relative -->
	<link rel="stylesheet" href="../admin/css/style.css">
	<script>
		// Clear any leftover tokens when visiting login page
		localStorage.removeItem('jwt_token');
		document.cookie = "admin_jwt_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
        
        // Define Base API URL with fallback
        const pathParts = window.location.pathname.split('/');
        const baseFolder = pathParts[1] === 'tour1' ? '/tour1' : '';
        window.BASE_API_URL = window.location.origin + baseFolder + '/api/';
        
        console.log("Admin API URL initialized:", window.BASE_API_URL);

	</script>
</head>
<body class="auth-page" style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('../admin/packageimages/tour_halong.webp') no-repeat center center; background-size: cover;">
	<div class="auth-card">
		<h1 style="color:#fff">Đăng nhập quản trị</h1>
		<p class="helper-text" style="color:#e5e7eb">Sử dụng thông tin tài khoản được cấp để truy cập hệ thống quản trị.</p>
		
        <div id="login-alert" style="display:none; margin-bottom: 1rem;"></div>

		<form id="adminLoginForm">
			<div class="form-group">
				<label for="username" style="color:#fff">Tên đăng nhập</label>
				<input type="text" id="username" required>
			</div>
			<div class="form-group">
				<label for="password" style="color:#fff">Mật khẩu</label>
				<input type="password" id="password" required>
			</div>
			<button type="submit" id="loginBtn" class="btn btn-primary">Đăng nhập</button>
		</form>
		<p class="helper-text"><a href="/tour1/" style="color:#e5e7eb">← Quay lại trang khách</a></p>
	</div>

    <script>
    document.getElementById('adminLoginForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        const alertBox = document.getElementById('login-alert');
        const btn = document.getElementById('loginBtn');
        
        btn.disabled = true;
        btn.textContent = 'Đang đăng nhập...';
        alertBox.style.display = 'none';

        try {
            const response = await fetch(window.BASE_API_URL + 'auth/admin-login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });
            const result = await response.json();

            if (result.success) {
                // Store JWT
                localStorage.setItem('jwt_token', result.data.token);
                // Redirect to dashboard
                window.location.href = 'dashboard.php';
            } else {
                alertBox.className = 'alert error';
                alertBox.textContent = result.message;
                alertBox.style.display = 'block';
                btn.disabled = false;
                btn.textContent = 'Đăng nhập';
            }
        } catch (error) {
            alertBox.className = 'alert error';
            alertBox.textContent = 'Lỗi kết nối máy chủ.';
            alertBox.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Đăng nhập';
        }
    });
    </script>
</body>
</html>