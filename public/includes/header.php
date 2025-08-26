<?php
// Include config voor session management
require_once __DIR__ . '/../../backend/config/config.php';

// Check of gebruiker is ingelogd (optioneel per pagina)
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$userName = $isLoggedIn ? $_SESSION['user_name'] : 'Gast';
?>

<body>
  <div class="app-container">
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <h3>EventPlanner</h3>
          <button class="toggle-btn">
            <i class="fas fa-bars"></i>
          </button>
        </div>
          <ul class="nav-menu">
            <li>
              <a href="<?php echo $isLoggedIn ? '/school/E1/index.php' : '/school/E1/public/pages/login.php'; ?>">
                <i class="fas fa-home"></i>
                <span class="nav-text">Dashboard</span>
              </a>
            </li>
            <li>
              <a href="<?php echo $isLoggedIn ? '/school/E1/public/pages/events_overview.php' : '/school/E1/public/pages/login.php'; ?>">
                <i class="fas fa-calendar-alt"></i>
                <span class="nav-text">Evenementen</span>
              </a>
            </li>
            <li>
              <a href="<?php echo $isLoggedIn ? '/school/E1/public/pages/my_events.php' : '/school/E1/public/pages/login.php'; ?>">
                <i class="fas fa-list"></i>
                <span class="nav-text">Mijn Events</span>
              </a>
            </li>
            <li>
              <a href="<?php echo $isLoggedIn ? '#' : '/school/E1/public/pages/login.php'; ?>">
                <i class="fas fa-user"></i>
                <span class="nav-text">Profiel</span>
              </a>
            </li>
            <?php if ($isLoggedIn): ?>
            <li>
              <a href="/school/E1/backend/auth/logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span class="nav-text">Uitloggen</span>
              </a>
            </li>
            <?php else: ?>
            <li>
              <a href="/school/E1/public/pages/login.php">
                <i class="fas fa-sign-in-alt"></i>
                <span class="nav-text">Inloggen</span>
              </a>
            </li>
            <?php endif; ?>
          </ul>
      </nav>
    <!-- Main Content -->
    <main class="main-content">
      <header class="top-bar">
        <h1><?php echo isset($pageTitle) ? $pageTitle : 'EventPlanner'; ?></h1>
        <div class="user-info">
          <?php if ($isLoggedIn): ?>
            Welkom, <?php echo htmlspecialchars($userName); ?>
          <?php else: ?>
            <a href="/school/E1/public/pages/login.php" class="login-link">Inloggen</a>
          <?php endif; ?>
        </div>
      </header>