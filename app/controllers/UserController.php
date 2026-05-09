<?php
class UserController extends Controller {

    /**
     * Đăng xuất — vẫn cần để xóa $_SESSION server-side (Cookie Bridge compatibility)
     */
    public function logout() {
        // Xóa Session cũ
        $_SESSION['login'] = '';
        session_unset();
        session_destroy();

        // Redirect về trang chủ, JS ở client sẽ tự xóa localStorage và Cookie JWT
        header('location:' . BASE_URL);
        exit;
    }

    /**
     * Trang tài khoản — chỉ trả về HTML Shell
     * Mọi dữ liệu được load qua REST API bằng assets/js/api/account.js
     */
    public function account() {
        $data = [
            'user'          => null,
            'bookings'      => [],
            'wishlistItems' => [],
            'error'         => null,
            'msg'           => null
        ];
        $this->view('account/index', $data);
    }
}
