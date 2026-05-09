<?php
namespace Api\Controllers;

use Api\Core\Response;
use Api\Core\JWTHandler;
use Api\Core\Database;
use PDO;

class AdminIssueController {
    
    private $db;

    public function __construct() {
        $user = JWTHandler::verifyBearerToken();
        if ($user->role !== 'admin') {
            Response::error("Bạn không có quyền truy cập", null, 403);
        }
        $this->db = Database::getConnection();
    }

    /**
     * GET /api/admin/issues
     */
    public function index() {
        try {
            // Join tblusers if possible to get FullName, but tblissues uses UserEmail
            $sql = "SELECT i.*, u.FullName 
                    FROM tblissues i 
                    LEFT JOIN tblusers u ON i.UserEmail = u.EmailId 
                    ORDER BY i.PostingDate DESC";
            $stmt = $this->db->query($sql);
            $issues = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($issues as &$issue) {
                $issue['PostingDateFormatted'] = date('d/m/Y H:i', strtotime($issue['PostingDate']));
            }

            Response::success($issues, "Lấy danh sách hỗ trợ thành công");
        } catch (\Exception $e) {
            Response::error("Lỗi khi truy vấn dữ liệu", null, 500);
        }
    }

    /**
     * PUT /api/admin/issues/{id}
     * Đánh dấu đã xử lý (Ghi remark và set ngày)
     */
    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $remark = trim($data['remark'] ?? '');

        if (empty($remark)) {
            Response::error("Vui lòng nhập ghi chú xử lý", null, 400);
        }

        try {
            $sql = "UPDATE tblissues SET AdminRemark = ?, AdminremarkDate = CURRENT_TIMESTAMP WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$remark, $id]);

            Response::success(null, "Đã cập nhật ghi chú xử lý yêu cầu");
        } catch (\Exception $e) {
            Response::error("Lỗi khi cập nhật dữ liệu", null, 500);
        }
    }
}
