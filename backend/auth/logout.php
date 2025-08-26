<?php
/**
 * User Logout Handler
 * 
 * Vernietig sessie en redirect naar login
 */

require_once '../config/config.php';

// Start sessie als deze nog niet bestaat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vernietig alle sessie data
$_SESSION = array();

// Vernietig sessie cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Vernietig sessie
session_destroy();

// Redirect naar login pagina
header('Location: ../../public/pages/login.php');
exit();
?>