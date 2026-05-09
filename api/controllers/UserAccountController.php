<?php
namespace Api\Controllers;

use Api\Core\Response;
use Api\Core\JWTHandler;
use Api\Core\Messages;
use Api\Core\Database;
use PDO;

class UserAccountController {

    private $userEmail;
    private $db;

    public function __construct() {
        $user = JWTHandler::verifyBearerToken();
        $this->userEmail = $user->email;
        $this->db = Database::getConnection();
    }

    /**
     * GET /api/user/account
     * Lấy toàn bộ thông tin tài khoản (Profile, Bookings, Wishlist)
     */
    public function index() {
        try {
            // 1. Profile
            $stmt = $this->db->prepare(
                "SELECT FullName, MobileNumber, EmailId, Address, DateOfBirth, Gender, Avatar
                 FROM tblusers WHERE EmailId = ?"
            );
            $stmt->execute([$this->userEmail]);
            $profile = $stmt->fetch();

            // 2. Bookings — tên cột đúng: CustomerMessage (không phải AdminMessage)
            $stmt = $this->db->prepare(
                "SELECT
                    b.BookingId       AS bookid,
                    b.PackageId       AS pkgid,
                    b.FromDate        AS fromdate,
                    b.RegDate         AS regdate,
                    b.status          AS status,
                    b.CancelledBy     AS cancelby,
                    b.CancelReason    AS cancelreason,
                    b.UpdationDate    AS upddate,
                    b.Comment         AS comment,
                    b.CustomerMessage AS customermessage,
                    p.PackageName     AS packagename,
                    p.PackagePrice    AS packageprice,
                    (SELECT COUNT(*) FROM tblreviews r WHERE r.BookingId = b.BookingId) AS hasreview
                 FROM tblbooking b
                 JOIN tbltourpackages p ON p.PackageId = b.PackageId
                 WHERE b.UserEmail = ?
                 ORDER BY b.RegDate DESC"
            );
            $stmt->execute([$this->userEmail]);
            $bookings = $stmt->fetchAll();

            // 3. Wishlist — PK của tblwishlist là `id`, không phải `WishlistId`
            $stmt = $this->db->prepare(
                "SELECT w.id AS wishlist_id, p.PackageId, p.PackageName, p.PackageType, p.PackageLocation,
                        p.PackagePrice, p.PackageFetures, p.PackageImage, p.TourDuration
                 FROM tblwishlist w
                 JOIN tbltourpackages p ON w.PackageId = p.PackageId
                 WHERE w.UserEmail = ?
                 ORDER BY w.CreatedAt DESC"
            );
            $stmt->execute([$this->userEmail]);
            $wishlist = $stmt->fetchAll();

            Response::success([
                'profile'  => $profile,
                'bookings' => $bookings,
                'wishlist' => $wishlist
            ], Messages::PROFILE_FETCH_SUCCESS);

        } catch (\Exception $e) {
            Response::error(Messages::ERROR_DATABASE, null, 500);
        }
    }


    /**
     * PUT /api/user/profile
     * Cập nhật hồ sơ cá nhân (Tên, SĐT, Địa chỉ, Ngày sinh, Giới tính)
     */
    public function updateProfile() {
        $data = json_decode(file_get_contents("php://input"), true);

        $name        = trim($data['name']        ?? '');
        $mobileno    = trim($data['mobileno']     ?? '');
        $address     = trim($data['address']      ?? '');
        $dateOfBirth = trim($data['dateofbirth']  ?? '');
        $gender      = trim($data['gender']       ?? '');

        if (empty($name)) {
            Response::error(Messages::ERROR_MISSING_INFO, null, 400);
        }

        if (!preg_match('/^[0-9]{10}$/', $mobileno)) {
            Response::error(Messages::ERROR_INVALID_DATA, null, 400);
        }

        try {
            $stmt = $this->db->prepare(
                "UPDATE tblusers SET FullName=?, MobileNumber=?, Address=?, DateOfBirth=?, Gender=? WHERE EmailId=?"
            );
            $stmt->execute([$name, $mobileno, $address, $dateOfBirth ?: null, $gender, $this->userEmail]);

            Response::success(null, Messages::PROFILE_UPDATE_SUCCESS);
        } catch (\Exception $e) {
            Response::error(Messages::ERROR_SYSTEM, null, 500);
        }
    }

    /**
     * PUT /api/user/password
     * Đổi mật khẩu (yêu cầu mật khẩu hiện tại)
     */
    public function updatePassword() {
        $data = json_decode(file_get_contents("php://input"), true);

        $currentPassword  = $data['password']        ?? '';
        $newPassword      = $data['newpassword']      ?? '';
        $confirmPassword  = $data['confirmpassword']  ?? '';

        if (empty($currentPassword) || empty($newPassword)) {
            Response::error(Messages::ERROR_MISSING_INFO, null, 400);
        }

        if (strlen($newPassword) < 6) {
            Response::error(Messages::AUTH_PASSWORD_MIN, null, 400);
        }

        if ($confirmPassword !== '' && $newPassword !== $confirmPassword) {
            Response::error(Messages::PWD_MISMATCH, null, 400);
        }

        // Lấy hash hiện tại
        $stmt = $this->db->prepare("SELECT Password FROM tblusers WHERE EmailId = ?");
        $stmt->execute([$this->userEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            Response::error(Messages::ERROR_INVALID_DATA, null, 404);
        }

        // Kiểm tra mật khẩu hiện tại (hỗ trợ cả MD5 cũ và password_hash mới)
        $storedHash = $user['Password'];
        $isValid = false;
        if (strlen($storedHash) === 32 && ctype_xdigit($storedHash)) {
            $isValid = ($storedHash === md5($currentPassword));
        } else {
            $isValid = password_verify($currentPassword, $storedHash);
        }

        if (!$isValid) {
            Response::error(Messages::PWD_CURRENT_WRONG, null, 401);
        }

        // Hash và lưu mật khẩu mới
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE tblusers SET Password = ? WHERE EmailId = ?");
        $stmt->execute([$newHash, $this->userEmail]);

        Response::success(null, Messages::PWD_CHANGE_SUCCESS);
    }
}
