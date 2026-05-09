<?php
namespace Api\Controllers;

use Api\Core\Response;
use Api\Core\JWTHandler;
use Api\Core\Database;

/**
 * UserIssueController
 * POST /api/user/issues — Yêu cầu JWT (email lấy từ token, không dùng $_SESSION)
 */
class UserIssueController {

    private $userEmail;
    private $db;

    public function __construct() {
        $user = JWTHandler::verifyBearerToken();
        $this->userEmail = $user->email;
        $this->db = Database::getConnection();
    }

    public function submit() {
        $data = json_decode(file_get_contents('php://input'), true);

        $issue       = trim($data['issue']       ?? '');
        $description = trim($data['description'] ?? '');

        // Validation
        $errors = [];
        if (empty($issue))                         $errors['issue']       = 'Chủ đề không được để trống';
        if (strlen($issue) > 100)                  $errors['issue']       = 'Chủ đề không được vượt quá 100 ký tự';
        if (empty($description))                   $errors['description'] = 'Mô tả không được để trống';
        if (strlen($description) > 5000)           $errors['description'] = 'Mô tả không được vượt quá 5000 ký tự';

        if (!empty($errors)) {
            Response::error('Dữ liệu không hợp lệ', $errors, 422);
        }

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO tblissues (UserEmail, Issue, Description) VALUES (?, ?, ?)'
            );
            $stmt->execute([$this->userEmail, $issue, $description]);

            Response::success(null, 'Yêu cầu hỗ trợ đã được gửi. Chúng tôi sẽ phản hồi sớm nhất có thể!', 201);
        } catch (\Exception $e) {
            Response::error('Có lỗi xảy ra, vui lòng thử lại', null, 500);
        }
    }
}
