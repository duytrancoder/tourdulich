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
}
