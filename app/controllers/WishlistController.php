<?php
class WishlistController extends Controller {
    /**
     * Display user's wishlist page
     */
    public function index() {
        if (strlen($_SESSION['login']) == 0) {
            header('location:' . BASE_URL);
            exit;
        }

        $wishlistModel = $this->model('WishlistModel');
        $userEmail = $_SESSION['login'];
        $wishlistItems = $wishlistModel->getWishlistByUser($userEmail);

        $data = [
            'wishlistItems' => $wishlistItems,
            'error' => $_SESSION['error'] ?? null,
            'msg' => $_SESSION['msg'] ?? null
        ];
        unset($_SESSION['error'], $_SESSION['msg']);

        $this->view('wishlist/index', $data);
    }

    /**
     * Add tour to wishlist (AJAX endpoint)
     */
    public function add($packageId = 0) {
        header('Content-Type: application/json');
        
        if (strlen($_SESSION['login']) == 0) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập', 'requireLogin' => true]);
            exit;
        }

        $wishlistModel = $this->model('WishlistModel');
        $userEmail = $_SESSION['login'];
        
        if ($wishlistModel->addToWishlist($userEmail, $packageId)) {
            echo json_encode(['success' => true, 'message' => 'Đã thêm vào danh sách yêu thích']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
        exit;
    }

    /**
     * Remove tour from wishlist (AJAX endpoint)
     */
    public function remove($packageId = 0) {
        header('Content-Type: application/json');
        
        if (strlen($_SESSION['login']) == 0) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập', 'requireLogin' => true]);
            exit;
        }

        $wishlistModel = $this->model('WishlistModel');
        $userEmail = $_SESSION['login'];
        
        if ($wishlistModel->removeFromWishlist($userEmail, $packageId)) {
            echo json_encode(['success' => true, 'message' => 'Đã xóa khỏi danh sách yêu thích']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
        exit;
    }

    /**
     * Toggle wishlist status (AJAX endpoint)
     */
    public function toggle($packageId = 0) {
        header('Content-Type: application/json');
        
        if (strlen($_SESSION['login']) == 0) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập', 'requireLogin' => true]);
            exit;
        }

        $wishlistModel = $this->model('WishlistModel');
        $userEmail = $_SESSION['login'];
        
        $isInWishlist = $wishlistModel->isInWishlist($userEmail, $packageId);
        
        if ($wishlistModel->toggleWishlist($userEmail, $packageId)) {
            $newStatus = !$isInWishlist;
            echo json_encode([
                'success' => true, 
                'inWishlist' => $newStatus,
                'message' => $newStatus ? 'Đã thêm vào danh sách yêu thích' : 'Đã xóa khỏi danh sách yêu thích'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra']);
        }
        exit;
    }

    /**
     * Check if package is in wishlist (AJAX endpoint)
     */
    public function check($packageId = 0) {
        header('Content-Type: application/json');
        
        if (strlen($_SESSION['login']) == 0) {
            echo json_encode(['inWishlist' => false]);
            exit;
        }

        $wishlistModel = $this->model('WishlistModel');
        $userEmail = $_SESSION['login'];
        
        $inWishlist = $wishlistModel->isInWishlist($userEmail, $packageId);
        echo json_encode(['inWishlist' => $inWishlist]);
        exit;
    }

    /**
     * Get all wishlist package IDs for current user (AJAX endpoint)
     */
    public function getIds() {
        header('Content-Type: application/json');
        
        if (strlen($_SESSION['login']) == 0) {
            echo json_encode(['packageIds' => []]);
            exit;
        }

        $wishlistModel = $this->model('WishlistModel');
        $userEmail = $_SESSION['login'];
        
        $packageIds = $wishlistModel->getWishlistPackageIds($userEmail);
        echo json_encode(['packageIds' => $packageIds]);
        exit;
    }
}
