<?php
namespace Api\Controllers;

use Api\Core\Response;
use Api\Core\JWTHandler;
use Api\Core\Database;
use PDO;

class AdminPageController {
    
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
     * GET /api/admin/pages/{type}
     */
    public function show($type) {
        try {
            $stmt = $this->db->prepare("SELECT detail FROM tblpages WHERE type = ?");
            $stmt->execute([$type]);
            $detail = $stmt->fetchColumn();
            
            if ($detail !== false) {
                Response::success(['detail' => $detail], "Lấy nội dung trang thành công");
            } else {
                Response::error("Trang không tồn tại", null, 404);
            }
        } catch (\Exception $e) {
            Response::error("Lỗi khi lấy nội dung trang", null, 500);
        }
    }

    /**
     * PUT /api/admin/pages/{type}
     */
    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $detail = $data['detail'] ?? '';

        if (empty($detail)) {
            Response::error("Nội dung không được để trống", null, 400);
        }

        try {
            $stmt = $this->db->prepare("UPDATE tblpages SET detail = ? WHERE type = ?");
            $stmt->execute([$detail, $id]);
            
            Response::success(null, "Cập nhật trang thành công");
        } catch (\Exception $e) {
            Response::error("Lỗi khi cập nhật trang", null, 500);
        }
    }
}
