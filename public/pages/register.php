<?php $pageTitle = "Registreren"; ?>
<?php include '../includes/head.php'; ?>
<body class="auth-body">
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h2><i class="fas fa-calendar-alt"></i> EventPlanner</h2>
        <p>Maak een account om evenementen te organiseren</p>
      </div>
      
      <?php if (isset($_SESSION['register_errors'])): ?>
        <div class="alert alert-error">
          <ul>
            <?php foreach ($_SESSION['register_errors'] as $error): ?>
              <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
          <?php unset($_SESSION['register_errors']); ?>
        </div>
      <?php endif; ?>
      
      <form class="auth-form" method="post" action="../../backend/auth/register.php">
        <div class="form-group">
          <label for="first_name"><i class="fas fa-user"></i> Voornaam</label>
          <input type="text" name="first_name" id="first_name" required 
                 value="<?php echo isset($_SESSION['register_data']['first_name']) ? htmlspecialchars($_SESSION['register_data']['first_name']) : ''; ?>">
        </div>
        
        <div class="form-group">
          <label for="last_name"><i class="fas fa-user"></i> Achternaam</label>
          <input type="text" name="last_name" id="last_name" required
                 value="<?php echo isset($_SESSION['register_data']['last_name']) ? htmlspecialchars($_SESSION['register_data']['last_name']) : ''; ?>">
        </div>
        
        <div class="form-group">
          <label for="email"><i class="fas fa-envelope"></i> E-mail</label>
          <input type="email" name="email" id="email" required
                 value="<?php echo isset($_SESSION['register_data']['email']) ? htmlspecialchars($_SESSION['register_data']['email']) : ''; ?>">
        </div>
        
        <div class="form-group">
          <label for="password"><i class="fas fa-lock"></i> Wachtwoord</label>
          <input type="password" name="password" id="password" required>
          <small class="form-text">Minimaal 6 karakters</small>
        </div>
        
        <div class="form-group">
          <label for="password_confirm"><i class="fas fa-lock"></i> Bevestig Wachtwoord</label>
          <input type="password" name="password_confirm" id="password_confirm" required>
        </div>
        
        <button class="btn-primary btn-full" type="submit">
          <i class="fas fa-user-plus"></i> Registreren
        </button>
        
        <div class="auth-links">
          <p>Al een account? <a href="login.php">Log hier in</a></p>
        </div>
      </form>
    </div>
  </div>
</body>
</html>

<?php 
// Clean up session data na gebruik
if (isset($_SESSION['register_data'])) {
    unset($_SESSION['register_data']);
}
?>