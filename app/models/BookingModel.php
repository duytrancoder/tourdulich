<?php

require_once(ROOT . '/core/Model.php');

class BookingModel extends Model {
    public function createBooking($pid, $useremail, $fromdate, $todate, $comment, $numberofpeople = 1, $totalprice = 0) {
        $status = 0;
        $sql = "INSERT INTO tblbooking(PackageId,UserEmail,FromDate,ToDate,Comment,NumberOfPeople,TotalPrice,status) VALUES(:pid,:useremail,:fromdate,:todate,:comment,:numberofpeople,:totalprice,:status)";
        $query = $this->db->prepare($sql);
        $query->bindParam(':pid', $pid, PDO::PARAM_INT);
        $query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
        $query->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
        $query->bindParam(':todate', $todate, PDO::PARAM_STR);
        $query->bindParam(':comment', $comment, PDO::PARAM_STR);
        $query->bindParam(':numberofpeople', $numberofpeople, PDO::PARAM_INT);
        $query->bindParam(':totalprice', $totalprice, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_INT);
        $query->execute();
        return $this->db->lastInsertId();
    }

    public function getBookingsByUserEmail($email) {
        $sql = "SELECT tblbooking.BookingId as bookid,tblbooking.PackageId as pkgid,tbltourpackages.PackageName as packagename,tbltourpackages.PackagePrice as packageprice,tblbooking.FromDate as fromdate,tblbooking.ToDate as todate,tblbooking.Comment as comment,tblbooking.status as status,tblbooking.RegDate as regdate,tblbooking.CancelledBy as cancelby,tblbooking.UpdationDate as upddate,tblbooking.CancelReason as cancelreason,tblbooking.CustomerMessage as customermessage from tblbooking join tbltourpackages on tbltourpackages.PackageId=tblbooking.PackageId where UserEmail=:email ORDER BY tblbooking.RegDate DESC";
        $query = $this->db->prepare($sql);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function getBookingForCancellation($bookingId, $userEmail) {
        $sql = "SELECT FromDate FROM tblbooking WHERE UserEmail=:email and BookingId=:bid";
        $query = $this->db->prepare($sql);
        $query->bindParam(':email', $userEmail, PDO::PARAM_STR);
        $query->bindParam(':bid', $bookingId, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ);
    }

    public function cancelBooking($bookingId, $userEmail) {
        $status = 2;
        $cancelby = 'u';
        $sql = "UPDATE tblbooking SET status=:status,CancelledBy=:cancelby WHERE UserEmail=:email and BookingId=:bid";
        $query = $this->db->prepare($sql);
        $query->bindParam(':status', $status, PDO::PARAM_INT);
        $query->bindParam(':cancelby', $cancelby, PDO::PARAM_STR);
        $query->bindParam(':email', $userEmail, PDO::PARAM_STR);
        $query->bindParam(':bid', $bookingId, PDO::PARAM_INT);
        return $query->execute();
    }
}
