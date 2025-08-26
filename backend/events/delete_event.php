<?php
/**
 * Delete Event Handler
 * 
 * Verwijdert events (alleen eigenaar kan dit)
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

    // Check of event bestaat en gebruiker eigenaar is
    $stmt = $pdo->prepare("
        SELECT event_id, title, created_by FROM events 
        WHERE event_id = ? AND created_by = ?
    ");
    $stmt->execute([$eventId, $userId]);
    $event = $stmt->fetch();

    if (!$event) {
        $_SESSION['event_error'] = "Event niet gevonden of je hebt geen toestemming om dit event te verwijderen.";
        header('Location: ../../public/pages/my_events.php');
        exit();
    }

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Eerst alle registraties verwijderen
        $stmt = $pdo->prepare("DELETE FROM registrations WHERE event_id = ?");
        $stmt->execute([$eventId]);

        // Dan het event zelf verwijderen
        $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = ?");
        $stmt->execute([$eventId]);

        // Commit transaction
        $pdo->commit();

        $_SESSION['event_success'] = "Event '{$event['title']}' succesvol verwijderd!";

    } catch (Exception $e) {
        // Rollback transaction bij fout
        $pdo->rollback();
        throw $e;
    }

    header('Location: ../../public/pages/my_events.php');
    exit();

} catch (Exception $e) {
    error_log("Delete event error: " . $e->getMessage());
    $_SESSION['event_error'] = "Er ging iets mis bij het verwijderen. Probeer het opnieuw.";
    header('Location: ../../public/pages/my_events.php');
    exit();
}
?>