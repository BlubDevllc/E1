<?php 
// Start sessie als nog niet gestart
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = "Dashboard"; 

// Check of gebruiker ingelogd is voor dynamische content
$isLoggedIn = isset($_SESSION['user_id']);
$userStats = null;

if ($isLoggedIn) {
    require_once 'backend/config/conn.php';
    
    try {
        $pdo = getDBConnection();
        $userId = $_SESSION['user_id'];
        
        // Haal statistieken op voor ingelogde gebruiker
        
        // Aantal events die gebruiker heeft gemaakt
        $stmt = $pdo->prepare("SELECT COUNT(*) as my_events FROM events WHERE created_by = ?");
        $stmt->execute([$userId]);
        $myEventsCount = $stmt->fetchColumn();
        
        // Aantal registraties voor gebruiker's events
        $stmt = $pdo->prepare("
            SELECT COUNT(r.registration_id) as total_registrations 
            FROM registrations r 
            JOIN events e ON r.event_id = e.event_id 
            WHERE e.created_by = ?
        ");
        $stmt->execute([$userId]);
        $totalRegistrations = $stmt->fetchColumn();
        
        // Aantal events deze week waar gebruiker voor ingeschreven is of heeft gemaakt
        $stmt = $pdo->prepare("
            SELECT COUNT(DISTINCT e.event_id) as this_week_events
            FROM events e 
            LEFT JOIN registrations r ON e.event_id = r.event_id 
            WHERE (e.created_by = ? OR r.user_id = ?) 
            AND e.event_date >= CURDATE() 
            AND e.event_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ");
        $stmt->execute([$userId, $userId]);
        $thisWeekEvents = $stmt->fetchColumn();
        
        $userStats = [
            'my_events' => $myEventsCount,
            'total_registrations' => $totalRegistrations,
            'this_week_events' => $thisWeekEvents
        ];
        
    } catch (Exception $e) {
        error_log("Dashboard stats error: " . $e->getMessage());
        // Gebruik fallback stats bij database fout
        $userStats = [
            'my_events' => 0,
            'total_registrations' => 0,
            'this_week_events' => 0
        ];
    }
}
?>
<?php include_once 'public/includes/head.php'; ?>
<?php include_once 'public/includes/header.php'; ?>

      <div class="content-area">
        <div class="dashboard-welcome">
          <?php if ($isLoggedIn): ?>
            <h2>Welkom terug, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
            <p>Hier is een overzicht van je evenementen en activiteiten.</p>
          <?php else: ?>
            <h2>Welkom bij EventPlanner</h2>
            <p>Organiseer en beheer eenvoudig al je evenementen op één plek.</p>
          <?php endif; ?>
        </div>
        
        <div class="dashboard-stats">
          <div class="stat-card">
            <h3>Mijn Evenementen</h3>
            <div class="stat-number"><?php echo $isLoggedIn ? $userStats['my_events'] : '5'; ?></div>
            <p><?php echo $isLoggedIn ? 'Jouw actieve evenementen' : 'Actieve evenementen (demo)'; ?></p>
          </div>
          
          <div class="stat-card">
            <h3>Inschrijvingen</h3>
            <div class="stat-number"><?php echo $isLoggedIn ? $userStats['total_registrations'] : '23'; ?></div>
            <p><?php echo $isLoggedIn ? 'Totaal ingeschreven voor jouw events' : 'Totaal ingeschreven (demo)'; ?></p>
          </div>
          
          <div class="stat-card">
            <h3>Deze Week</h3>
            <div class="stat-number"><?php echo $isLoggedIn ? $userStats['this_week_events'] : '2'; ?></div>
            <p><?php echo $isLoggedIn ? 'Jouw events deze week' : 'Aankomende events (demo)'; ?></p>
          </div>
        </div>
        
        <div class="quick-actions">
          <h3>Snelle Acties</h3>
          <?php if ($isLoggedIn): ?>
            <a href="public/pages/create_event.php" class="btn-primary">
              <i class="fas fa-plus"></i> Nieuw Evenement
            </a>
            <a href="public/pages/events_overview.php" class="btn-secondary">
              <i class="fas fa-calendar"></i> Evenementen Bekijken
            </a>
          <?php else: ?>
            <a href="public/pages/login.php" class="btn-primary">
              <i class="fas fa-sign-in-alt"></i> Inloggen
            </a>
            <a href="public/pages/register.php" class="btn-secondary">
              <i class="fas fa-user-plus"></i> Registreren
            </a>
          <?php endif; ?>
        </div>
      </div>
    </main>
  </div>
<?php include_once 'public/includes/footer.php'; ?>