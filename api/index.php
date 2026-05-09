<?php
session_start();

// Suppress PHP errors/warnings from corrupting JSON output
// Errors will be caught via try/catch in controllers instead
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

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
$router->post('/api/auth/forgot-password', 'AuthController@forgotPassword');
$router->post('/api/auth/check-availability', 'AuthController@checkAvailability');
$router->post('/api/auth/admin-login', 'AuthController@adminLogin');

// Public Enquiry Route (không cần JWT)
$router->post('/api/enquiries', 'PublicEnquiryController@submit');

// User Issue Routes (JWT bắt buộc)
$router->post('/api/user/issues', 'UserIssueController@submit');

// Admin Routes (Protected by JWT - Admin role)
$router->get('/api/admin/users', 'AdminUserController@index');
$router->delete('/api/admin/users/{id}', 'AdminUserController@delete');
$router->get('/api/admin/pages/{id}', 'AdminPageController@show');
$router->put('/api/admin/pages/{id}', 'AdminPageController@update');

// Admin Booking Routes
$router->get('/api/admin/bookings', 'AdminBookingController@index');
$router->get('/api/admin/bookings/{id}', 'AdminBookingController@show');
$router->put('/api/admin/bookings/{id}', 'AdminBookingController@update');

// Admin Issue Routes
$router->get('/api/admin/issues', 'AdminIssueController@index');
$router->put('/api/admin/issues/{id}', 'AdminIssueController@update');

// Admin Tour Routes (Protected by JWT)
$router->get('/api/admin/tours', 'AdminTourController@index');
$router->get('/api/admin/tours/{id}', 'AdminTourController@show');
$router->post('/api/admin/tours', 'AdminTourController@create');
$router->post('/api/admin/tours/{id}', 'AdminTourController@update'); // Handle update via POST for multipart support
$router->delete('/api/admin/tours/{id}', 'AdminTourController@delete');

// Alias for 'packages' to match user request
$router->get('/api/admin/packages/{id}', 'AdminTourController@show');
$router->post('/api/admin/packages/{id}', 'AdminTourController@update');

// User Account Routes (Protected by JWT)
$router->get('/api/user/account', 'UserAccountController@index');
$router->put('/api/user/profile', 'UserAccountController@updateProfile');
$router->put('/api/user/password', 'UserAccountController@updatePassword');
$router->get('/api/user/wishlist', 'UserWishlistController@getIds');
$router->post('/api/user/wishlist/toggle/{id}', 'UserWishlistController@toggle');
$router->post('/api/user/booking', 'UserBookingController@book');
$router->delete('/api/user/booking/{id}', 'UserBookingController@cancel');

// Review Routes
$router->post('/api/user/review', 'UserReviewController@submit');         // Protected (JWT required)
$router->get('/api/reviews/{id}', 'UserReviewController@getByPackage');   // Public

// Dispatch the request
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// Method Spoofing for PUT/DELETE with multipart/form-data
if ($requestMethod === 'POST' && isset($_POST['_method'])) {
    $requestMethod = strtoupper($_POST['_method']);
}

// Clean base path from URI if necessary (depends on your server config)
// If URI is like /tour1/api/ping, we want to strip the /tour1 part for the router
$basePath = '/tour1';
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}

$router->dispatch($requestMethod, $requestUri);
