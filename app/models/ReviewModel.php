<?php
require_once(ROOT . '/core/Model.php');

class ReviewModel extends Model {
    
    public function hasReview($bookingId) {
        $sql = "SELECT id FROM tblreviews WHERE BookingId = :bid LIMIT 1";
        $q = $this->db->prepare($sql);
        $q->bindParam(':bid', $bookingId, PDO::PARAM_INT);
        $q->execute();
        return $q->fetch(PDO::FETCH_OBJ) ? true : false;
    }
    
    public function createReview($bookingId, $userEmail, $packageId, $rating, $comment) {
        $sql = "INSERT INTO tblreviews (BookingId, UserEmail, PackageId, Rating, Comment)
                VALUES (:bid, :email, :pid, :rating, :comment)";
        $q = $this->db->prepare($sql);
        $q->bindParam(':bid',     $bookingId,  PDO::PARAM_INT);
        $q->bindParam(':email',   $userEmail,  PDO::PARAM_STR);
        $q->bindParam(':pid',     $packageId,  PDO::PARAM_INT);
        $q->bindParam(':rating',  $rating,     PDO::PARAM_INT);
        $q->bindParam(':comment', $comment,    PDO::PARAM_STR);
        return $q->execute();
    }
    
    public function getReviewsByPackage($packageId) {
        $sql = "SELECT r.*, u.FullName 
                FROM tblreviews r
                JOIN tblusers u ON u.EmailId = r.UserEmail
                WHERE r.PackageId = :pid 
                ORDER BY r.CreatedAt DESC";
        $q = $this->db->prepare($sql);
        $q->bindParam(':pid', $packageId, PDO::PARAM_INT);
        $q->execute();
        return $q->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function getAverageRating($packageId) {
        $sql = "SELECT AVG(Rating) as avg_rating, COUNT(*) as total 
                FROM tblreviews WHERE PackageId = :pid";
        $q = $this->db->prepare($sql);
        $q->bindParam(':pid', $packageId, PDO::PARAM_INT);
        $q->execute();
        return $q->fetch(PDO::FETCH_OBJ);
    }
}
