/**
 * Auth Guard for Admin Panel
 * Chặn truy cập nếu không có JWT token hợp lệ
 */
(function() {
    const token = localStorage.getItem('jwt_token');
    
    // Nếu không có token, redirect về trang login ngay lập tức
    if (!token) {
        // Kiểm tra xem có đang ở trang login không để tránh loop
        if (!window.location.pathname.includes('admin/index.php') && window.location.pathname.includes('admin/')) {
            window.location.href = 'index.php';
        }
        return;
    }

    // Decode token sơ bộ để check role (không check signature ở client, signature được check ở API)
    try {
        const payload = JSON.parse(atob(token.split('.')[1]));
        const userData = payload.data || {};
        
        if (userData.role !== 'admin') {
            alert('Bạn không có quyền truy cập khu vực này.');
            localStorage.removeItem('jwt_token');
            window.location.href = 'index.php';
        }
    } catch (e) {
        localStorage.removeItem('jwt_token');
        window.location.href = 'index.php';
    }

    // Inject Base API URL cho toàn bộ các script admin
    window.BASE_API_URL = window.location.origin + '/tour1/api/';

    // Toàn cục hàm logout
    window.adminLogout = function() {
        localStorage.removeItem('jwt_token');
        window.location.href = 'index.php';
    };
})();
