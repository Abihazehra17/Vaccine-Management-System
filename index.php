<?php
$pageTitle = 'Hospital Dashboard - VaxCare Vaccination Management System';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
  <?php require_once __DIR__ . '/includes/navbar.php'; ?>

  <main class="p-3 p-md-4">
    <!-- Top Header & Pills (Matching Image 4) -->
    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-start align-items-xl-center mb-4 gap-3">
      <div>
        <h4 class="fw-bold mb-1" style="color: var(--vax-text-main);">Hospital Clinical Overview</h4>
        <p class="mb-0 small" style="color: var(--vax-text-muted);">Vaccination monitoring, scheduled child appointments, hospital directory & reports</p>
      </div>
      <!-- Top Action Pills (Exact match to Image 4) -->
      <div class="d-flex flex-wrap gap-2">
        <!-- <a href="appointments.php" class="btn btn-sm rounded-pill text-white shadow-sm d-flex align-items-center gap-2" style="background-color: #ef4444; font-size: 0.8rem; font-weight: 600;">
          <i class="bi bi-hospital"></i>
          <span>Today's Queue (5)</span>
        </a> -->
        <a href="appointments.php" class="btn btn-sm rounded-pill text-dark shadow-sm d-flex align-items-center gap-2" style="background-color: #f59e0b; font-size: 0.8rem; font-weight: 600;">
          <i class="bi bi-clock-history"></i>
          <span>Pending Doses (5)</span>
        </a>
        <a href="appointments.php" class="btn btn-sm rounded-pill text-white shadow-sm d-flex align-items-center gap-2" style="background-color: #2563eb; font-size: 0.8rem; font-weight: 600;">
          <i class="bi bi-file-earmark-medical"></i>
          <span>Vaccination Reports</span>
        </a>
      </div>
    </div>

    <!-- Alert Banner with Red Left Accent (Exact match to Image 4) -->
    <div class="alert-banner-vax">
      <div class="alert-banner-left">
        <div class="alert-icon-box">
          <i class="bi bi-hospital"></i>
        </div>
        <div>
          <div class="fw-bold text-dark" style="font-size: 0.925rem;">Pending Child Vaccination Appointments</div>
          <small class="text-muted">There are <strong>5 child appointment(s)</strong> awaiting vaccination status update in your clinic queue.</small>
        </div>
      </div>
      <a href="appointments.php" class="alert-banner-btn">
        <span>Review Queue</span>
        <i class="bi bi-arrow-right"></i>
      </a>
    </div>

    <!-- 6 Colorful Stat Tiles Row (Exact match to Image 4 & 2) -->
    <div class="row g-3 mb-4">
      <!-- Tile 1: Children -->
      <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card-vax">
          <div class="stat-tile-icon stat-tile-blue">
            <i class="bi bi-person-fill"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Children</span>
            <span class="stat-number">18</span>
          </div>
        </div>
      </div>

      <!-- Tile 2: Parents -->
      <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card-vax">
          <div class="stat-tile-icon stat-tile-cyan">
            <i class="bi bi-people-fill"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Parents</span>
            <span class="stat-number">18</span>
          </div>
        </div>
      </div>

      <!-- Tile 3: Hospitals -->
      <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card-vax">
          <div class="stat-tile-icon stat-tile-green">
            <i class="bi bi-hospital-fill"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Station</span>
            <span class="stat-number" style="font-size: 1.15rem;">Active</span>
          </div>
        </div>
      </div>

      <!-- Tile 4: Vaccines -->
      <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card-vax">
          <div class="stat-tile-icon stat-tile-purple">
            <i class="bi bi-capsule"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Vaccines</span>
            <span class="stat-number">6</span>
          </div>
        </div>
      </div>

      <!-- Tile 5: Pending Req -->
      <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card-vax">
          <div class="stat-tile-icon stat-tile-orange">
            <i class="bi bi-clock-fill"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Pending</span>
            <span class="stat-number">5</span>
          </div>
        </div>
      </div>

      <!-- Tile 6: Vaccinated -->
      <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card-vax">
          <div class="stat-tile-icon stat-tile-red">
            <i class="bi bi-shield-fill-check"></i>
          </div>
          <div class="stat-info">
            <span class="stat-label">Vaccinated</span>
            <span class="stat-number">12</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Middle Section: Today's Appointments Table & Recent Children (Exact match to Image 4) -->
    <div class="row g-4 mb-4">
      <!-- Left (col-xl-8): Pending Patient Appointments Table -->
      <div class="col-12 col-xl-8">
        <div class="card-vax h-100">
          <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-journal-medical text-warning fs-5"></i>
              <h6 class="fw-bold mb-0" style="color: var(--vax-text-main);">Today's Patient Appointment Queue</h6>
            </div>
            <a href="appointments.php" class="btn btn-outline-primary btn-sm rounded-pill px-3" style="font-size: 0.775rem;">
              View All (18)
            </a>
          </div>

          <div class="table-responsive">
            <table class="table-vax">
              <thead>
                <tr>
                  <th>BOOKING ID</th>
                  <th>CHILD PROFILE</th>
                  <th>VACCINE</th>
                  <th>TARGET HOSPITAL</th>
                  <th>APPT DATE</th>
                  <th class="text-end">ACTION</th>
                </tr>
              </thead>
              <tbody>
                <!-- Row 1 (matching style of Image 4) -->
                <tr>
                  <td>
                    <span class="booking-ref">#BK-0002</span>
                  </td>
                  <td>
                    <div class="fw-bold text-dark">Mayam</div>
                    <small class="text-muted">Parent: John Doe</small>
                  </td>
                  <td>
                    <span class="vaccine-badge-purple">BCG</span>
                    <div class="text-muted" style="font-size: 0.725rem;">new born</div>
                  </td>
                  <td>
                    <div class="text-dark small"><i class="bi bi-hospital me-1 text-muted"></i>City Hospital</div>
                    <small class="text-muted">Children's Ward</small>
                  </td>
                  <td>
                    <span class="text-dark fw-semibold small">Today</span>
                    <div class="text-muted" style="font-size: 0.725rem;">10:00 AM</div>
                  </td>
                  <td class="text-end">
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" style="background-color: #2563eb; font-size: 0.775rem;"
                      data-bs-toggle="modal" data-bs-target="#updateStatusModal"
                      data-booking-id="BK-0002"
                      data-child-name="Mayam"
                      data-parent="John Doe (+92 3553923483)"
                      data-vaccine="BCG (new born)"
                      data-current-status="pending">
                      <i class="bi bi-pencil-square me-1"></i> Update
                    </button>
                  </td>
                </tr>

                <!-- Row 2 -->
                <tr>
                  <td>
                    <span class="booking-ref">#BK-0003</span>
                  </td>
                  <td>
                    <div class="fw-bold text-dark">Hamza Ali</div>
                    <small class="text-muted">Parent: Ahmed Ali</small>
                  </td>
                  <td>
                    <span class="vaccine-badge-purple">OPV + PENTA</span>
                    <div class="text-muted" style="font-size: 0.725rem;">6 weeks</div>
                  </td>
                  <td>
                    <div class="text-dark small"><i class="bi bi-hospital me-1 text-muted"></i>City Hospital</div>
                    <small class="text-muted">Pediatric OPD</small>
                  </td>
                  <td>
                    <span class="text-dark fw-semibold small">Today</span>
                    <div class="text-muted" style="font-size: 0.725rem;">10:30 AM</div>
                  </td>
                  <td class="text-end">
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" style="background-color: #2563eb; font-size: 0.775rem;"
                      data-bs-toggle="modal" data-bs-target="#updateStatusModal"
                      data-booking-id="BK-0003"
                      data-child-name="Hamza Ali"
                      data-parent="Ahmed Ali (0300-1234567)"
                      data-vaccine="OPV Dose 1 + Pentavalent-1"
                      data-current-status="pending">
                      <i class="bi bi-pencil-square me-1"></i> Update
                    </button>
                  </td>
                </tr>

                <!-- Row 3 -->
                <tr>
                  <td>
                    <span class="booking-ref">#BK-0004</span>
                  </td>
                  <td>
                    <div class="fw-bold text-dark">Zoya Khan</div>
                    <small class="text-muted">Parent: Farhan Khan</small>
                  </td>
                  <td>
                    <span class="vaccine-badge-purple">HEPB</span>
                    <div class="text-muted" style="font-size: 0.725rem;">birth dose</div>
                  </td>
                  <td>
                    <div class="text-dark small"><i class="bi bi-hospital me-1 text-muted"></i>City Hospital</div>
                    <small class="text-muted">Maternity Block</small>
                  </td>
                  <td>
                    <span class="text-dark fw-semibold small">Today</span>
                    <div class="text-muted" style="font-size: 0.725rem;">11:15 AM</div>
                  </td>
                  <td class="text-end">
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" style="background-color: #2563eb; font-size: 0.775rem;"
                      data-bs-toggle="modal" data-bs-target="#updateStatusModal"
                      data-booking-id="BK-0004"
                      data-child-name="Zoya Khan"
                      data-parent="Farhan Khan (0321-9876543)"
                      data-vaccine="Hepatitis B (Birth Dose)"
                      data-current-status="pending">
                      <i class="bi bi-pencil-square me-1"></i> Update
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Right (col-xl-4): Recent Children Cards (Exact match to Image 4) -->
      <div class="col-12 col-xl-4">
        <div class="card-vax h-100">
          <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-person-hearts text-primary fs-5"></i>
              <h6 class="fw-bold mb-0" style="color: var(--vax-text-main);">Recent Children</h6>
            </div>
            <a href="appointments.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3" style="font-size: 0.775rem;">
              View All
            </a>
          </div>

          <div class="p-3 d-flex flex-column gap-3">
            <!-- Child Card 1 (matching Image 4) -->
            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-body-tertiary">
              <div>
                <h6 class="fw-bold text-dark mb-0">Mayam</h6>
                <small class="text-muted">Female • Age: 6 wks</small>
              </div>
              <button type="button" class="btn btn-sm btn-light border rounded px-3" style="font-size: 0.75rem;">
                Profile
              </button>
            </div>

            <!-- Child Card 2 -->
            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-body-tertiary">
              <div>
                <h6 class="fw-bold text-dark mb-0">Sarah</h6>
                <small class="text-muted">Female • Age: 9 mos</small>
              </div>
              <button type="button" class="btn btn-sm btn-light border rounded px-3" style="font-size: 0.75rem;">
                Profile
              </button>
            </div>

            <!-- Child Card 3 -->
            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-body-tertiary">
              <div>
                <h6 class="fw-bold text-dark mb-0">Bilal Raza</h6>
                <small class="text-muted">Male • Age: 9 mos</small>
              </div>
              <button type="button" class="btn btn-sm btn-light border rounded px-3" style="font-size: 0.75rem;">
                Profile
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom 4 Quick Navigation Cards (Exact match to Image 4 & 2) -->
    <div class="row g-3">
      <!-- Nav Card 1: Date of Vaccination -->
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="quick-nav-card">
          <i class="bi bi-calendar2-week text-primary quick-nav-icon"></i>
          <div class="quick-nav-title">Date of Vaccination</div>
          <div class="quick-nav-subtitle">Upcoming vaccination dates of all children</div>
          <a href="appointments.php" class="quick-nav-btn">Open Schedule</a>
        </div>
      </div>

      <!-- Nav Card 2: List of Vaccine -->
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="quick-nav-card">
          <i class="bi bi-eyedropper text-success quick-nav-icon"></i>
          <div class="quick-nav-title">List of Vaccine</div>
          <div class="quick-nav-subtitle">View catalog & toggle availability</div>
          <a href="vaccines.php" class="quick-nav-btn">View Vaccines</a>
        </div>
      </div>

      <!-- Nav Card 3: Hospital Profile -->
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="quick-nav-card">
          <i class="bi bi-hospital text-info quick-nav-icon"></i>
          <div class="quick-nav-title">Hospitals Directory</div>
          <div class="quick-nav-subtitle">View facility license & clinic location</div>
          <a href="profile.php" class="quick-nav-btn">Manage Hospital</a>
        </div>
      </div>

      <!-- Nav Card 4: Report of Vaccination -->
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="quick-nav-card">
          <i class="bi bi-file-earmark-medical text-danger quick-nav-icon"></i>
          <div class="quick-nav-title">Report of Vaccination</div>
          <div class="quick-nav-subtitle">Filter date-wise vaccination reports</div>
          <a href="appointments.php" class="quick-nav-btn">View Reports</a>
        </div>
      </div>
    </div>
  </main>

  <!-- ============================================================== -->
  <!-- MODAL: Update Child Vaccination Status (Core Requirement)       -->
  <!-- ============================================================== -->
  <div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow">
        <form action="appointments.php" method="POST">
          <input type="hidden" name="action" value="update_vaccine_status">
          <input type="hidden" name="booking_id" id="statusBookingId" value="">

          <div class="modal-header bg-light">
            <div>
              <h5 class="modal-title fw-bold text-dark" id="updateStatusModalLabel">
                <i class="bi bi-shield-check text-primary me-2"></i>Update Child Vaccination Status
              </h5>
              <small class="text-muted">Booking Reference: <span id="modalBookingIdDisplay" class="fw-bold text-primary">#</span></small>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body p-4">
            <!-- Patient Info Box -->
            <div class="p-3 rounded-3 border mb-4 bg-body-tertiary">
              <div class="row g-2">
                <div class="col-6 col-md-4">
                  <small class="text-muted d-block">Child Name</small>
                  <strong class="text-dark" id="modalChildName">Child Name</strong>
                </div>
                <div class="col-6 col-md-4">
                  <small class="text-muted d-block">Prescribed Vaccine</small>
                  <strong class="text-primary" id="modalVaccineName">Vaccine Name</strong>
                </div>
                <div class="col-12 col-md-4">
                  <small class="text-muted d-block">Parent Contact</small>
                  <strong class="text-dark" id="modalParentName">Parent Name</strong>
                </div>
              </div>
            </div>

            <!-- Status Choice: Vaccinated vs Not Vaccinated -->
            <label class="form-label fw-bold mb-2">Vaccine Administration Status <span class="text-danger">*</span></label>
            <div class="row g-3 mb-4">
              <div class="col-12 col-md-6">
                <div class="form-check p-3 border rounded-3 h-100 bg-white shadow-sm position-relative">
                  <input class="form-check-input ms-0 me-2" type="radio" name="vaccine_status" id="statusVaccinated" value="vaccinated" checked>
                  <label class="form-check-label fw-bold text-success stretched-link" for="statusVaccinated">
                    <i class="bi bi-check-circle-fill me-1"></i> Vaccinated (Completed)
                  </label>
                  <div class="text-muted small mt-1 ms-4">Child attended hospital and vaccine dose was successfully administered.</div>
                </div>
              </div>

              <div class="col-12 col-md-6">
                <div class="form-check p-3 border rounded-3 h-100 bg-white shadow-sm position-relative">
                  <input class="form-check-input ms-0 me-2" type="radio" name="vaccine_status" id="statusNotVaccinated" value="not_vaccinated">
                  <label class="form-check-label fw-bold text-danger stretched-link" for="statusNotVaccinated">
                    <i class="bi bi-x-circle-fill me-1"></i> Not Vaccinated (Missed)
                  </label>
                  <div class="text-muted small mt-1 ms-4">Child did not attend, was unwell (fever), or dose was postponed.</div>
                </div>
              </div>
            </div>

            <!-- Vaccinated Details -->
            <div id="vaccinatedDetailsSection" class="p-3 rounded-3 border bg-light mb-3">
              <h6 class="fw-bold text-success mb-3"><i class="bi bi-clipboard2-pulse me-1"></i> Dose Administration Details</h6>
              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold">Vaccine Batch / Lot Number <span class="text-danger">*</span></label>
                  <input type="text" name="batch_number" class="form-control form-control-sm" value="LOT-VAX-2026-X4">
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label small fw-semibold">Administering Doctor / Nurse</label>
                  <input type="text" name="admin_staff" class="form-control form-control-sm" value="Dr. Sarah Khan">
                </div>
                <div class="col-12">
                  <label class="form-label small fw-semibold">Clinical Observations & Parent Advice</label>
                  <input type="text" name="admin_notes" class="form-control form-control-sm" placeholder="e.g., Well tolerated, advised to monitor temperature for 24h">
                </div>
              </div>
            </div>

            <!-- Not Vaccinated Reason -->
            <div id="notVaccinatedReasonSection" class="p-3 rounded-3 border bg-light mb-3 d-none">
              <h6 class="fw-bold text-danger mb-3"><i class="bi bi-exclamation-triangle me-1"></i> Non-Administration Reason</h6>
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label small fw-semibold">Reason for Absence or Postponement <span class="text-danger">*</span></label>
                  <select name="reason" class="form-select form-select-sm">
                    <option value="absent">Parent / Child Absent (No Show)</option>
                    <option value="unwell">Child Unwell / High Fever (Medically Postponed)</option>
                    <option value="rescheduled">Parent Requested Reschedule</option>
                    <option value="stock_out">Vaccine Temporarily Out of Stock</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label small fw-semibold">Follow-Up Instructions for Parent</label>
                  <input type="text" name="followup_notes" class="form-control form-control-sm" placeholder="e.g., Advised to return next Wednesday once fever clears">
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer bg-light">
            <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary px-4 shadow-sm">
              <i class="bi bi-check2-circle me-1"></i> Save Status Update
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php require_once __DIR__ . '/includes/footer.php'; ?>
