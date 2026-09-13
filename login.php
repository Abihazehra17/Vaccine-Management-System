<?php
require_once __DIR__ . '/db.php';

$pageTitle = 'Hospital Sign In - VaxCare Vaccination Management System';
$errorMessage = '';
$identity = '';

// Handle Logout action safely
if (isset($_GET['logout'])) {
    unset($_SESSION['hospital_id']);
    unset($_SESSION['hospital_name']);
    unset($_SESSION['hospital_email']);
    unset($_SESSION['role_id']);
    unset($_SESSION['user_role']);
}

// Process Login Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($identity) || empty($password)) {
        $errorMessage = 'Please enter both your hospital email/username and password.';
    } else {
        $stmt = mysqli_prepare(
            $connection,
            "SELECT hospital_id, role_id, hospital_name, email, username, password, status
             FROM hospitals
             WHERE email = ? OR username = ?
             LIMIT 1"
        );
        mysqli_stmt_bind_param($stmt, "ss", $identity, $identity);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $hospital = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if ($hospital) {
            // Support both modern bcrypt hash and legacy plain-text test accounts
            $passwordValid = password_verify($password, $hospital['password']) || ($password === $hospital['password']);

            if ($passwordValid) {
                if ($hospital['status'] === 'Inactive') {
                    $errorMessage = 'Your facility account is inactive. Please contact the administrator.';
                } else {
                    $_SESSION['hospital_id']    = (int) $hospital['hospital_id'];
                    $_SESSION['hospital_name']  = $hospital['hospital_name'];
                    $_SESSION['hospital_email'] = $hospital['email'];
                    $_SESSION['role_id']        = (int) ($hospital['role_id'] ?? 3);
                    $_SESSION['user_role']      = 'Hospital';

                    header("Location: index.php");
                    exit;
                }
            } else {
                $errorMessage = 'Incorrect password. Please verify and try again.';
            }
        } else {
            $errorMessage = 'No hospital facility registered with that email or username.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($pageTitle); ?></title>

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap 5.3 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- VaxCare Styles -->
  <link rel="stylesheet" href="assets/css/hospital.css">
</head>
<body class="auth-page p-3">

  <div class="auth-card card shadow-lg p-4 p-md-5 bg-white">
    <!-- VaxCare Brand Header (Matching Screenshot Logo) -->
    <div class="text-center mb-4">
      <div class="vax-logo-shield mx-auto mb-2" style="width: 52px; height: 52px; font-size: 1.6rem; border-radius: 12px;">
        <i class="bi bi-shield-fill-check"></i>
      </div>
      <h4 class="fw-bold text-dark mb-1">VaxCare Hospital Portal</h4>
      <p class="text-muted small">Sign in to manage pediatric appointments and vaccine status</p>
    </div>

    <!-- Alert Placeholders -->
    <?php if (isset($_GET['logout'])): ?>
      <div class="alert alert-info border-0 small py-2 px-3 mb-3 d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-info-circle-fill text-info"></i>
        <div>You have safely signed out of VaxCare Hospital Portal.</div>
      </div>
    <?php elseif (isset($_GET['registered'])): ?>
      <div class="alert alert-success border-0 small py-2 px-3 mb-3 d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-check-circle-fill text-success"></i>
        <div>Hospital facility registered! You can now log in.</div>
      </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
      <div class="alert alert-danger border-0 small py-2 px-3 mb-3 d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill text-danger"></i>
        <div><?php echo htmlspecialchars($errorMessage); ?></div>
      </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form action="login.php" method="POST">
      <!-- Email / Username -->
      <div class="mb-3">
        <label for="hospEmail" class="form-label small fw-semibold text-muted">Hospital Email / Username</label>
        <div class="input-group">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-building"></i></span>
          <input type="text" class="form-control border-start-0 ps-0" id="hospEmail" name="email" placeholder="e.g. shedhospital or shed@gmail.com" value="<?php echo htmlspecialchars(!empty($identity) ? $identity : 'shedhospital'); ?>" required>
        </div>
      </div>

      <!-- Password with Eye Toggle -->
      <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <label for="hospPassword" class="form-label small fw-semibold text-muted mb-0">Password</label>
          <a href="#" class="small text-decoration-none text-primary">Forgot password?</a>
        </div>
        <div class="input-group">
          <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
          <input type="password" class="form-control border-start-0 border-end-0 ps-0" id="hospPassword" name="password" value="<?php echo !empty($identity) ? '' : 'shed12345'; ?>" required>
          <button class="btn btn-outline-secondary border-start-0 toggle-password-btn" type="button" data-target="hospPassword" title="Show/Hide Password">
            <i class="bi bi-eye"></i>
          </button>
        </div>
      </div>

      <!-- Remember Me -->
      <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" id="rememberMe">
        <label class="form-check-label text-muted small" for="rememberMe">
          Keep hospital terminal session active
        </label>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mb-3" style="background-color: #2563eb;">
        Sign In to Hospital Portal
      </button>

      <!-- Direct Demo Link -->
      <a href="index.php" class="btn btn-light border w-100 py-2 small fw-medium text-secondary mb-3">
        <i class="bi bi-box-arrow-in-right me-1"></i> Open Hospital Dashboard (Demo)
      </a>
    </form>

    <!-- Register Link -->
    <div class="text-center text-muted small pt-2 border-top">
      New Healthcare Provider? 
      <a href="register.php" class="text-primary fw-semibold text-decoration-none">Register Hospital Facility</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/hospital.js"></script>
</body>
</html>
