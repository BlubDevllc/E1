<?php
/**
 * User Login Handler
 * 
 * Verwerkt gebruiker login met security en session management
 */

require_once '../config/config.php';
require_once '../config/conn.php';

// Alleen POST requests accepteren
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/pages/login.php');
    exit();
}

// Functie om input te sanitizen
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

try {
    // Haal form data op
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basis validatie
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Email en wachtwoord zijn verplicht.";
        header('Location: ../../public/pages/login.php');
        exit();
    }

    // Database connectie
    $pdo = getDBConnection();

    // Zoek gebruiker op email
    $stmt = $pdo->prepare("
        SELECT user_id, first_name, last_name, email, password_hash, created_at 
        FROM users 
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Check of gebruiker bestaat en wachtwoord klopt
    if (!$user || !password_verify($password, $user['password_hash'])) {
        $_SESSION['login_error'] = "Ongeldige email of wachtwoord.";
        header('Location: ../../public/pages/login.php');
        exit();
    }

    // Login succesvol - start user sessie
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();

    // Verwijder eventuele error messages
    unset($_SESSION['login_error']);

    // Redirect naar dashboard
    header('Location: ../../index.php');
    exit();

} catch (Exception $e) {
    // Log error
    error_log("Login error: " . $e->getMessage());
    $_SESSION['login_error'] = "Er ging iets mis bij het inloggen. Probeer opnieuw.";
    header('Location: ../../public/pages/login.php');
    exit();
}
?>