<?php
session_start(); 
include('includes/config.php');
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 60*60,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
unset($_SESSION['alogin']);
session_destroy(); 

// Clear JWT Cookie
setcookie('admin_jwt_token', '', time() - 3600, '/');

header("location:" . BASE_URL . "admin/index.php"); 
?>

