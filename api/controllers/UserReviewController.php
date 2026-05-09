<?php
namespace Api\Controllers;

use Api\Core\Response;
use Api\Core\JWTHandler;
use Api\Core\Messages;
use Api\Core\Database;
use PDO;

class UserReviewController {

    private $userEmail;
    private $db;

    public function __construct() {
        $user = JWTHandler::verifyBearerToken();
        $this->userEmail = $user->email;
        $this->db = Database::getConnection();
    }

    /**
     * POST /api/user/review
     * Gửi đánh giá tour (chỉ cho phép booking đã hoàn thành - status=3)
     */
    public function submit() {
        $data = json_decode(file_get_contents("php://input"), true);

        $bookingId = intval($data['booking_id'] ?? 0);
        $packageId = intval($data['package_id'] ?? 0);
        $rating    = intval($data['rating']     ?? 0);
        $comment   = trim($data['comment']      ?? '');

        // Validate input
        if ($bookingId <= 0 || $packageId <= 0) {
            Response::error(Messages::ERROR_INVALID_DATA, null, 400);
        }

        if ($rating < 1 || $rating > 5) {
            Response::error(Messages::ERROR_INVALID_DATA, null, 400);
        }

        // Kiểm tra booking có thuộc user này và đã hoàn thành (status=3) không
        $stmt = $this->db->prepare(
            "SELECT BookingId FROM tblbooking
             WHERE BookingId = ? AND UserEmail = ? AND PackageId = ? AND status = 3
             LIMIT 1"
        );
        $stmt->execute([$bookingId, $this->userEmail, $packageId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            Response::error(Messages::REVIEW_NOT_ALLOWED, null, 403);
        }

        // Kiểm tra đã review chưa
        $stmt = $this->db->prepare("SELECT id FROM tblreviews WHERE BookingId = ? LIMIT 1");
        $stmt->execute([$bookingId]);
        if ($stmt->fetch()) {
            Response::error(Messages::REVIEW_ALREADY_EXISTS, null, 409);
        }

        // Lưu review
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO tblreviews (BookingId, UserEmail, PackageId, Rating, Comment)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$bookingId, $this->userEmail, $packageId, $rating, $comment]);

            // Trả về danh sách review mới nhất của package để JS có thể reload
            $stmt = $this->db->prepare(
                "SELECT r.id, r.Rating, r.Comment, r.CreatedAt, u.FullName
                 FROM tblreviews r
                 JOIN tblusers u ON u.EmailId = r.UserEmail
                 WHERE r.PackageId = ?
                 ORDER BY r.CreatedAt DESC"
            );
            $stmt->execute([$packageId]);
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            Response::success([
                'reviews' => $reviews
            ], Messages::REVIEW_SUBMIT_SUCCESS, 201);

        } catch (\Exception $e) {
            Response::error(Messages::ERROR_SYSTEM, null, 500);
        }
    }

    /**
     * GET /api/reviews/{packageId}
     * Lấy tất cả review của một package (Public - không cần JWT)
     */
    public function getByPackage($packageId) {
        $packageId = intval($packageId);
        if ($packageId <= 0) {
            Response::error(Messages::ERROR_INVALID_DATA, null, 400);
        }

        $stmt = $this->db->prepare(
            "SELECT r.id, r.Rating, r.Comment, r.CreatedAt, u.FullName
             FROM tblreviews r
             JOIN tblusers u ON u.EmailId = r.UserEmail
             WHERE r.PackageId = ?
             ORDER BY r.CreatedAt DESC"
        );
        $stmt->execute([$packageId]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tính điểm trung bình
        $stmtAvg = $this->db->prepare("SELECT AVG(Rating) as avg_rating, COUNT(*) as total FROM tblreviews WHERE PackageId = ?");
        $stmtAvg->execute([$packageId]);
        $stats = $stmtAvg->fetch(PDO::FETCH_ASSOC);

        Response::success([
            'reviews'    => $reviews,
            'avg_rating' => round((float)$stats['avg_rating'], 1),
            'total'      => (int)$stats['total']
        ], Messages::SUCCESS);
    }
}
