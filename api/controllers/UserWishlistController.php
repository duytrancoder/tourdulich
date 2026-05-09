<?php
namespace Api\Controllers;

use Api\Core\Response;
use Api\Core\JWTHandler;
use Api\Core\Messages;
use Api\Core\Database;
use PDO;

class UserWishlistController {
    
    private $userEmail;
    private $db;

    public function __construct() {
        // Authenticate request
        $user = JWTHandler::verifyBearerToken();
        $this->userEmail = $user->email;
        $this->db = Database::getConnection();
    }

    /**
     * GET /api/user/wishlist
     */
    public function getIds() {
        $stmt = $this->db->prepare("SELECT PackageId FROM tblwishlist WHERE UserEmail = ?");
        $stmt->execute([$this->userEmail]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $packageIds = array_map(function($row) {
            return (int)$row['PackageId'];
        }, $results);
        
        Response::success(['packageIds' => $packageIds], Messages::WISHLIST_FETCH_SUCCESS);
    }

    /**
     * POST /api/user/wishlist/toggle/{id}
     */
    public function toggle($id) {
        $packageId = intval($id);
        if ($packageId <= 0) {
            Response::error("ID tour không hợp lệ", null, 400);
        }
        
        try {
            // Check if in wishlist
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM tblwishlist WHERE UserEmail = ? AND PackageId = ?");
            $stmt->execute([$this->userEmail, $packageId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $isInWishlist = ($result['count'] > 0);
            
            if ($isInWishlist) {
                // Remove
                $delStmt = $this->db->prepare("DELETE FROM tblwishlist WHERE UserEmail = ? AND PackageId = ?");
                $delStmt->execute([$this->userEmail, $packageId]);
                Response::success(['inWishlist' => false], Messages::WISHLIST_REMOVED);
            } else {
                // Add
                $insStmt = $this->db->prepare("INSERT INTO tblwishlist (UserEmail, PackageId) VALUES (?, ?)");
                $insStmt->execute([$this->userEmail, $packageId]);
                Response::success(['inWishlist' => true], Messages::WISHLIST_ADDED);
            }
        } catch (\Exception $e) {
            Response::error(Messages::ERROR_SYSTEM, null, 500);
        }
    }
}
