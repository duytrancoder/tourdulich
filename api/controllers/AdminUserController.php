<?php
namespace Api\Controllers;

use Api\Core\Response;
use Api\Core\JWTHandler;
use Api\Core\Database;
use PDO;

class AdminUserController {
    
    private $db;

    public function __construct() {
        // Authenticate as Admin
        $user = JWTHandler::verifyBearerToken();
        if ($user->role !== 'admin') {
            Response::error("Bạn không có quyền truy cập", null, 403);
        }
        $this->db = Database::getConnection();
    }

    /**
     * GET /api/admin/users
     */
    public function index() {
        $searchName = $_GET['name'] ?? '';
        $searchPhone = $_GET['phone'] ?? '';
        $searchEmail = $_GET['email'] ?? '';

        $sql = "SELECT id, FullName, MobileNumber, EmailId, RegDate FROM tblusers WHERE 1=1";
        $params = [];

        if (!empty($searchName)) {
            $sql .= " AND (FullName LIKE ? OR id = ?)";
            $params[] = "%$searchName%";
            $params[] = ltrim($searchName, '#');
        }
        if (!empty($searchPhone)) {
            $sql .= " AND MobileNumber LIKE ?";
            $params[] = "%$searchPhone%";
        }
        if (!empty($searchEmail)) {
            $sql .= " AND EmailId LIKE ?";
            $params[] = "%$searchEmail%";
        }

        $sql .= " ORDER BY id DESC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            Response::success($users, "Lấy danh sách người dùng thành công");
        } catch (\Exception $e) {
            Response::error("Lỗi khi truy vấn dữ liệu", null, 500);
        }
    }

    /**
     * DELETE /api/admin/users/{id}
     */
    public function delete($id) {
        $userId = intval($id);
        if ($userId <= 0) {
            Response::error("ID người dùng không hợp lệ", null, 400);
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM tblusers WHERE id = ?");
            $stmt->execute([$userId]);
            
            if ($stmt->rowCount() > 0) {
                Response::success(null, "Xóa người dùng thành công");
            } else {
                Response::error("Người dùng không tồn tại hoặc đã bị xóa", null, 404);
            }
        } catch (\Exception $e) {
            Response::error("Lỗi khi xóa người dùng", null, 500);
        }
    }
}
