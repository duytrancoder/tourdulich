<?php
namespace Api\Controllers;

use Api\Core\Response;
use Api\Core\JWTHandler;
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
        // 1. Get Profile
        $stmt = $this->db->prepare("SELECT FullName, MobileNumber, EmailId, Address, DateOfBirth, Gender, Avatar FROM tblusers WHERE EmailId = ?");
        $stmt->execute([$this->userEmail]);
        $profile = $stmt->fetch();

        // 2. Get Bookings
        $stmt = $this->db->prepare("SELECT tblbooking.BookingId as bookid, tblbooking.PackageId as pkgid, tblbooking.FromDate as fromdate, 
                                     tblbooking.RegDate as regdate, tblbooking.status as status, tblbooking.CancelledBy as cancelby, 
                                     tblbooking.CancelReason as cancelreason, tblbooking.UpdationDate as upddate, tblbooking.Comment as comment, 
                                     tblbooking.AdminMessage as customermessage, tbltourpackages.PackageName as packagename, 
                                     tbltourpackages.PackagePrice as packageprice,
                                     (SELECT COUNT(*) FROM tblreviews WHERE BookingId = tblbooking.BookingId) as hasreview
                              FROM tblbooking 
                              JOIN tbltourpackages ON tbltourpackages.PackageId = tblbooking.PackageId 
                              WHERE tblbooking.UserEmail = ? 
                              ORDER BY tblbooking.RegDate DESC");
        $stmt->execute([$this->userEmail]);
        $bookings = $stmt->fetchAll();

        // 3. Get Wishlist
        $stmt = $this->db->prepare("SELECT w.WishlistId, p.PackageId, p.PackageName, p.PackageType, p.PackageLocation, 
                                     p.PackagePrice, p.PackageFetures, p.PackageImage, p.TourDuration
                              FROM tblwishlist w
                              JOIN tbltourpackages p ON w.PackageId = p.PackageId
                              WHERE w.UserEmail = ?
                              ORDER BY w.CreatedAt DESC");
        $stmt->execute([$this->userEmail]);
        $wishlist = $stmt->fetchAll();

        Response::success([
            'profile' => $profile,
            'bookings' => $bookings,
            'wishlist' => $wishlist
        ], "Lấy thông tin tài khoản thành công");
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
            Response::error("Họ và tên không được để trống", null, 400);
        }

        if (!preg_match('/^[0-9]{10}$/', $mobileno)) {
            Response::error("Số điện thoại phải có đúng 10 chữ số", null, 400);
        }

        try {
            $stmt = $this->db->prepare(
                "UPDATE tblusers SET FullName=?, MobileNumber=?, Address=?, DateOfBirth=?, Gender=? WHERE EmailId=?"
            );
            $stmt->execute([$name, $mobileno, $address, $dateOfBirth ?: null, $gender, $this->userEmail]);

            Response::success(null, "Hồ sơ đã được cập nhật thành công");
        } catch (\Exception $e) {
            Response::error("Có lỗi xảy ra khi cập nhật", null, 500);
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
            Response::error("Vui lòng nhập đầy đủ thông tin", null, 400);
        }

        if (strlen($newPassword) < 6) {
            Response::error("Mật khẩu mới phải có ít nhất 6 ký tự", null, 400);
        }

        if ($confirmPassword !== '' && $newPassword !== $confirmPassword) {
            Response::error("Mật khẩu mới và xác nhận không khớp", null, 400);
        }

        // Lấy hash hiện tại
        $stmt = $this->db->prepare("SELECT Password FROM tblusers WHERE EmailId = ?");
        $stmt->execute([$this->userEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            Response::error("Không tìm thấy tài khoản", null, 404);
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
            Response::error("Mật khẩu hiện tại không chính xác", null, 401);
        }

        // Hash và lưu mật khẩu mới
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE tblusers SET Password = ? WHERE EmailId = ?");
        $stmt->execute([$newHash, $this->userEmail]);

        Response::success(null, "Đổi mật khẩu thành công");
    }
}
