<?php 
// Check of gebruiker ingelogd is
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pageTitle = "Mijn Evenementen"; 
$userId = $_SESSION['user_id'];

// Haal gebruiker's events op uit database
$myEvents = [];
$myRegistrations = [];

require_once '../../backend/config/conn.php';

try {
    $pdo = getDBConnection();
    
    // Haal events op die gebruiker heeft gemaakt
    $stmt = $pdo->prepare("
        SELECT e.event_id, e.title, e.description, e.event_date, e.max_participants, e.created_at,
               COUNT(r.registration_id) as current_registrations
        FROM events e
        LEFT JOIN registrations r ON e.event_id = r.event_id
        WHERE e.created_by = ?
        GROUP BY e.event_id, e.title, e.description, e.event_date, e.max_participants, e.created_at
        ORDER BY e.event_date ASC
    ");
    $stmt->execute([$userId]);
    $myEvents = $stmt->fetchAll();
    
    // Debug informatie (tijdelijk)
    error_log("My Events query executed. User ID: $userId");
    error_log("Number of my events found: " . count($myEvents));
    
    // Haal events op waar gebruiker voor ingeschreven is
    $stmt = $pdo->prepare("
        SELECT e.event_id, e.title, e.description, e.event_date, e.max_participants,
               u.first_name, u.last_name, r.registered_at,
               COUNT(r2.registration_id) as current_registrations
        FROM registrations r
        JOIN events e ON r.event_id = e.event_id
        JOIN users u ON e.created_by = u.user_id
        LEFT JOIN registrations r2 ON e.event_id = r2.event_id
        WHERE r.user_id = ?
        GROUP BY e.event_id, e.title, e.description, e.event_date, e.max_participants, u.first_name, u.last_name, r.registered_at
        ORDER BY e.event_date ASC
    ");
    $stmt->execute([$userId]);
    $myRegistrations = $stmt->fetchAll();
    
    // Debug informatie (tijdelijk)
    error_log("My Registrations query executed. User ID: $userId");
    error_log("Number of my registrations found: " . count($myRegistrations));
    
} catch (Exception $e) {
    error_log("My events error: " . $e->getMessage());
    $myEvents = [];
    $myRegistrations = [];
}
?>
<?php include '../includes/head.php'; ?>
<?php include '../includes/header.php'; ?>

      <div class="content-area">
        <div class="my-events-overview">
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

          <div class="page-header">
            <h2>Mijn Georganiseerde Events</h2>
            <a href="create_event.php" class="btn-primary">
              <i class="fas fa-plus"></i> Nieuw Event Maken
            </a>
          </div>
          
          <div class="events-list">
            <?php if (!empty($myEvents)): ?>
              <?php foreach ($myEvents as $event): ?>
                <div class="event-card my-event">
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
                    <span class="event-status">
                      <i class="fas fa-check-circle"></i> 
                      <?php echo (strtotime($event['event_date']) > time()) ? 'Actief' : 'Afgelopen'; ?>
                    </span>
                  </div>
                  <div class="event-actions">
                    <button class="btn-secondary">
                      <i class="fas fa-edit"></i> Bewerken
                    </button>
                    <button class="btn-success">
                      <i class="fas fa-users"></i> Deelnemers (<?php echo $event['current_registrations']; ?>)
                    </button>
                    <form method="post" action="../../backend/events/delete_event.php" style="display: inline;" 
                          onsubmit="return confirm('Weet je zeker dat je dit evenement wilt verwijderen?');">
                      <input type="hidden" name="event_id" value="<?php echo $event['event_id']; ?>">
                      <button type="submit" class="btn-danger">
                        <i class="fas fa-trash"></i> Verwijderen
                      </button>
                    </form>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="no-events">
                <i class="fas fa-calendar-plus"></i>
                <h3>Je hebt nog geen evenementen gemaakt</h3>
                <p>Maak je eerste evenement en begin met het organiseren!</p>
                <a href="create_event.php" class="btn-primary">
                  <i class="fas fa-plus"></i> Eerste Event Maken
                </a>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="page-header">
            <h2>Events waar ik ben ingeschreven</h2>
          </div>
          
          <div class="events-list">
            <?php if (!empty($myRegistrations)): ?>
              <?php foreach ($myRegistrations as $registration): ?>
                <div class="event-card">
                  <h3><?php echo htmlspecialchars($registration['title']); ?></h3>
                  <p><?php echo htmlspecialchars($registration['description']); ?></p>
                  <div class="event-details">
                    <span class="event-date">
                      <i class="fas fa-calendar"></i> 
                      <?php echo date('d F Y H:i', strtotime($registration['event_date'])); ?>
                    </span>
                    <span class="event-participants">
                      <i class="fas fa-users"></i> 
                      <?php echo $registration['current_registrations']; ?>/<?php echo $registration['max_participants']; ?> deelnemers
                    </span>
                    <span class="event-organizer">
                      <i class="fas fa-user"></i> 
                      Organisator: <?php echo htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']); ?>
                    </span>
                  </div>
                  <div class="event-actions">
                    <form method="post" action="../../backend/events/unregister_from_event.php" style="display: inline;"
                          onsubmit="return confirm('Weet je zeker dat je je wilt uitschrijven?');">
                      <input type="hidden" name="event_id" value="<?php echo $registration['event_id']; ?>">
                      <button type="submit" class="btn-danger">
                        <i class="fas fa-user-minus"></i> Uitschrijven
                      </button>
                    </form>
                    <button class="btn-secondary">
                      <i class="fas fa-info-circle"></i> Details
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="no-events">
                <i class="fas fa-calendar-check"></i>
                <h3>Je bent nog nergens voor ingeschreven</h3>
                <p>Bekijk de beschikbare evenementen en schrijf je in!</p>
                <a href="events_overview.php" class="btn-primary">
                  <i class="fas fa-search"></i> Evenementen Bekijken
                </a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </div>
<?php include '../includes/footer.php'; ?>