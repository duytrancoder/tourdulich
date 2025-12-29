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
$app = new App();

