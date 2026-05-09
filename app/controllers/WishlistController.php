<?php
class WishlistController extends Controller {
    /**
     * Trang danh sách yêu thích — chỉ trả về HTML Shell
     * Mọi dữ liệu được load qua REST API: GET /api/user/wishlist (assets/js/api/account.js)
     */
    public function index() {
        // Không kiểm tra Session, không gọi Model
        // JS (account.js) sẽ kiểm tra JWT và redirect nếu chưa đăng nhập
        $data = [
            'wishlistItems' => [],
            'error'         => null,
            'msg'           => null
        ];
        $this->view('wishlist/index', $data);
    }
}
