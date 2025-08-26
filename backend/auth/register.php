<?php
/**
 * User Registration Handler
 * 
 * Verwerkt nieuwe gebruiker registraties met validatie en security
 */

require_once '../config/config.php';
require_once '../config/conn.php';

// Alleen POST requests accepteren
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/pages/register.php');
    exit();
}

// Functie om input te sanitizen
function sanitizeInput($data) {
    $data = trim($data);           // Verwijder whitespace
    $data = stripslashes($data);   // Verwijder backslashes
    $data = htmlspecialchars($data); // Converteer special chars
    return $data;
}

// Functie om wachtwoord te valideren
function validatePassword($password) {
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return "Wachtwoord moet minimaal " . PASSWORD_MIN_LENGTH . " karakters lang zijn.";
    }
    return true;
}

// Functie om email te valideren
function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Ongeldig email adres.";
    }
    return true;
}

try {
    // Haal form data op en sanitize
    $firstName = sanitizeInput($_POST['first_name'] ?? '');
    $lastName = sanitizeInput($_POST['last_name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    // Validatie
    $errors = [];

    if (empty($firstName)) {
        $errors[] = "Voornaam is verplicht.";
    }

    if (empty($lastName)) {
        $errors[] = "Achternaam is verplicht.";
    }

    if (empty($email)) {
        $errors[] = "Email is verplicht.";
    } else {
        $emailValidation = validateEmail($email);
        if ($emailValidation !== true) {
            $errors[] = $emailValidation;
        }
    }

    if (empty($password)) {
        $errors[] = "Wachtwoord is verplicht.";
    } else {
        $passwordValidation = validatePassword($password);
        if ($passwordValidation !== true) {
            $errors[] = $passwordValidation;
        }
    }

    if ($password !== $passwordConfirm) {
        $errors[] = "Wachtwoorden komen niet overeen.";
    }

    // Als er errors zijn, ga terug naar form
    if (!empty($errors)) {
        $_SESSION['register_errors'] = $errors;
        $_SESSION['register_data'] = $_POST; // Behoud form data
        header('Location: ../../public/pages/register.php');
        exit();
    }

    // Database connectie
    $pdo = getDBConnection();

    // Check of email al bestaat
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        $_SESSION['register_errors'] = ["Dit email adres is al in gebruik."];
        $_SESSION['register_data'] = $_POST;
        header('Location: ../../public/pages/register.php');
        exit();
    }

    // Hash wachtwoord (SECURE!)
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert nieuwe gebruiker
    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, password_hash, created_at) 
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    $result = $stmt->execute([$firstName, $lastName, $email, $passwordHash]);

    if ($result) {
        // Registratie succesvol
        $_SESSION['register_success'] = "Account succesvol aangemaakt! Je kunt nu inloggen.";
        header('Location: ../../public/pages/login.php');
        exit();
    } else {
        throw new Exception("Er ging iets mis bij het aanmaken van je account.");
    }

} catch (Exception $e) {
    // Log error en toon generieke melding
    error_log("Registration error: " . $e->getMessage());
    $_SESSION['register_errors'] = ["Er ging iets mis. Probeer het opnieuw."];
    header('Location: ../../public/pages/register.php');
    exit();
}
?>