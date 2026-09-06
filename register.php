<?php
$pageTitle = 'Register Hospital Facility - VaxCare Vaccination Management System';
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

  <div class="auth-card card shadow-lg p-4 p-md-5 bg-white my-4" style="max-width: 600px;">
    <!-- VaxCare Brand Header -->
    <div class="text-center mb-4">
      <div class="vax-logo-shield mx-auto mb-2" style="width: 52px; height: 52px; font-size: 1.6rem; border-radius: 12px;">
        <i class="bi bi-shield-fill-check"></i>
      </div>
      <h4 class="fw-bold text-dark mb-1">Register Hospital Facility</h4>
      <p class="text-muted small">Connect your hospital to VaxCare to receive pediatric appointments</p>
    </div>

    <!-- Registration Form -->
    <form action="register.php" method="POST">
      <div class="row g-3">
        <!-- Hospital Name -->
        <div class="col-12 col-md-7">
          <label class="form-label small fw-semibold text-muted">Hospital / Facility Name <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="hospital_name" placeholder="e.g. Jinnah Hospital" required>
        </div>

        <!-- License No -->
        <div class="col-12 col-md-5">
          <label class="form-label small fw-semibold text-muted">Govt. License / Reg # <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="license_number" placeholder="e.g. HOSP-9042" required>
        </div>

        <!-- Email -->
        <div class="col-12 col-md-6">
          <label class="form-label small fw-semibold text-muted">Official Email Address <span class="text-danger">*</span></label>
          <input type="email" class="form-control" name="email" placeholder="pediatrics@hospital.gov.pk" required>
        </div>

        <!-- Phone -->
        <div class="col-12 col-md-6">
          <label class="form-label small fw-semibold text-muted">Hospital Official Phone <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="phone" placeholder="021-35539234" required>
        </div>

        <!-- Location / City -->
        <div class="col-12 col-md-6">
          <label class="form-label small fw-semibold text-muted">City / District <span class="text-danger">*</span></label>
          <input type="text" class="form-control" name="city" placeholder="e.g. Karachi Cantonment" required>
        </div>

        <!-- Operating Hours -->
        <div class="col-12 col-md-6">
          <label class="form-label small fw-semibold text-muted">Vaccination Clinic Hours</label>
          <input type="text" class="form-control" name="hours" placeholder="e.g. 08:30 AM - 04:30 PM">
        </div>

        <!-- Physical Address -->
        <div class="col-12">
          <label class="form-label small fw-semibold text-muted">Complete Physical Address <span class="text-danger">*</span></label>
          <textarea class="form-control" name="address" rows="2" placeholder="Street address, ward number, sector..." required></textarea>
        </div>

        <!-- Password -->
        <div class="col-12 col-md-6">
          <label class="form-label small fw-semibold text-muted">Access Password <span class="text-danger">*</span></label>
          <div class="input-group">
            <input type="password" class="form-control border-end-0" id="regPassword" name="password" minlength="8" placeholder="••••••••" required>
            <button class="btn btn-outline-secondary border-start-0 toggle-password-btn" type="button" data-target="regPassword">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="col-12 col-md-6">
          <label class="form-label small fw-semibold text-muted">Confirm Password <span class="text-danger">*</span></label>
          <div class="input-group">
            <input type="password" class="form-control border-end-0" id="regConfirmPassword" name="confirm_password" minlength="8" placeholder="••••••••" required>
            <button class="btn btn-outline-secondary border-start-0 toggle-password-btn" type="button" data-target="regConfirmPassword">
              <i class="bi bi-eye"></i>
            </button>
          </div>
        </div>

        <!-- Checkbox -->
        <div class="col-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="termsCheck" required>
            <label class="form-check-label text-muted small" for="termsCheck">
              I certify that our hospital maintains active cold chain standards for vaccines.
            </label>
          </div>
        </div>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm mt-4 mb-3" style="background-color: #2563eb;">
        Register Hospital Facility
      </button>

      <!-- Demo Link -->
      <a href="index.php" class="btn btn-light border w-100 py-2 small fw-medium text-secondary mb-3">
        <i class="bi bi-box-arrow-in-right me-1"></i> Skip & View Dashboard (Demo)
      </a>
    </form>

    <!-- Sign In Link -->
    <div class="text-center text-muted small pt-2 border-top">
      Already registered? 
      <a href="login.php" class="text-primary fw-semibold text-decoration-none">Hospital Sign In</a>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/hospital.js"></script>
</body>
</html>
