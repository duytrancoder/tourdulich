<?php
namespace Api\Models;

use Api\Core\Database;
use PDO;

class Tour {
    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll($search = '', $type = '', $location = '') {
        $sql = "SELECT * FROM tbltourpackages WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (PackageName LIKE :search_name OR PackageId = :search_id)";
            $params[':search_name'] = '%' . $search . '%';
            $normalizedSearchId = preg_replace('/^#?PKG-?/i', '', $search);
            $params[':search_id'] = ctype_digit($normalizedSearchId) ? (int)$normalizedSearchId : 0;
        }

        if (!empty($type)) {
            // Map slug from REST API to actual DB values
            $typeMap = [
                'economy' => 'Tour tiết kiệm',
                'standard' => 'Tour tiêu chuẩn',
                'premium' => 'Tour cao cấp',
                'private' => 'Tour riêng'
            ];
            
            $dbType = $typeMap[$type] ?? $type; // Fallback to raw value if not mapped

            $sql .= " AND PackageType LIKE :type";
            $params[':type'] = '%' . $dbType . '%';
        }

        if (!empty($location)) {
            // Restore spaces from slug (ha-noi -> ha noi)
            $dbLocation = str_replace('-', ' ', $location);
            $sql .= " AND PackageLocation LIKE :location";
            $params[':location'] = '%' . $dbLocation . '%';
        }

        $sql .= " ORDER BY PackageId DESC";
        
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($name, $type, $location, $duration, $price, $features, $details, $image) {
        $sql = "INSERT INTO tbltourpackages(PackageName, PackageType, PackageLocation, TourDuration, PackagePrice, PackageFetures, PackageDetails, PackageImage) 
                VALUES(:name, :type, :loc, :dur, :price, :feat, :det, :img)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':type' => $type,
            ':loc' => $location,
            ':dur' => $duration,
            ':price' => $price,
            ':feat' => $features,
            ':det' => $details,
            ':img' => $image
        ]);
        return $this->db->lastInsertId();
    }

    public function addItinerary($packageId, $timeLabel, $activity, $sortOrder) {
        $sql = "INSERT INTO tblitinerary (PackageId, TimeLabel, Activity, SortOrder) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$packageId, $timeLabel, $activity, $sortOrder]);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tbltourpackages WHERE PackageId = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getItineraries($packageId) {
        $stmt = $this->db->prepare("SELECT * FROM tblitinerary WHERE PackageId = ? ORDER BY SortOrder ASC, ItineraryId ASC");
        $stmt->execute([$packageId]);
        return $stmt->fetchAll();
    }

    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];

        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $sql = "UPDATE tbltourpackages SET " . implode(', ', $fields) . " WHERE PackageId = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function clearItineraries($packageId) {
        $stmt = $this->db->prepare("DELETE FROM tblitinerary WHERE PackageId = ?");
        return $stmt->execute([$packageId]);
    }

    public function delete($id) {
        if ($this->bookedTour($id)) {
            return false;
        }
        $sql = "DELETE FROM tbltourpackages WHERE PackageId = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function bookedTour($id) {
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM tblbooking WHERE PackageId = ?");
    $stmt->execute([(int)$id]);
    return (int)$stmt->fetchColumn() > 0;
    }
}
