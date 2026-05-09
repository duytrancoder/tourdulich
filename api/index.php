<?php
// Set CORS headers required for REST API
header("Access-Control-Allow-Origin: *"); // For production, replace * with your domain
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight requests (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Require Composer Autoloader
$composerAutoload = dirname(__DIR__) . '/vendor/autoload.php';
if (!file_exists($composerAutoload)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Composer autoload not found. Please run `composer require firebase/php-jwt`.'
    ]);
    exit;
}
require_once $composerAutoload;

// Simple internal autoloader for our Api classes
spl_autoload_register(function ($class_name) {
    // Check if the class is part of our Api namespace
    if (strpos($class_name, 'Api\\') === 0) {
        $relative_class = substr($class_name, 4); // Remove 'Api\'
        $file = __DIR__ . '/' . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});

use Api\Core\Router;
use Api\Core\Response;
use Api\Core\JWTHandler;

// Initialize Router
$router = new Router();

// Define API Routes
$router->get('/api/ping', function() {
    Response::success(['status' => 'API is running'], "Pong");
});

$router->get('/api/secure-ping', function() {
    // This will throw 401 if token is missing or invalid
    $user = JWTHandler::verifyBearerToken();
    
    Response::success([
        'user' => $user,
        'message' => 'You are authenticated!'
    ], "Secure Pong");
});

// Auth Routes
$router->post('/api/auth/login', 'AuthController@login');
$router->post('/api/auth/register', 'AuthController@register');

// Dispatch the request
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// Clean base path from URI if necessary (depends on your server config)
// If URI is like /tour1/api/ping, we want to strip the /tour1 part for the router
$basePath = '/tour1';
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

$router->dispatch($requestMethod, $requestUri);
