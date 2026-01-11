<?php
class EnquiryController extends Controller {
    public function index() {
        $data = [
            'error' => $_SESSION['error'] ?? null,
            'msg' => $_SESSION['msg'] ?? null
        ];
        unset($_SESSION['error'], $_SESSION['msg']);
        $this->view('enquiry/index', $data);
    }

    public function submit() {
        if (isset($_POST['submit1'])) {
            $fname = trim($_POST['fname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $mobile = trim($_POST['mobileno'] ?? '');
            $subject = trim($_POST['subject'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // Validate inputs
            if (empty($fname) || empty($email) || empty($mobile) || empty($subject) || empty($description)) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin";
                header('location:' . BASE_URL . 'enquiry');
                exit;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Email không hợp lệ";
                header('location:' . BASE_URL . 'enquiry');
                exit;
            }

            if (!preg_match('/^[0-9]{10}$/', $mobile)) {
                $_SESSION['error'] = "Số điện thoại phải có 10 chữ số";
                header('location:' . BASE_URL . 'enquiry');
                exit;
            }

            if (strlen($subject) > 100) {
                $_SESSION['error'] = "Tiêu đề không được vượt quá 100 ký tự";
                header('location:' . BASE_URL . 'enquiry');
                exit;
            }

            $enquiryModel = $this->model('EnquiryModel');
            $lastInsertId = $enquiryModel->createEnquiry($fname, $email, $mobile, $subject, $description);

            if ($lastInsertId) {
                $_SESSION['msg'] = "Bạn đã gửi yêu cầu thành công";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra. Vui lòng thử lại";
            }
        }
        header('location:' . BASE_URL . 'enquiry');
        exit;
    }
}
