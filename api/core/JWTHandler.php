<?php
namespace Api\Core;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTHandler {
    // Secret key for encoding/decoding JWT (SHOULD BE IN .env IN PRODUCTION)
    private static $secret_key = 'GoTravel_Secret_Key_2026_Secure!@#';
    
    // JWT issuer
    private static $issuer = 'http://localhost/tour1';

    /**
     * Generate a new JWT token
     * 
     * @param array $data Payload data to store in JWT
     * @param int $expireInSeconds Time to live (default 24 hours)
     * @return string JWT Token
     */
    public static function encode($data, $expireInSeconds = 86400) {
        $issuedAt = time();
        $expire = $issuedAt + $expireInSeconds;

        $payload = [
            'iss' => self::$issuer,
            'iat' => $issuedAt,
            'exp' => $expire,
            'data' => $data
        ];

        return JWT::encode($payload, self::$secret_key, 'HS256');
    }

    /**
     * Decode and verify a JWT token
     * 
     * @param string $token JWT string
     * @return object Payload data or throws Exception if invalid
     */
    public static function decode($token) {
        try {
            $decoded = JWT::decode($token, new Key(self::$secret_key, 'HS256'));
            return $decoded->data;
        } catch (Exception $e) {
            throw new Exception("Invalid or expired token");
        }
    }

    /**
     * Middleware to verify Bearer token from Request Headers
     * Supports multiple environments: Apache module, CGI, FastCGI, XAMPP
     * 
     * @return object Decoded user data
     */
    public static function verifyBearerToken() {
        $authHeader = null;

        // Method 1: apache_request_headers() — works on Apache mod_php
        if (function_exists('apache_request_headers')) {
            $apacheHeaders = apache_request_headers();
            if (isset($apacheHeaders['Authorization'])) {
                $authHeader = $apacheHeaders['Authorization'];
            } elseif (isset($apacheHeaders['authorization'])) {
                $authHeader = $apacheHeaders['authorization'];
            }
        }

        // Method 2: $_SERVER superglobals — works on Apache CGI/FastCGI (XAMPP default)
        if (!$authHeader) {
            if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
                $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
            } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                // When mod_rewrite is used, Apache sometimes renames the header
                $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            }
        }

        // Method 3: getallheaders() — PHP 7+ built-in fallback
        if (!$authHeader && function_exists('getallheaders')) {
            $allHeaders = getallheaders();
            if (isset($allHeaders['Authorization'])) {
                $authHeader = $allHeaders['Authorization'];
            } elseif (isset($allHeaders['authorization'])) {
                $authHeader = $allHeaders['authorization'];
            }
        }

        if (!$authHeader) {
            Response::error("Authorization header is missing", null, 401);
        }

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            Response::error("Invalid Authorization header format", null, 401);
        }

        $token = $matches[1];

        try {
            $userData = self::decode($token);
            return $userData;
        } catch (Exception $e) {
            Response::error($e->getMessage(), null, 401);
        }
    }

}
