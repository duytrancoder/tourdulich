<?php
class TourModel {
    public function getAllTours() {
        // Chuẩn hóa câu lệnh SQL viết hoa cho đẹp
        $sql = "SELECT * FROM tours WHERE status = 1 ORDER BY id DESC";
        return $sql;
    }
}