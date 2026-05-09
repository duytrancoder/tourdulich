<?php
namespace Api\Controllers;

use Api\Core\Response;
use Api\Core\JWTHandler;
use Api\Core\Database;
use PDO;

class UserBookingController {
    private $userEmail;
    private $db;

    public function __construct() {
        // Authenticate request
        $user = JWTHandler::verifyBearerToken();
        $this->userEmail = $user->email;
        $this->db = Database::getConnection();
    }

    /**
     * POST /api/user/booking
     */
    public function book() {
        $data = json_decode(file_get_contents("php://input"));
        
        $pid = isset($data->packageId) ? intval($data->packageId) : 0;
        $departureDate = trim($data->departureDate ?? '');
        $numberofpeople = isset($data->numberOfPeople) ? intval($data->numberOfPeople) : 1;
        $comment = trim($data->comment ?? '');

        if ($pid <= 0) {
            Response::error("Gói tour không hợp lệ", null, 400);
        }

        if (empty($departureDate)) {
            Response::error("Vui lòng chọn ngày khởi hành", null, 400);
        }

        if ($numberofpeople < 1 || $numberofpeople > 100) {
            Response::error("Số người phải từ 1 đến 100", null, 400);
        }

        $departureTimestamp = strtotime($departureDate);
        $todayTimestamp = strtotime('today');

        if ($departureTimestamp === false) {
            Response::error("Ngày không hợp lệ", null, 400);
        }

        if ($departureTimestamp < $todayTimestamp) {
            Response::error("Ngày khởi hành không thể là ngày trong quá khứ", null, 400);
        }

        $stmt = $this->db->prepare("SELECT PackagePrice FROM tbltourpackages WHERE PackageId = ?");
        $stmt->execute([$pid]);
        $package = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$package) {
            Response::error("Gói tour không tồn tại", null, 404);
        }

        $totalprice = $package['PackagePrice'] * $numberofpeople;

        try {
            $stmt = $this->db->prepare("INSERT INTO tblbooking (PackageId, UserEmail, FromDate, ToDate, Comment, NumberOfPeople, TotalPrice, status) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
            $stmt->execute([
                $pid,
                $this->userEmail,
                $departureDate,
                $departureDate, // Use same date
                $comment,
                $numberofpeople,
                $totalprice
            ]);
            $lastInsertId = $this->db->lastInsertId();

            if ($lastInsertId) {
                Response::success(['bookingId' => $lastInsertId], "Đặt tour thành công!");
            } else {
                Response::error("Có lỗi xảy ra. Vui lòng thử lại", null, 500);
            }
        } catch (\Exception $e) {
            Response::error("Có lỗi xảy ra: " . $e->getMessage(), null, 500);
        }
    }

    /**
     * DELETE /api/user/booking/{id}
     */
    public function cancel($id) {
        $bookingId = intval($id);
        if ($bookingId <= 0) {
            Response::error("ID Đặt tour không hợp lệ", null, 400);
        }

        $stmt = $this->db->prepare("SELECT FromDate FROM tblbooking WHERE BookingId = ? AND UserEmail = ? AND status = 0");
        $stmt->execute([$bookingId, $this->userEmail]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($booking) {
            $fdate = $booking['FromDate'];
            $a = explode("/", $fdate);
            if (count($a) == 3) { // Try to handle MM/DD/YYYY or DD/MM/YYYY
                $val = array_reverse($a);
                $fdate = implode("-", $val); // Use standard format
            }
            
            $cdate = date('Y-m-d');
            $date1 = date_create($cdate);
            $date2 = date_create($fdate);
            
            if (!$date1 || !$date2) {
                // If parsing fails, allow cancel just in case, but warn
                // actually we'll just throw error
                Response::error("Lỗi định dạng ngày, không thể kiểm tra điều kiện hủy.", null, 500);
            }
            
            $diff = date_diff($date1, $date2);
            
            // Check if diff is positive (future date)
            if ($diff->invert == 0 && $diff->days > 1) {
                $status = 2; // Cancelled
                $cancelby = 'u';
                $cancelReason = "Hủy bởi người dùng";
                
                $updStmt = $this->db->prepare("UPDATE tblbooking SET status = ?, CancelledBy = ?, CancelReason = ? WHERE BookingId = ? AND UserEmail = ?");
                
                if ($updStmt->execute([$status, $cancelby, $cancelReason, $bookingId, $this->userEmail])) {
                    Response::success(null, "Hủy đặt tour thành công");
                } else {
                    Response::error("Có lỗi xảy ra. Vui lòng thử lại", null, 500);
                }
            } else {
                Response::error("Bạn không thể hủy đặt tour trước 24 giờ", null, 400);
            }
        } else {
            Response::error("Không tìm thấy đặt tour hoặc tour không thể hủy", null, 404);
        }
    }
}
