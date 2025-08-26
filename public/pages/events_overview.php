<?php 
// Check login status
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = "Evenementen Overzicht"; 
$isLoggedIn = isset($_SESSION['user_id']);

// Debug: check login status
error_log("Events Overview - Is logged in: " . ($isLoggedIn ? 'YES' : 'NO'));
if ($isLoggedIn) {
    error_log("User ID: " . $_SESSION['user_id']);
}

// Haal events op uit database
$events = [];
// Tijdelijk: haal altijd events op, ongeacht login status
require_once '../../backend/config/conn.php';

try {
    $pdo = getDBConnection();
    
    // Debug: check totaal aantal events in database
    $debugStmt = $pdo->prepare("SELECT COUNT(*) as total_events FROM events");
    $debugStmt->execute();
    $totalEvents = $debugStmt->fetchColumn();
    error_log("Total events in database: $totalEvents");
    
    if ($isLoggedIn) {
        $userId = $_SESSION['user_id'];
        
        // Haal alle toekomstige events op (behalve die van de gebruiker zelf)
        $stmt = $pdo->prepare("
            SELECT e.event_id, e.title, e.description, e.event_date, e.max_participants,
                   u.first_name, u.last_name,
                   COUNT(r.registration_id) as current_registrations,
                   CASE WHEN ur.user_id IS NOT NULL THEN 1 ELSE 0 END as user_registered
            FROM events e
            JOIN users u ON e.created_by = u.user_id
            LEFT JOIN registrations r ON e.event_id = r.event_id
            LEFT JOIN registrations ur ON e.event_id = ur.event_id AND ur.user_id = ?
            WHERE e.created_by != ?
            GROUP BY e.event_id, e.title, e.description, e.event_date, e.max_participants, u.first_name, u.last_name, ur.user_id
            ORDER BY e.event_date ASC
        ");
        $stmt->execute([$userId, $userId]);
        $events = $stmt->fetchAll();
        
        // Debug informatie (tijdelijk)
        error_log("Events query executed. User ID: $userId");
        error_log("Number of events found: " . count($events));
        if (empty($events)) {
            error_log("No events found. Query parameters: [$userId, $userId]");
        }
    } else {
        // Voor niet-ingelogde gebruikers: toon alle events (tijdelijk voor debug)
        $stmt = $pdo->prepare("
            SELECT e.event_id, e.title, e.description, e.event_date, e.max_participants,
                   u.first_name, u.last_name,
                   COUNT(r.registration_id) as current_registrations,
                   0 as user_registered
            FROM events e
            JOIN users u ON e.created_by = u.user_id
            LEFT JOIN registrations r ON e.event_id = r.event_id
            GROUP BY e.event_id, e.title, e.description, e.event_date, e.max_participants, u.first_name, u.last_name
            ORDER BY e.event_date ASC
        ");
        $stmt->execute();
        $events = $stmt->fetchAll();
        error_log("Not logged in - showing all events: " . count($events));
    }
    
} catch (Exception $e) {
    error_log("Events overview error: " . $e->getMessage());
    $events = [];
}
?>
<?php include '../includes/head.php'; ?>
<?php include '../includes/header.php'; ?>

      <div class="content-area">
        <div class="events-overview">
          <div class="page-header">
            <h2>Alle Evenementen</h2>
            <?php if ($isLoggedIn): ?>
              <a href="create_event.php" class="btn-primary">
                <i class="fas fa-plus"></i> Nieuw Evenement
              </a>
            <?php endif; ?>
          </div>

          <?php if (isset($_SESSION['event_success'])): ?>
            <div class="alert alert-success">
              <?php echo htmlspecialchars($_SESSION['event_success']); ?>
              <?php unset($_SESSION['event_success']); ?>
            </div>
          <?php endif; ?>

          <?php if (isset($_SESSION['event_error'])): ?>
            <div class="alert alert-error">
              <?php echo htmlspecialchars($_SESSION['event_error']); ?>
              <?php unset($_SESSION['event_error']); ?>
            </div>
          <?php endif; ?>
          
          <div class="events-list">
            <!-- Debug informatie (tijdelijk) -->
            <div style="background: #f0f0f0; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
              <strong>Debug Info:</strong><br>
              Ingelogd: <?php echo $isLoggedIn ? 'JA' : 'NEE'; ?><br>
              <?php if ($isLoggedIn): ?>
                User ID: <?php echo $_SESSION['user_id']; ?><br>
              <?php endif; ?>
              Aantal events gevonden: <?php echo count($events); ?><br>
            </div>
            
            <?php if (!empty($events)): ?>
              <?php foreach ($events as $event): ?>
                <div class="event-card">
                  <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                  <p><?php echo htmlspecialchars($event['description']); ?></p>
                  <div class="event-details">
                    <span class="event-date">
                      <i class="fas fa-calendar"></i> 
                      <?php echo date('d F Y H:i', strtotime($event['event_date'])); ?>
                    </span>
                    <span class="event-participants">
                      <i class="fas fa-users"></i> 
                      <?php echo $event['current_registrations']; ?>/<?php echo $event['max_participants']; ?> deelnemers
                    </span>
                    <span class="event-organizer">
                      <i class="fas fa-user"></i> 
                      <?php echo htmlspecialchars($event['first_name'] . ' ' . $event['last_name']); ?>
                    </span>
                  </div>
                  <div class="event-actions">
                    <?php if ($event['user_registered']): ?>
                      <span class="btn-success disabled">
                        <i class="fas fa-check"></i> Ingeschreven
                      </span>
                    <?php elseif ($event['current_registrations'] >= $event['max_participants']): ?>
                      <span class="btn-danger disabled">
                        <i class="fas fa-times"></i> Vol
                      </span>
                    <?php else: ?>
                      <form method="post" action="../../backend/events/register_for_event.php" style="display: inline;">
                        <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                        <button type="submit" class="btn-primary">
                          <i class="fas fa-user-plus"></i> Inschrijven
                        </button>
                      </form>
                    <?php endif; ?>
                    <button class="btn-secondary">
                      <i class="fas fa-info-circle"></i> Details
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php elseif (empty($events)): ?>
              <div class="no-events">
                <i class="fas fa-calendar-times"></i>
                <h3>Geen evenementen gevonden</h3>
                <p>Er zijn momenteel geen evenementen beschikbaar.</p>
                <?php if ($isLoggedIn): ?>
                  <a href="create_event.php" class="btn-primary">
                    <i class="fas fa-plus"></i> Maak het eerste evenement
                  </a>
                <?php else: ?>
                  <a href="login.php" class="btn-primary">Inloggen om evenementen te maken</a>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>
<?php include '../includes/footer.php'; ?>
