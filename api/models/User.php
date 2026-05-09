<?php
namespace Api\Models;

use Api\Core\Database;
use PDO;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get user by email
     */
    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM tblusers WHERE EmailId = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Create new user
     */
    public function create($fullName, $mobile, $email, $password) {
        $hashedPassword = md5($password); // Note: Keep using md5 for legacy compatibility or upgrade to password_hash if starting fresh. Here keeping legacy.
        
        $stmt = $this->db->prepare("INSERT INTO tblusers(FullName, MobileNumber, EmailId, Password) VALUES(?, ?, ?, ?)");
        if ($stmt->execute([$fullName, $mobile, $email, $hashedPassword])) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Check if password matches
     */
    public function verifyPassword($user, $password) {
        // Based on original system using md5
        return $user['Password'] === md5($password);
    }
}
