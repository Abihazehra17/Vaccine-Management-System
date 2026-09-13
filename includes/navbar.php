<?php

require_once __DIR__ . '/../db.php';

$appointmentCount = 0;

$result = mysqli_query(
    $connection,
    "SELECT COUNT(*) AS total
     FROM bookings
     WHERE status = 'Pending'"
);

if ($result) {
    $appointmentCount = (int) mysqli_fetch_assoc($result)['total'];
}

// Dynamic Hospital Display (Safe for merge with Admin/Parent panels)
$hospitalName = $_SESSION['hospital_name'] ?? '';
$hospitalEmail = $_SESSION['hospital_email'] ?? '';

if (empty($hospitalName)) {
    $hospRes = mysqli_query($connection, "SELECT hospital_name, email FROM hospitals WHERE status = 'Active' LIMIT 1");
    if ($hospRes && ($hRow = mysqli_fetch_assoc($hospRes))) {
        $hospitalName = $hRow['hospital_name'];
        $hospitalEmail = $hRow['email'] ?? 'hospital@vaxcare.gov';
    } else {
        $hospitalName = 'Hospital Staff';
        $hospitalEmail = 'hospital@vaxcare.gov';
    }
}
$hospitalInitial = strtoupper(substr($hospitalName, 0, 1));
?>
<header class="top-navbar d-flex align-items-center justify-content-between">
  <!-- Left: Portal Identification -->
  <div class="d-flex align-items-center gap-3">
    <button class="btn btn-light d-lg-none p-1 border" id="sidebarToggle" type="button" aria-label="Toggle navigation">
      <i class="bi bi-list fs-5"></i>
    </button>

    <div class="portal-tag d-flex align-items-center">
      <i class="bi bi-shield-check me-2"></i>
      <span>Vaccination Management System • <strong>Hospital Portal</strong></span>
    </div>
  </div>

  <!-- Right: Dark Mode Switch, Quick Pill Badge, Profile -->
  <div class="d-flex align-items-center gap-3">
    <!-- Dark / Light Theme Toggle -->
    <button class="theme-toggle-btn" id="themeToggleBtn" type="button" title="Toggle Light/Dark Theme">
      <i class="bi bi-moon-fill"></i>
    </button>

    <!-- Quick Appointments Pill -->
    <a href="appointments.php" class="top-pill-btn shadow-sm">
      <i class="bi bi-calendar2-check text-warning"></i>
      <span>Appointments</span>
      <?php if ($appointmentCount > 0): ?>
        <span class="badge rounded-pill bg-warning text-dark ms-1" style="font-size: 0.72rem;"><?php echo $appointmentCount; ?></span>
      <?php endif; ?>
    </a>

    <!-- Staff Profile Dropdown -->
    <div class="dropdown">
      <button class="user-profile-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="user-avatar-circle">
          <?php echo htmlspecialchars($hospitalInitial); ?>
        </div>
        <span class="user-name-text d-none d-sm-inline"><?php echo htmlspecialchars($hospitalName); ?></span>
      </button>

      <!-- Dropdown Card -->
      <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-3" style="min-width: 250px;">
        <li class="d-flex align-items-center gap-3 mb-2 pb-2 border-bottom">
          <div class="user-avatar-circle" style="width: 42px; height: 42px; font-size: 1.1rem;">
            <?php echo htmlspecialchars($hospitalInitial); ?>
          </div>
          <div>
            <div class="fw-bold text-dark" style="font-size: 0.9rem;"><?php echo htmlspecialchars($hospitalName); ?></div>
            <small class="text-muted" style="font-size: 0.75rem;"><?php echo htmlspecialchars($hospitalEmail); ?></small>
            <div>
              <span class="badge bg-primary" style="font-size: 0.65rem;">HOSPITAL PORTAL</span>
            </div>
          </div>
        </li>
        <li>
          <a class="dropdown-item py-2 small d-flex align-items-center gap-2" href="appointments.php">
            <i class="bi bi-calendar-check text-warning"></i> Today's Queue
            <!-- <span class="badge bg-warning ms-auto" style="font-size: 0.65rem;">5</span> -->
          </a>
        </li>
        <li>
          <a class="dropdown-item py-2 small d-flex align-items-center gap-2" href="vaccines.php">
            <i class="bi bi-capsule text-teal"></i> Vaccine Inventory
          </a>
        </li>
        <li>
          <a class="dropdown-item py-2 small d-flex align-items-center gap-2" href="profile.php">
            <i class="bi bi-building"></i> Hospital Profile
          </a>
        </li>
        <li><hr class="dropdown-divider my-2"></li>
        <li>
          <a class="dropdown-item py-2 small text-danger d-flex align-items-center gap-2" href="login.php?logout=1">
            <i class="bi bi-box-arrow-right"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</header>
