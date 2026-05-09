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


}
