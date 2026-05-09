<?php
session_start();

define("ROOT", dirname(__DIR__));
define("APP", ROOT . "/app");

// Base URL with subdirectory support
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];

// Calculate base path - get the directory containing the project (one level up from public)
// SCRIPT_NAME will be something like /tour1/public/index.php, we want /tour1
$scriptPath = $_SERVER['SCRIPT_NAME'];
$basePath = dirname(dirname($scriptPath));

// Clean up the path
$basePath = rtrim(str_replace('\\', '/', $basePath), '/');

// Ensure it starts with /
if ($basePath === '' || $basePath === '.') {
    $basePath = '';
} elseif ($basePath[0] !== '/') {
    $basePath = '/' . $basePath;
}

define("BASE_URL", $protocol . '://' . $host . $basePath . '/');

// Autoload Helper class
require_once ROOT . "/core/Helper.php";

spl_autoload_register(function ($className) {
    $corePath = ROOT . "/core/" . str_replace("\\", "/", $className) . ".php";
    if (file_exists($corePath)) {
        require_once $corePath;
        return;
    }

    $controllerPath =
        APP . "/controllers/" . str_replace("\\", "/", $className) . ".php";
    if (file_exists($controllerPath)) {
        require_once $controllerPath;
        return;
    }

    $modelPath = APP . "/models/" . str_replace("\\", "/", $className) . ".php";
    if (file_exists($modelPath)) {
        require_once $modelPath;
    }
});

require_once ROOT . "/core/App.php";

// JWT Bridge: Khôi phục $_SESSION từ JWT Cookie cho các trang PHP cũ
if (isset($_COOKIE['jwt_token']) && empty($_SESSION['login'])) {
    try {
        require_once ROOT . '/vendor/autoload.php';
        $decoded = \Firebase\JWT\JWT::decode($_COOKIE['jwt_token'], new \Firebase\JWT\Key('GoTravel_Secret_Key_2026_Secure!@#', 'HS256'));
        if (isset($decoded->data->email)) {
            $_SESSION['login'] = $decoded->data->email;
        }
    } catch (Exception $e) {
        // Token invalid
    }
} else if (!isset($_COOKIE['jwt_token']) && !empty($_SESSION['login'])) {
    unset($_SESSION['login']);
}

$app = new App();

