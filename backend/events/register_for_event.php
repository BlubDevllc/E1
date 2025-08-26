<?php
/**
 * Register for Event Handler
 * 
 * Verwerkt event registraties met validatie
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
    header('Location: ../../public/pages/events_overview.php');
    exit();
}

try {
    $eventId = (int)($_POST['event_id'] ?? 0);
    $userId = $_SESSION['user_id'];

    if ($eventId <= 0) {
        $_SESSION['event_error'] = "Ongeldig event ID.";
        header('Location: ../../public/pages/events_overview.php');
        exit();
    }

    // Database connectie
    $pdo = getDBConnection();

    // Check of event bestaat en nog niet vol is
    $stmt = $pdo->prepare("
        SELECT e.event_id, e.title, e.max_participants, e.event_date,
               COUNT(r.registration_id) as current_registrations
        FROM events e
        LEFT JOIN registrations r ON e.event_id = r.event_id
        WHERE e.event_id = ?
        GROUP BY e.event_id
    ");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();

    if (!$event) {
        $_SESSION['event_error'] = "Event niet gevonden.";
        header('Location: ../../public/pages/events_overview.php');
        exit();
    }

    // Check of event al voorbij is
    $eventDateTime = new DateTime($event['event_date']);
    $now = new DateTime();
    if ($eventDateTime <= $now) {
        $_SESSION['event_error'] = "Je kunt je niet meer inschrijven voor een event dat al voorbij is.";
        header('Location: ../../public/pages/events_overview.php');
        exit();
    }

    // Check of event vol is
    if ($event['current_registrations'] >= $event['max_participants']) {
        $_SESSION['event_error'] = "Dit event is vol. Je kunt je niet meer inschrijven.";
        header('Location: ../../public/pages/events_overview.php');
        exit();
    }

    // Check of gebruiker al ingeschreven is
    $stmt = $pdo->prepare("
        SELECT registration_id FROM registrations 
        WHERE user_id = ? AND event_id = ?
    ");
    $stmt->execute([$userId, $eventId]);
    
    if ($stmt->fetch()) {
        $_SESSION['event_error'] = "Je bent al ingeschreven voor dit event.";
        header('Location: ../../public/pages/events_overview.php');
        exit();
    }

    // Check of gebruiker eigenaar is van het event
    $stmt = $pdo->prepare("SELECT created_by FROM events WHERE event_id = ?");
    $stmt->execute([$eventId]);
    $eventOwner = $stmt->fetch();
    
    if ($eventOwner['created_by'] == $userId) {
        $_SESSION['event_error'] = "Je kunt je niet inschrijven voor je eigen event.";
        header('Location: ../../public/pages/events_overview.php');
        exit();
    }

    // Registreer gebruiker voor event
    $stmt = $pdo->prepare("
        INSERT INTO registrations (user_id, event_id, registered_at) 
        VALUES (?, ?, NOW())
    ");
    
    $result = $stmt->execute([$userId, $eventId]);

    if ($result) {
        $_SESSION['event_success'] = "Je bent succesvol ingeschreven voor '{$event['title']}'!";
    } else {
        $_SESSION['event_error'] = "Er ging iets mis bij het inschrijven.";
    }

    header('Location: ../../public/pages/events_overview.php');
    exit();

} catch (Exception $e) {
    error_log("Register for event error: " . $e->getMessage());
    $_SESSION['event_error'] = "Er ging iets mis. Probeer het opnieuw.";
    header('Location: ../../public/pages/events_overview.php');
    exit();
}
?>