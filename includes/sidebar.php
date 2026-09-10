<?php
/**
 * VaxCare - Hospital Portal Sidebar Template
 */
$currentPage = basename($_SERVER['PHP_SELF'] ?? 'index.php');
?>
<!-- Mobile Backdrop -->
<div id="sidebarBackdrop" class="sidebar-backdrop d-none d-lg-none"></div>

<!-- VaxCare Dark Sidebar -->
<aside id="appSidebar" class="sidebar">
  <!-- Brand Logo -->
  <a href="index.php" class="brand">
    <div class="brand-left">
      <div class="vax-logo-shield">
        <i class="bi bi-shield-fill-check"></i>
      </div>
      <span class="brand-text">VaxCare</span>
    </div>
    <button class="brand-hamburger d-none d-lg-block" type="button" aria-label="Menu">
      <i class="bi bi-list"></i>
    </button>
  </a>

  <!-- Navigation Menu -->
  <nav class="sidebar-nav">
    <!-- Main Dashboard Link -->
    <a href="index.php" class="sidebar-link <?php echo ($currentPage === 'index.php') ? 'active' : ''; ?>">
      <div class="link-content">
        <i class="bi bi-speedometer2"></i>
        <span>Hospital Dashboard</span>
      </div>
    </a>

    <!-- Category 1: CHILD & VACCINATION -->
    <div class="sidebar-heading">CHILD & VACCINATION</div>

    <!-- <a href="appointments.php?filter=today" class="sidebar-link  echo (isset($_GET['filter']) && $_GET['filter'] === 'today') ? 'active' : ''; ?>">
      <div class="link-content">
        <i class="bi bi-person-lines-fill"></i>
        <span>Today's Queue</span>
      </div>
      <span class="badge-pill-count badge-pill-orange">5</span>
    </a> -->

    <a href="appointments.php" class="sidebar-link <?php echo ($currentPage === 'appointments.php' && !isset($_GET['filter'])) ? 'active' : ''; ?>">
      <div class="link-content">
        <i class="bi bi-calendar-check"></i>
        <span>Appointments Queue</span>
      </div>
      <span class="badge-pill-count badge-pill-red">1</span>
    </a>

    <a href="vaccines.php" class="sidebar-link <?php echo ($currentPage === 'vaccines.php') ? 'active' : ''; ?>">
      <div class="link-content">
        <i class="bi bi-capsule"></i>
        <span>List of Vaccine</span>
      </div>
    </a>

    <!-- Category 2: BOOKINGS & CLINIC -->
    <div class="sidebar-heading">BOOKINGS & CLINIC</div>

    <a href="appointments.php" class="sidebar-link <?php echo ($currentPage === 'appointments.php') ? 'active' : ''; ?>">
      <div class="link-content">
        <i class="bi bi-journal-medical"></i>
        <span>Booking Details</span>
      </div>
    </a>

    <a href="profile.php" class="sidebar-link <?php echo ($currentPage === 'profile.php') ? 'active' : ''; ?>">
      <div class="link-content">
        <i class="bi bi-building"></i>
        <span>Hospital Profile</span>
      </div>
    </a>

    <!-- Logout -->
    <a href="login.php?logout=1" class="sidebar-link logout-link">
      <div class="link-content">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
      </div>
    </a>
  </nav>
</aside>
