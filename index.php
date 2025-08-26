<?php include_once 'public/includes/head.php'; ?>
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
              <a href="index.php">
                <i class="fas fa-home"></i>
                <span class="nav-text">Dashboard</span>
              </a>
            </li>
            <li>
              <a href="#">
                <i class="fas fa-calendar-alt"></i>
                <span class="nav-text">Evenementen</span>
              </a>
            </li>
            <li>
              <a href="#">
                <i class="fas fa-list"></i>
                <span class="nav-text">Mijn Events</span>
              </a>
            </li>
            <li>
              <a href="#">
                <i class="fas fa-user"></i>
                <span class="nav-text">Profiel</span>
              </a>
            </li>
          </ul>
      </nav>
    <!-- Main Content -->
    <main class="main-content">
      <header class="top-bar">
        <h1>Dashboard</h1>
        <div class="user-info">Welkom</div>
      </header>
      
      <div class="content-area">
        <!-- Hier komt je pagina content -->
      </div>
    </main>
  </div>
<?php include_once 'public/includes/footer.php'; ?>