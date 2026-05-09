<?php
namespace Api\Controllers;

use Api\Core\Response;
use Api\Core\JWTHandler;
use Api\Core\Database;
use PDO;

class AdminBookingController {
    
    private $db;

    public function __construct() {
        $user = JWTHandler::verifyBearerToken();
        if ($user->role !== 'admin') {
            Response::error("Bạn không có quyền truy cập", null, 403);
        }
        $this->db = Database::getConnection();
    }

    /**
     * GET /api/admin/bookings
     */
    public function index() {
        $status = $_GET['status'] ?? '';
        
        $sql = "SELECT 
                    b.BookingId, 
                    b.PackageId,
                    b.UserEmail,
                    b.FromDate, 
                    b.ToDate, 
                    b.Comment, 
                    b.RegDate, 
                    b.status, 
                    b.CancelledBy, 
                    b.UpdationDate,
                    b.NumberOfPeople,
                    b.TotalPrice,
                    u.FullName as UserName,
                    p.PackageName
                FROM tblbooking b
                JOIN tblusers u ON b.UserEmail = u.EmailId
                JOIN tbltourpackages p ON b.PackageId = p.PackageId
                WHERE 1=1";
        
        $params = [];
        if ($status !== '') {
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY b.RegDate DESC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Format date d/m/Y
            foreach ($bookings as &$b) {
                $b['RegDateFormatted'] = date('d/m/Y', strtotime($b['RegDate']));
                $b['FromDateFormatted'] = date('d/m/Y', strtotime($b['FromDate']));
                $b['ToDateFormatted'] = date('d/m/Y', strtotime($b['ToDate']));
            }

            Response::success($bookings, "Lấy danh sách đặt tour thành công");
        } catch (\Exception $e) {
            Response::error("Lỗi khi truy vấn dữ liệu: " . $e->getMessage(), null, 500);
        }
    }

    /**
     * GET /api/admin/bookings/{id}
     */
    public function show($id) {
        $sql = "SELECT 
                    b.*, 
                    u.FullName as UserName, 
                    u.MobileNumber,
                    p.PackageName,
                    p.PackageLocation
                FROM tblbooking b
                JOIN tblusers u ON b.UserEmail = u.EmailId
                JOIN tbltourpackages p ON b.PackageId = p.PackageId
                WHERE b.BookingId = ? LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($booking) {
                $booking['RegDateFormatted'] = date('d/m/Y H:i', strtotime($booking['RegDate']));
                $booking['FromDateFormatted'] = date('d/m/Y', strtotime($booking['FromDate']));
                $booking['ToDateFormatted'] = date('d/m/Y', strtotime($booking['ToDate']));
                Response::success($booking, "Lấy chi tiết đặt tour thành công");
            } else {
                Response::error("Không tìm thấy đặt tour", null, 404);
            }
        } catch (\Exception $e) {
            Response::error("Lỗi khi truy vấn dữ liệu", null, 500);
        }
    }

    /**
     * PUT /api/admin/bookings/{id}
     */
    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $status = $data['status'] ?? null;
        $adminRemark = $data['adminRemark'] ?? '';

        if ($status === null) {
            Response::error("Thiếu trạng thái cập nhật", null, 400);
        }

        try {
            $sql = "UPDATE tblbooking SET status = ?, AdminRemark = ?, UpdationDate = CURRENT_TIMESTAMP WHERE BookingId = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$status, $adminRemark, $id]);

            Response::success(null, "Cập nhật trạng thái đặt tour thành công");
        } catch (\Exception $e) {
            Response::error("Lỗi khi cập nhật dữ liệu", null, 500);
        }
    }
}
