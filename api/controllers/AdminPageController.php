<?php
namespace Api\Controllers;

use Api\Core\Response;
use Api\Core\JWTHandler;
use Api\Core\Messages;
use Api\Core\Database;
use PDO;

class AdminPageController {
    
    private $db;

    public function __construct() {
        // Authenticate as Admin
        $user = JWTHandler::verifyBearerToken();
        if ($user->role !== 'admin') {
            Response::error(Messages::ERROR_FORBIDDEN, null, 403);
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
                Response::success(['detail' => $detail], Messages::SUCCESS);
            } else {
                Response::error(Messages::ERROR_INVALID_DATA, null, 404);
            }
        } catch (\Exception $e) {
            Response::error(Messages::ERROR_DATABASE, null, 500);
        }
    }

    /**
     * PUT /api/admin/pages/{type}
     */
    public function update($id) {
        $data = json_decode(file_get_contents('php://input'), true);
        $detail = $data['detail'] ?? '';

        if (empty($detail)) {
            Response::error(Messages::ERROR_MISSING_INFO, null, 400);
        }

        try {
            $stmt = $this->db->prepare("UPDATE tblpages SET detail = ? WHERE type = ?");
            $stmt->execute([$detail, $id]);
            
            Response::success(null, Messages::TOUR_UPDATE_SUCCESS);
        } catch (\Exception $e) {
            Response::error(Messages::ERROR_SYSTEM, null, 500);
        }
    }
}
