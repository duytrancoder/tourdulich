<?php

require_once(ROOT . '/core/Model.php');

class PackageModel extends Model {
    public function getFeaturedPackages($limit = 4) {
        $limit = (int)$limit;
        $sql = "SELECT * FROM tbltourpackages ORDER BY rand() LIMIT " . $limit;
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function getDistinctLocations() {
        $sql = "SELECT DISTINCT PackageLocation FROM tbltourpackages WHERE PackageLocation <> '' ORDER BY PackageLocation";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function getFilteredPackages($keyword, $location, $price) {
        $sql = "SELECT * FROM tbltourpackages WHERE 1=1";
        if($keyword !== '') {
            $sql .= " AND PackageName LIKE :keyword";
        }
        if($location !== '') {
            $sql .= " AND PackageLocation = :location";
        }
        if($price === 'under-200') {
            $sql .= " AND PackagePrice < 4800000"; // dưới 4.8 triệu
        } elseif($price === '200-500') {
            $sql .= " AND PackagePrice BETWEEN 4800000 AND 12000000"; // 4.8 - 12 triệu
        } elseif($price === 'over-500') {
            $sql .= " AND PackagePrice > 12000000"; // trên 12 triệu
        }
        $sql .= " ORDER BY Creationdate DESC";

        $query = $this->db->prepare($sql);
        if($keyword !== '') {
            $likeKeyword = "%".$keyword."%";
            $query->bindParam(':keyword', $likeKeyword, PDO::PARAM_STR);
        }
        if($location !== '') {
            $query->bindParam(':location', $location, PDO::PARAM_STR);
        }
        $query->execute();
        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function getPackageById($id) {
        $sql = "SELECT * from tbltourpackages where PackageId=:id";
        $query = $this->db->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        return $query->fetch(PDO::FETCH_OBJ);
    }
}
