<?php
class IssueController extends Controller {

    /**
     * Lấy email của user hiện tại từ Session hoặc JWT Cookie
     * Trả về chuỗi email nếu đăng nhập, null nếu chưa đăng nhập
     */
    private function getAuthEmail() {
        // Ưu tiên 1: Session (set bởi Cookie Bridge)
        if (!empty($_SESSION['login'])) {
            return $_SESSION['login'];
        }

        // Ưu tiên 2: Giải mã JWT từ Cookie (user đăng nhập bằng JWT)
        if (!empty($_COOKIE['jwt_token'])) {
            try {
                require_once ROOT . '/vendor/autoload.php';
                $decoded = \Firebase\JWT\JWT::decode(
                    $_COOKIE['jwt_token'],
                    new \Firebase\JWT\Key('GoTravel_Secret_Key_2026_Secure!@#', 'HS256')
                );
                if (isset($decoded->data->email)) {
                    return $decoded->data->email;
                }
            } catch (Exception $e) {
                // Token không hợp lệ hoặc hết hạn
            }
        }

        return null;
    }

    public function index() {
        $email = $this->getAuthEmail();
        if (!$email) {
            header('location:' . BASE_URL);
            exit;
        }

        // Trang Issue hiện redirect về home — UI dùng modal write-us.php
        header('location:' . BASE_URL);
        exit;
    }

    public function submit() {
        $email = $this->getAuthEmail();

        if (!$email) {
            // Chưa đăng nhập (cả Session lẫn JWT đều không có) → redirect về trang chủ
            header('location:' . BASE_URL);
            exit;
        }

        if (isset($_POST['submit'])) {
            $issue       = trim($_POST['issue']       ?? '');
            $description = trim($_POST['description'] ?? '');

            // Validate inputs
            if (empty($issue) || empty($description)) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin.";
                header('location:' . BASE_URL);
                exit;
            }

            if (strlen($issue) > 100) {
                $_SESSION['error'] = "Tiêu đề vấn đề không được vượt quá 100 ký tự";
                header('location:' . BASE_URL);
                exit;
            }

            if (strlen($description) > 5000) {
                $_SESSION['error'] = "Mô tả không được vượt quá 5000 ký tự";
                header('location:' . BASE_URL);
                exit;
            }

            $issueModel    = $this->model('IssueModel');
            $lastInsertId  = $issueModel->createIssue($email, $issue, $description);

            if ($lastInsertId) {
                $_SESSION['msg'] = "Yêu cầu hỗ trợ của bạn đã được gửi. Chúng tôi sẽ phản hồi sớm nhất có thể.";
            } else {
                $_SESSION['error'] = "Không thể lưu yêu cầu. Vui lòng thử lại.";
            }
        }

        header('location:' . BASE_URL);
        exit;
    }
}
