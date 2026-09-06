<?php
$pageTitle = 'Hospital Profile - VaxCare Hospital Portal';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
  <?php require_once __DIR__ . '/includes/navbar.php'; ?>

  <main class="p-3 p-md-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <div class="p-2 rounded-2" style="background: rgba(14, 165, 233, 0.15); color: #0ea5e9;">
            <i class="bi bi-building fs-5"></i>
          </div>
          <h4 class="fw-bold mb-0" style="color: var(--vax-text-main);">Hospital Facility Details</h4>
        </div>
        <div class="small" style="color: var(--vax-text-muted);">
          Dashboard / <span class="text-secondary">Manage Hospital Identification & Location</span>
        </div>
      </div>
    </div>

    <div class="row g-4 mt-1">
      <!-- Edit Form -->
      <div class="col-12 col-xl-8">
        <div class="card-vax p-4">
          <h6 class="fw-bold text-dark mb-3">Facility Registration & Public Info</h6>
          <form action="profile.php" method="POST">
            <div class="row g-3">
              <div class="col-12 col-md-8">
                <label class="form-label small fw-semibold text-muted">Hospital / Facility Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="hospital_name" value="Jinnah Hospital - Children's Ward" required>
              </div>

              <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold text-muted">Govt License / Reg ID</label>
                <input type="text" class="form-control bg-light" name="license_number" value="HOSP-JINNAH-042" readonly>
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label small fw-semibold text-muted">Chief Medical Officer / Duty In-Charge</label>
                <input type="text" class="form-control" name="officer_name" value="Dr. Sarah Khan">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label small fw-semibold text-muted">Hospital Contact Email</label>
                <input type="email" class="form-control" name="email" value="pediatrics@jinnahhospital.gov.pk">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label small fw-semibold text-muted">Helpdesk Official Phone</label>
                <input type="text" class="form-control" name="phone" value="+92 21 35539234">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label small fw-semibold text-muted">Emergency Vaccine Hotline</label>
                <input type="text" class="form-control" name="emergency_phone" value="+92 300 9988776">
              </div>

              <div class="col-12">
                <label class="form-label small fw-semibold text-muted">Complete Physical Address <span class="text-danger">*</span></label>
                <textarea class="form-control" name="address" rows="2">Rafiqui Shaheed Road, Karachi Cantonment, Karachi</textarea>
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label small fw-semibold text-muted">City / District</label>
                <input type="text" class="form-control" name="city" value="Karachi Cantonment">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label small fw-semibold text-muted">Vaccination Clinic Hours</label>
                <input type="text" class="form-control" name="timings" value="Mon - Sat: 08:30 AM - 04:30 PM">
              </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
              <button type="reset" class="btn btn-light border px-3">Reset</button>
              <button type="submit" class="btn btn-primary px-4 shadow-sm" style="background-color: #2563eb;">
                <i class="bi bi-check2 me-1"></i> Save Hospital Profile
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Facility Quick Badge -->
      <div class="col-12 col-xl-4">
        <div class="card-vax p-4 text-center">
          <div class="vax-logo-shield mx-auto mb-3" style="width: 60px; height: 60px; font-size: 2rem;">
            <i class="bi bi-hospital"></i>
          </div>
          <h5 class="fw-bold text-dark mb-1">Jinnah Hospital</h5>
          <span class="badge bg-primary-subtle text-primary border px-3 py-1 mb-3">Verified EPI Vaccination Center</span>

          <p class="text-muted small mb-3">
            Authorized regional hospital facility linked with VaxCare National Child Immunization Portal.
          </p>

          <ul class="list-group list-group-flush text-start small border-top pt-2">
            <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
              <span class="text-muted">Center Status:</span>
              <strong class="text-success"><i class="bi bi-circle-fill me-1" style="font-size: 0.55rem;"></i>Operational</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
              <span class="text-muted">Total Immunizations:</span>
              <strong class="text-dark">2,140 Doses</strong>
            </li>
            <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
              <span class="text-muted">Cold Vault Check:</span>
              <strong class="text-primary">Passed (Aug 2026)</strong>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </main>

  <?php require_once __DIR__ . '/includes/footer.php'; ?>
