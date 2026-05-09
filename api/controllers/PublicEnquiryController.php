<?php
namespace Api\Controllers;

use Api\Core\Response;
use Api\Core\Messages;
use Api\Core\Database;

/**
 * PublicEnquiryController
 * POST /api/enquiries — Public, không cần JWT
 */
class PublicEnquiryController {

    private $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function submit() {
        $data = json_decode(file_get_contents('php://input'), true);

        $fname       = trim($data['fname']       ?? '');
        $email       = trim($data['email']       ?? '');
        $mobile      = trim($data['mobileno']    ?? '');
        $subject     = trim($data['subject']     ?? '');
        $description = trim($data['description'] ?? '');

        // Validation
        $errors = [];
        if (empty($fname))                                      $errors['fname']       = 'Họ và tên không được để trống';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))         $errors['email']       = 'Email không hợp lệ';
        if (!preg_match('/^[0-9]{10}$/', $mobile))              $errors['mobileno']    = 'Số điện thoại phải có đúng 10 chữ số';
        if (empty($subject) || strlen($subject) > 100)          $errors['subject']     = 'Chủ đề không được để trống và tối đa 100 ký tự';
        if (empty($description))                                $errors['description'] = 'Nội dung không được để trống';

        if (!empty($errors)) {
            Response::error(Messages::ERROR_INVALID_DATA, $errors, 422);
        }

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO tblenquiry (FullName, EmailId, MobileNumber, Subject, Description)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([$fname, $email, $mobile, $subject, $description]);

            Response::success(null, Messages::ENQUIRY_SUBMIT_SUCCESS, 201);
        } catch (\Exception $e) {
            Response::error(Messages::ERROR_SYSTEM, null, 500);
        }
    }
}
