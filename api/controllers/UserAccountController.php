<?php
namespace Api\Controllers;

use Api\Core\Response;
use Api\Core\JWTHandler;
use Api\Core\Database;
use PDO;

class UserAccountController {

    private $userEmail;

    public function __construct() {
        // Yêu cầu token hợp lệ
        $user = JWTHandler::verifyBearerToken();
        $this->userEmail = $user->email;
    }

    /**
     * GET /api/user/account
     * Lấy toàn bộ thông tin tài khoản (Profile, Bookings, Wishlist)
     */
    public function index() {
        $db = Database::getConnection();

        // 1. Get Profile
        $stmt = $db->prepare("SELECT FullName, MobileNumber, EmailId, Address, DateOfBirth, Gender, Avatar FROM tblusers WHERE EmailId = ?");
        $stmt->execute([$this->userEmail]);
        $profile = $stmt->fetch();

        // 2. Get Bookings
        $stmt = $db->prepare("SELECT tblbooking.BookingId as bookid, tblbooking.PackageId as pkgid, tblbooking.FromDate as fromdate, 
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
        $stmt = $db->prepare("SELECT w.WishlistId, p.PackageId, p.PackageName, p.PackageType, p.PackageLocation, 
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
}
