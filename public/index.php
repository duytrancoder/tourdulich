<?php
session_start();

define("ROOT", dirname(__DIR__));
define("APP", ROOT . "/app");
define("BASE_URL", "http://" . $_SERVER["HTTP_HOST"] . "/");
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

