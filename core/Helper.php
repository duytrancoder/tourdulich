<?php
/**
 * Helper Class - Utility functions for the application
 * Provides security, validation, and formatting helpers
 */
class Helper {
    
    /**
     * Generate CSRF Token
     * @return string
     */
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF Token
     * @param string $token
     * @return bool
     */
    public static function verifyCSRFToken($token) {
        if (empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Get CSRF Token HTML Input
     * @return string
     */
    public static function csrfField() {
        $token = self::generateCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Sanitize string input
     * @param string $input
     * @return string
     */
    public static function sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Validate email
     * @param string $email
     * @return bool
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate phone number (10 digits)
     * @param string $phone
     * @return bool
     */
    public static function validatePhone($phone) {
        return preg_match('/^[0-9]{10}$/', $phone);
    }
    
    /**
     * Format price in VND
     * @param int|float $price
     * @return string
     */
    public static function formatVND($price) {
        return number_format($price, 0, ',', '.') . ' đ';
    }
    
    /**
     * Validate and sanitize filename
     * @param string $filename
     * @return string
     */
    public static function sanitizeFilename($filename) {
        $filename = basename($filename);
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        return $filename;
    }
    
    /**
     * Validate image file
     * @param array $file $_FILES array element
     * @param int $maxSize Maximum file size in bytes (default 5MB)
     * @return array ['valid' => bool, 'error' => string]
     */
    public static function validateImage($file, $maxSize = 5242880) {
        $result = ['valid' => false, 'error' => ''];
        
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $result['error'] = 'Vui lòng chọn hình ảnh';
            return $result;
        }
        
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $file['type'];
        
        // Verify MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($fileType, $allowedTypes) || !in_array($mimeType, $allowedTypes)) {
            $result['error'] = 'Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WEBP)';
            return $result;
        }
        
        if ($file['size'] > $maxSize) {
            $result['error'] = 'Kích thước file không được vượt quá ' . ($maxSize / 1024 / 1024) . 'MB';
            return $result;
        }
        
        // Verify it's actually an image
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $result['error'] = 'File không phải là hình ảnh hợp lệ';
            return $result;
        }
        
        $result['valid'] = true;
        return $result;
    }
    
    /**
     * Redirect with flash message
     * @param string $url
     * @param string $message
     * @param string $type 'error' or 'msg'
     */
    public static function redirect($url, $message = '', $type = 'msg') {
        if ($message) {
            $_SESSION[$type] = $message;
        }
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Check if user is logged in
     * @return bool
     */
    public static function isLoggedIn() {
        return !empty($_SESSION['login']);
    }
    
    /**
     * Check if admin is logged in
     * @return bool
     */
    public static function isAdminLoggedIn() {
        return !empty($_SESSION['alogin']);
    }
    
    /**
     * Require user login
     * @param string $redirectUrl
     */
    public static function requireLogin($redirectUrl = '') {
        if (!self::isLoggedIn()) {
            $url = $redirectUrl ?: BASE_URL;
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục';
            header('Location: ' . $url);
            exit;
        }
    }
    
    /**
     * Require admin login
     * @param string $redirectUrl
     */
    public static function requireAdminLogin($redirectUrl = 'index.php') {
        if (!self::isAdminLoggedIn()) {
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
    
    /**
     * Validate date format and value
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * Escape output for HTML
     * @param string $string
     * @return string
     */
    public static function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Generate random string
     * @param int $length
     * @return string
     */
    public static function randomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
}
