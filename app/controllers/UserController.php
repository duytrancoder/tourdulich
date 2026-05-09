<?php
/**
 * UserController — Phase 5 Amputated Version
 * Business logic đã chuyển sang api/controllers/UserAccountController.php
 */
class UserController extends Controller {

    /**
     * Đăng xuất — vẫn cần để xóa $_SESSION server-side (Cookie Bridge compatibility)
     */
    public function logout() {
        $_SESSION['login'] = '';
        session_unset();
        session_destroy();
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

    /**
     * [REDIRECT] Trang hồ sơ cũ → user/account
     * Giữ lại để tránh 404 cho bookmark cũ
     */
    public function profile() {
        header('location:' . BASE_URL . 'user/account');
        exit;
    }

    /**
     * [REDIRECT] Đổi mật khẩu cũ → user/account (tab security)
     * Chức năng đã được tích hợp vào tab "Đổi mật khẩu" ở trang account
     */
    public function changePassword() {
        header('location:' . BASE_URL . 'user/account#security');
        exit;
    }

    /**
     * [REDIRECT] POST updateProfile cũ → user/account
     * Method này đã bị xóa, giờ dùng PUT /api/user/profile
     */
    public function updateProfile() {
        header('location:' . BASE_URL . 'user/account');
        exit;
    }

    /**
     * [REDIRECT] POST updatePassword cũ → user/account
     * Method này đã bị xóa, giờ dùng PUT /api/user/password
     */
    public function updatePassword() {
        header('location:' . BASE_URL . 'user/account#security');
        exit;
    }

    /**
     * [REDIRECT] Quên mật khẩu → trang chủ (dùng modal đăng nhập)
     * Tính năng sẽ được tích hợp vào modal trong Phase sau
     */
    public function forgotPassword() {
        header('location:' . BASE_URL);
        exit;
    }

    /**
     * [REDIRECT] POST resetPassword cũ → trang chủ
     */
    public function resetPassword() {
        header('location:' . BASE_URL);
        exit;
    }
}
