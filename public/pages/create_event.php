<?php 
// Check of gebruiker ingelogd is
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$pageTitle = "Nieuw Evenement Maken"; 
?>
<?php include '../includes/head.php'; ?>
<?php include '../includes/header.php'; ?>

      <div class="content-area">
        <div class="create-event-container">
          <div class="page-header">
            <h2><i class="fas fa-plus-circle"></i> Nieuw Evenement Maken</h2>
            <p>Vul de details in voor je nieuwe evenement</p>
          </div>

          <?php if (isset($_SESSION['event_errors'])): ?>
            <div class="alert alert-error">
              <ul>
                <?php foreach ($_SESSION['event_errors'] as $error): ?>
                  <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
              </ul>
              <?php unset($_SESSION['event_errors']); ?>
            </div>
          <?php endif; ?>

          <div class="event-form-card">
            <form class="event-form" method="post" action="../../backend/events/create_event.php">
              <div class="form-group">
                <label for="title"><i class="fas fa-heading"></i> Event Titel</label>
                <input type="text" name="title" id="title" required 
                       value="<?php echo isset($_SESSION['event_data']['title']) ? htmlspecialchars($_SESSION['event_data']['title']) : ''; ?>">
              </div>

              <div class="form-group">
                <label for="description"><i class="fas fa-align-left"></i> Beschrijving</label>
                <textarea name="description" id="description" rows="4" required><?php echo isset($_SESSION['event_data']['description']) ? htmlspecialchars($_SESSION['event_data']['description']) : ''; ?></textarea>
              </div>

              <div class="form-row">
                <div class="form-group">
                  <label for="event_date"><i class="fas fa-calendar"></i> Event Datum & Tijd</label>
                  <input type="datetime-local" name="event_date" id="event_date" required 
                         value="<?php echo isset($_SESSION['event_data']['event_date']) ? htmlspecialchars($_SESSION['event_data']['event_date']) : ''; ?>">
                </div>

                <div class="form-group">
                  <label for="max_participants"><i class="fas fa-users"></i> Max Deelnemers</label>
                  <input type="number" name="max_participants" id="max_participants" min="1" max="1000" required 
                         value="<?php echo isset($_SESSION['event_data']['max_participants']) ? htmlspecialchars($_SESSION['event_data']['max_participants']) : '20'; ?>">
                </div>
              </div>

              <div class="form-actions">
                <button type="submit" class="btn-primary">
                  <i class="fas fa-save"></i> Event Aanmaken
                </button>
                <a href="my_events.php" class="btn-secondary">
                  <i class="fas fa-times"></i> Annuleren
                </a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
<?php include '../includes/footer.php'; ?>

<?php 
// Clean up session data na gebruik
if (isset($_SESSION['event_data'])) {
    unset($_SESSION['event_data']);
}
?>
