<?php
namespace Api\Controllers;

use Api\Core\Response;
use Api\Core\JWTHandler;
use Api\Models\User;

class AuthController {
    
    /**
     * POST /api/auth/login
     */
    public function login() {
        // Read JSON payload
        $data = json_decode(file_get_contents("php://input"));
        
        $email = $data->email ?? '';
        $password = $data->password ?? '';

        if (empty($email) || empty($password)) {
            Response::error("Vui lòng nhập đầy đủ thông tin", null, 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error("Email không hợp lệ", null, 400);
        }

        $userModel = new User();
        $user = $userModel->getByEmail($email);

        if ($user && $userModel->verifyPassword($user, $password)) {
            // Generate JWT
            $tokenPayload = [
                'id' => $user['id'],
                'email' => $user['EmailId'],
                'name' => $user['FullName'],
                'role' => 'user'
            ];
            
            $token = JWTHandler::encode($tokenPayload);

            Response::success([
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['FullName'],
                    'email' => $user['EmailId']
                ]
            ], "Đăng nhập thành công!");
        } else {
            Response::error("Email hoặc mật khẩu không chính xác", null, 401);
        }
    }

    /**
     * POST /api/auth/register
     */
    public function register() {
        $data = json_decode(file_get_contents("php://input"));
        
        $fname = trim($data->fname ?? '');
        $mobile = trim($data->mobilenumber ?? '');
        $email = trim($data->email ?? '');
        $password = $data->password ?? '';

        // Validation
        $errors = [];
        if (empty($fname)) $errors['fname'] = "Họ tên không được để trống";
        if (empty($mobile) || !preg_match('/^[0-9]{10}$/', $mobile)) $errors['mobilenumber'] = "Số điện thoại phải có 10 chữ số";
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = "Email không hợp lệ";
        if (empty($password) || strlen($password) < 6) $errors['password'] = "Mật khẩu phải từ 6 ký tự trở lên";

        if (!empty($errors)) {
            Response::error("Lỗi dữ liệu", $errors, 400);
        }

        $userModel = new User();
        
        // Check if email exists
        if ($userModel->getByEmail($email)) {
            Response::error("Email này đã được sử dụng", null, 409); // Conflict
        }

        $userId = $userModel->create($fname, $mobile, $email, $password);

        if ($userId) {
            Response::success(['id' => $userId], "Đăng ký thành công! Bạn có thể đăng nhập ngay.", 201);
        } else {
            Response::error("Có lỗi xảy ra khi tạo tài khoản", null, 500);
        }
    }

    /**
     * POST /api/auth/forgot-password — Public
     * Kiểm tra email tồn tại và giả lập gửi mail reset
     */
    public function forgotPassword() {
        $data  = json_decode(file_get_contents('php://input'), true);
        $email = trim($data['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Email không hợp lệ', null, 400);
        }

        try {
            $db   = \Api\Core\Database::getConnection();
            $stmt = $db->prepare('SELECT id, FullName FROM tblusers WHERE EmailId = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Luôn trả về thành công để tránh lộ thông tin email có tồn tại hay không (security best practice)
            // Nếu user tồn tại: ghi nhận yêu cầu (có thể mở rộng gửi email thực sau)
            if ($user) {
                // TODO Phase 6: Gửi email reset thực sự qua SMTP/Mailgun
                // Tạm thời: ghi log hoặc reset về mật khẩu mặc định theo yêu cầu nghiệp vụ
            }

            Response::success(
                null,
                'Nếu email này tồn tại trong hệ thống, bạn sẽ nhận được hướng dẫn khôi phục mật khẩu trong vài phút.'
            );
        } catch (\Exception $e) {
            Response::error('Có lỗi xảy ra, vui lòng thử lại', null, 500);
        }
    }

    /**
     * POST /api/auth/check-availability
     * Kiểm tra email đã tồn tại hay chưa
     */
    public function checkAvailability() {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = trim($data['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::error('Email không hợp lệ', null, 400);
        }

        try {
            $userModel = new \Api\Models\User();
            $user = $userModel->getByEmail($email);

            if ($user) {
                Response::success(['available' => false], 'Email đã được sử dụng.', 200);
            } else {
                Response::success(['available' => true], 'Email có thể sử dụng.', 200);
            }
        } catch (\Exception $e) {
            Response::error('Lỗi kiểm tra dữ liệu', null, 500);
        }
    }

    /**
     * POST /api/auth/admin-login — Public
     * Đăng nhập dành cho Quản trị viên
     */
    public function adminLogin() {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($username) || empty($password)) {
            Response::error("Vui lòng nhập đầy đủ thông tin", null, 400);
        }

        try {
            $db = \Api\Core\Database::getConnection();
            $stmt = $db->prepare("SELECT id, UserName, Password FROM admin WHERE UserName = ? LIMIT 1");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin) {
                $passwordValid = false;
                // Support MD5 legacy
                if (strlen($admin['Password']) === 32 && ctype_xdigit($admin['Password'])) {
                    if ($admin['Password'] === md5($password)) {
                        $passwordValid = true;
                        // Upgrade to password_hash
                        $newHash = password_hash($password, PASSWORD_DEFAULT);
                        $updateStmt = $db->prepare("UPDATE admin SET Password = ? WHERE id = ?");
                        $updateStmt->execute([$newHash, $admin['id']]);
                    }
                } else {
                    $passwordValid = password_verify($password, $admin['Password']);
                }

                if ($passwordValid) {
                    $tokenPayload = [
                        'id' => $admin['id'],
                        'email' => $admin['UserName'],
                        'name' => 'Administrator',
                        'role' => 'admin'
                    ];
                    $token = JWTHandler::encode($tokenPayload);

                    Response::success([
                        'token' => $token,
                        'user' => [
                            'id' => $admin['id'],
                            'name' => 'Administrator',
                            'username' => $admin['UserName']
                        ]
                    ], "Đăng nhập Admin thành công!");
                } else {
                    Response::error("Mật khẩu không chính xác", null, 401);
                }
            } else {
                Response::error("Tài khoản không tồn tại", null, 404);
            }
        } catch (\Exception $e) {
            Response::error("Có lỗi xảy ra khi đăng nhập", null, 500);
        }
    }
}
