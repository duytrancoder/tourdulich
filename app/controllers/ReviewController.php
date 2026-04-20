<?php
class ReviewController extends Controller {
    
    /**
     * POST /review/submit
     * AJAX — Lưu đánh giá từ người dùng
     */
    public function submit() {
        header('Content-Type: application/json');
        
        if (empty($_SESSION['login'])) {
            echo json_encode(['status' => 'error', 'message' => 'Vui lòng đăng nhập.']);
            return;
        }
        
        $bookingId = intval($_POST['booking_id'] ?? 0);
        $packageId = intval($_POST['package_id'] ?? 0);
        $rating    = intval($_POST['rating'] ?? 0);
        $comment   = trim($_POST['comment'] ?? '');
        $userEmail = $_SESSION['login'];
        
        if ($bookingId <= 0 || $packageId <= 0 || $rating < 1 || $rating > 5) {
            echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ.']);
            return;
        }
        
        // Verify booking belongs to this user and is completed (status=3)
        $bookingModel = $this->model('BookingModel');
        $booking = $bookingModel->getCompletedBookingForReview($bookingId, $userEmail, $packageId);
        
        if (!$booking) {
            echo json_encode(['status' => 'error', 'message' => 'Đơn hàng không hợp lệ hoặc chưa hoàn thành.']);
            return;
        }
        
        $reviewModel = $this->model('ReviewModel');
        
        if ($reviewModel->hasReview($bookingId)) {
            echo json_encode(['status' => 'error', 'message' => 'Bạn đã đánh giá tour này rồi.']);
            return;
        }
        
        if ($reviewModel->createReview($bookingId, $userEmail, $packageId, $rating, $comment)) {
            echo json_encode(['status' => 'success', 'message' => 'Cảm ơn bạn đã đánh giá!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Không thể lưu đánh giá. Thử lại.']);
        }
    }
}
