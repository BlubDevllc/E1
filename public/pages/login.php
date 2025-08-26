<?php $pageTitle = "Inloggen"; ?>
<?php include '../includes/head.php'; ?>
<body class="auth-body">
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h2><i class="fas fa-calendar-alt"></i> EventPlanner</h2>
        <p>Log in om je evenementen te beheren</p>
      </div>
      
      <?php if (isset($_SESSION['login_error'])): ?>
        <div class="alert alert-error">
          <?php echo htmlspecialchars($_SESSION['login_error']); ?>
          <?php unset($_SESSION['login_error']); ?>
        </div>
      <?php endif; ?>
      
      <?php if (isset($_SESSION['register_success'])): ?>
        <div class="alert alert-success">
          <?php echo htmlspecialchars($_SESSION['register_success']); ?>
          <?php unset($_SESSION['register_success']); ?>
        </div>
      <?php endif; ?>
      
      <form class="auth-form" method="post" action="../../backend/auth/login.php">
        <div class="form-group">
          <label for="email"><i class="fas fa-envelope"></i> E-mail</label>
          <input type="email" name="email" id="email" required>
        </div>
        
        <div class="form-group">
          <label for="password"><i class="fas fa-lock"></i> Wachtwoord</label>
          <input type="password" name="password" id="password" required>
        </div>
        
        <button class="btn-primary btn-full" type="submit">
          <i class="fas fa-sign-in-alt"></i> Inloggen
        </button>
        
        <div class="auth-links">
          <p>Nog geen account? <a href="register.php">Registreer hier</a></p>
        </div>
      </form>
    </div>
  </div>
</body>
</html>