<?php
/**
 * Create Event Handler
 * 
 * Verwerkt nieuwe event creatie met validatie en security
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

// Functie om input te sanitizen
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

try {
    // Haal form data op
    $title = sanitizeInput($_POST['title'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $eventDate = $_POST['event_date'] ?? '';
    $maxParticipants = (int)($_POST['max_participants'] ?? 0);

    // Validatie
    $errors = [];

    if (empty($title)) {
        $errors[] = "Event titel is verplicht.";
    }

    if (empty($description)) {
        $errors[] = "Event beschrijving is verplicht.";
    }

    if (empty($eventDate)) {
        $errors[] = "Event datum is verplicht.";
    } else {
        // Check of datum in de toekomst ligt
        $eventDateTime = new DateTime($eventDate);
        $now = new DateTime();
        if ($eventDateTime <= $now) {
            $errors[] = "Event datum moet in de toekomst liggen.";
        }
    }

    if ($maxParticipants <= 0) {
        $errors[] = "Maximum aantal deelnemers moet groter dan 0 zijn.";
    }

    // Als er errors zijn, ga terug naar form
    if (!empty($errors)) {
        $_SESSION['event_errors'] = $errors;
        $_SESSION['event_data'] = $_POST; // Behoud form data
        header('Location: ../../public/pages/create_event.php');
        exit();
    }

    // Database connectie
    $pdo = getDBConnection();

    // Insert nieuw event
    $stmt = $pdo->prepare("
        INSERT INTO events (title, description, event_date, max_participants, created_by, created_at) 
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    
    $result = $stmt->execute([$title, $description, $eventDate, $maxParticipants, $_SESSION['user_id']]);

    if ($result) {
        $_SESSION['event_success'] = "Event succesvol aangemaakt!";
        header('Location: ../../public/pages/my_events.php');
        exit();
    } else {
        $_SESSION['event_errors'] = ["Er ging iets mis bij het aanmaken van het event."];
        header('Location: ../../public/pages/create_event.php');
        exit();
    }

} catch (Exception $e) {
    // Log error
    error_log("Create event error: " . $e->getMessage());
    $_SESSION['event_errors'] = ["Er ging iets mis. Probeer het opnieuw."];
    header('Location: ../../public/pages/create_event.php');
    exit();
}
?>