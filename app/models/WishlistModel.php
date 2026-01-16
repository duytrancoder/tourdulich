<?php

require_once(ROOT . '/core/Model.php');

class WishlistModel extends Model {
    /**
     * Add a tour package to user's wishlist
     */
    public function addToWishlist($userEmail, $packageId) {
        try {
            // Check if already exists to avoid duplicate key error
            if ($this->isInWishlist($userEmail, $packageId)) {
                return true; // Already in wishlist, consider it success
            }
            
            $sql = "INSERT INTO tblwishlist (UserEmail, PackageId) VALUES (:email, :packageId)";
            $query = $this->db->prepare($sql);
            $query->bindParam(':email', $userEmail, PDO::PARAM_STR);
            $query->bindParam(':packageId', $packageId, PDO::PARAM_INT);
            return $query->execute();
        } catch (PDOException $e) {
            // Log error for debugging
            error_log("WishlistModel::addToWishlist Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove a tour package from user's wishlist
     */
    public function removeFromWishlist($userEmail, $packageId) {
        try {
            $sql = "DELETE FROM tblwishlist WHERE UserEmail = :email AND PackageId = :packageId";
            $query = $this->db->prepare($sql);
            $query->bindParam(':email', $userEmail, PDO::PARAM_STR);
            $query->bindParam(':packageId', $packageId, PDO::PARAM_INT);
            return $query->execute();
        } catch (PDOException $e) {
            // Log error for debugging
            error_log("WishlistModel::removeFromWishlist Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all wishlist items for a user with full tour package details
     */
    public function getWishlistByUser($userEmail) {
        $sql = "SELECT w.id, w.PackageId, w.CreatedAt, 
                   p.PackageName, p.PackageType, p.PackageLocation, 
                   p.PackagePrice, p.PackageFetures, p.PackageImage, p.TourDuration
                FROM tblwishlist w
                INNER JOIN tbltourpackages p ON w.PackageId = p.PackageId
                WHERE w.UserEmail = :email
                ORDER BY w.CreatedAt DESC";
        $query = $this->db->prepare($sql);
        $query->bindParam(':email', $userEmail, PDO::PARAM_STR);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Check if a package is in user's wishlist
     */
    public function isInWishlist($userEmail, $packageId) {
        try {
            $sql = "SELECT COUNT(*) as count FROM tblwishlist WHERE UserEmail = :email AND PackageId = :packageId";
            $query = $this->db->prepare($sql);
            $query->bindParam(':email', $userEmail, PDO::PARAM_STR);
            $query->bindParam(':packageId', $packageId, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            return $result->count > 0;
        } catch (PDOException $e) {
            error_log("WishlistModel::isInWishlist Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get array of package IDs in user's wishlist
     */
    public function getWishlistPackageIds($userEmail) {
        $sql = "SELECT PackageId FROM tblwishlist WHERE UserEmail = :email";
        $query = $this->db->prepare($sql);
        $query->bindParam(':email', $userEmail, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        
        $packageIds = [];
        foreach ($results as $row) {
            $packageIds[] = $row->PackageId;
        }
        return $packageIds;
    }

    /**
     * Toggle wishlist status (add if not exists, remove if exists)
     */
    public function toggleWishlist($userEmail, $packageId) {
        try {
            if ($this->isInWishlist($userEmail, $packageId)) {
                return $this->removeFromWishlist($userEmail, $packageId);
            } else {
                return $this->addToWishlist($userEmail, $packageId);
            }
        } catch (Exception $e) {
            error_log("WishlistModel::toggleWishlist Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get wishlist count for a user
     */
    public function getWishlistCount($userEmail) {
        $sql = "SELECT COUNT(*) as count FROM tblwishlist WHERE UserEmail = :email";
        $query = $this->db->prepare($sql);
        $query->bindParam(':email', $userEmail, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        return $result->count;
    }
}
