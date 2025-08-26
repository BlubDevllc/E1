<?php
/**
 * Unregister from Event Handler
 * 
 * Verwijdert gebruiker registratie van een event
 */

require_once '../config/config.php';
require_once '../config/conn.php';

// Check of gebruiker ingelogd is
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../public/pages/login.php');
    exit();
}

// Alleen POST requests accepteren
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/pages/my_events.php');
    exit();
}

try {
    $eventId = (int)($_POST['event_id'] ?? 0);
    $userId = $_SESSION['user_id'];

    if ($eventId <= 0) {
        $_SESSION['event_error'] = "Ongeldig event ID.";
        header('Location: ../../public/pages/my_events.php');
        exit();
    }

    // Database connectie
    $pdo = getDBConnection();

    // Check of registratie bestaat
    $stmt = $pdo->prepare("
        SELECT r.registration_id, e.title, e.event_date
        FROM registrations r
        JOIN events e ON r.event_id = e.event_id
        WHERE r.user_id = ? AND r.event_id = ?
    ");
    $stmt->execute([$userId, $eventId]);
    $registration = $stmt->fetch();

    if (!$registration) {
        $_SESSION['event_error'] = "Je bent niet ingeschreven voor dit event.";
        header('Location: ../../public/pages/my_events.php');
        exit();
    }

    // Check of event al voorbij is
    $eventDateTime = new DateTime($registration['event_date']);
    $now = new DateTime();
    if ($eventDateTime <= $now) {
        $_SESSION['event_error'] = "Je kunt je niet meer uitschrijven voor een event dat al voorbij is.";
        header('Location: ../../public/pages/my_events.php');
        exit();
    }

    // Verwijder registratie
    $stmt = $pdo->prepare("DELETE FROM registrations WHERE registration_id = ?");
    $result = $stmt->execute([$registration['registration_id']]);

    if ($result) {
        $_SESSION['event_success'] = "Je bent succesvol uitgeschreven voor '{$registration['title']}'!";
    } else {
        $_SESSION['event_error'] = "Er ging iets mis bij het uitschrijven.";
    }

    header('Location: ../../public/pages/my_events.php');
    exit();

} catch (Exception $e) {
    error_log("Unregister from event error: " . $e->getMessage());
    $_SESSION['event_error'] = "Er ging iets mis. Probeer het opnieuw.";
    header('Location: ../../public/pages/my_events.php');
    exit();
}
?>
