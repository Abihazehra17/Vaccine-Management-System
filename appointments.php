<?php
$pageTitle = 'Appointments Queue - VaxCare Hospital Portal';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
  <?php require_once __DIR__ . '/includes/navbar.php'; ?>

  <main class="p-3 p-md-4">
    <!-- Page Header & Right Button Group (Exact match to Image 1 & 3) -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
      <div>
        <div class="d-flex align-items-center gap-2 mb-1">
          <div class="p-2 rounded-2" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
            <i class="bi bi-calendar-check-fill fs-5"></i>
          </div>
          <h4 class="fw-bold mb-0" style="color: var(--vax-text-main);">Child Appointments Queue</h4>
        </div>
        <div class="small" style="color: var(--vax-text-muted);">
          Dashboard / <span class="text-secondary">Hospital Appointments Verification & Status</span>
        </div>
      </div>

      <!-- Top Right Button Group (Matching Image 1 & 3) -->
      <div class="d-flex gap-2">
        <a href="appointments.php?filter=pending" class="btn btn-sm rounded-2 text-white shadow-sm d-flex align-items-center gap-1" style="background-color: #f59e0b; font-size: 0.8rem; font-weight: 600;">
          <i class="bi bi-clock-history"></i>
          <span>Pending Queue (5)</span>
        </a>
        <a href="appointments.php" class="btn btn-sm rounded-2 btn-outline-primary d-flex align-items-center gap-1" style="font-size: 0.8rem; font-weight: 600;">
          <i class="bi bi-list-ul"></i>
          <span>All Appointment History</span>
        </a>
      </div>
    </div>

    <!-- Section Title (Matching Image 1 & 3) -->
    <h6 class="fw-bold text-dark mt-4 mb-3" style="font-size: 1.05rem;">
      Pending Child Appointments Requiring Hospital Action
    </h6>

    <!-- Table Container Card (Matching Image 1 & 3) -->
    <div class="card-vax p-3">
      <!-- Show Entries & Search Bar Row (Matching Image 1 & 3) -->
      <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">
        <div class="d-flex align-items-center gap-2 small text-muted">
          <span>Show</span>
          <select class="form-select form-select-sm" style="width: 70px;">
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
          </select>
          <span>entries</span>
        </div>

        <div class="d-flex align-items-center gap-2">
          <input type="search" id="tableSearchInput" class="form-control form-control-sm" placeholder="Search records..." style="max-width: 240px;">
        </div>
      </div>

      <!-- VaxCare Table (Exact match to Image 1 & 3) -->
      <div class="table-responsive">
        <table class="table-vax">
          <thead>
            <tr>
              <th>BOOKING REF <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size: 0.65rem;"></i></th>
              <th>CHILD PROFILE <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size: 0.65rem;"></i></th>
              <th>REQUESTED VACCINE <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size: 0.65rem;"></i></th>
              <th>TARGET HOSPITAL <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size: 0.65rem;"></i></th>
              <th>REQUESTED APPT DATE <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size: 0.65rem;"></i></th>
              <th>STATUS & APPROVAL <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size: 0.65rem;"></i></th>
              <th class="text-end">ACTION / DECISION <i class="bi bi-arrow-down-up text-muted ms-1" style="font-size: 0.65rem;"></i></th>
            </tr>
          </thead>
          <tbody id="vaxcareTableBody">
            <!-- Row 1 (Matching Image 1 & 3) -->
            <tr>
              <td>
                <span class="booking-ref">#BK-0002</span>
                <div class="text-muted" style="font-size: 0.725rem;">Booked: 23 Jul 2026</div>
              </td>
              <td>
                <div class="fw-bold text-dark">Mayam</div>
                <small class="text-muted">Parent: John Doe (+92 3553923483)</small>
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
                <span class="text-dark fw-semibold small">23 Jul 2026</span>
                <div class="text-muted" style="font-size: 0.725rem;">10:00 AM</div>
              </td>
              <td>
                <span class="status-pill status-pill-pending">
                  <i class="bi bi-clock-fill"></i> Pending
                </span>
              </td>
              <td class="text-end">
                <div class="d-inline-flex flex-column flex-sm-row gap-1">
                  <!-- Approve / Vaccinated Button (Green like Image 1 & 3) -->
                  <button type="button" class="btn-vax-approve"
                    data-bs-toggle="modal" data-bs-target="#updateStatusModal"
                    data-booking-id="BK-0002"
                    data-child-name="Mayam"
                    data-parent="John Doe (+92 3553923483)"
                    data-vaccine="BCG (new born)"
                    data-current-status="vaccinated">
                    <i class="bi bi-check2"></i> Vaccinated
                  </button>

                  <!-- Reject / Not Vaccinated Button (Red like Image 1 & 3) -->
                  <button type="button" class="btn-vax-reject"
                    data-bs-toggle="modal" data-bs-target="#updateStatusModal"
                    data-booking-id="BK-0002"
                    data-child-name="Mayam"
                    data-parent="John Doe (+92 3553923483)"
                    data-vaccine="BCG (new born)"
                    data-current-status="not_vaccinated">
                    <i class="bi bi-x-lg"></i> Missed
                  </button>
                </div>
              </td>
            </tr>

            <!-- Row 2 -->
            <tr>
              <td>
                <span class="booking-ref">#BK-0003</span>
                <div class="text-muted" style="font-size: 0.725rem;">Booked: 24 Jul 2026</div>
              </td>
              <td>
                <div class="fw-bold text-dark">Hamza Ali</div>
                <small class="text-muted">Parent: Ahmed Ali (+92 3001234567)</small>
              </td>
              <td>
                <span class="vaccine-badge-purple">OPV-1</span>
                <div class="text-muted" style="font-size: 0.725rem;">6 weeks</div>
              </td>
              <td>
                <div class="text-dark small"><i class="bi bi-hospital me-1 text-muted"></i>City Hospital</div>
                <small class="text-muted">Pediatric Ward</small>
              </td>
              <td>
                <span class="text-dark fw-semibold small">Today</span>
                <div class="text-muted" style="font-size: 0.725rem;">10:30 AM</div>
              </td>
              <td>
                <span class="status-pill status-pill-pending">
                  <i class="bi bi-clock-fill"></i> Pending
                </span>
              </td>
              <td class="text-end">
                <div class="d-inline-flex flex-column flex-sm-row gap-1">
                  <button type="button" class="btn-vax-approve"
                    data-bs-toggle="modal" data-bs-target="#updateStatusModal"
                    data-booking-id="BK-0003"
                    data-child-name="Hamza Ali"
                    data-parent="Ahmed Ali (+92 3001234567)"
                    data-vaccine="OPV-1 (6 weeks)"
                    data-current-status="vaccinated">
                    <i class="bi bi-check2"></i> Vaccinated
                  </button>
                  <button type="button" class="btn-vax-reject"
                    data-bs-toggle="modal" data-bs-target="#updateStatusModal"
                    data-booking-id="BK-0003"
                    data-child-name="Hamza Ali"
                    data-parent="Ahmed Ali (+92 3001234567)"
                    data-vaccine="OPV-1 (6 weeks)"
                    data-current-status="not_vaccinated">
                    <i class="bi bi-x-lg"></i> Missed
                  </button>
                </div>
              </td>
            </tr>

            <!-- Row 3: Vaccinated Row -->
            <tr>
              <td>
                <span class="booking-ref">#BK-0001</span>
                <div class="text-muted" style="font-size: 0.725rem;">Booked: 22 Jul 2026</div>
              </td>
              <td>
                <div class="fw-bold text-dark">Bilal Raza</div>
                <small class="text-muted">Parent: Usman Raza (+92 3335551234)</small>
              </td>
              <td>
                <span class="vaccine-badge-purple">MR-1</span>
                <div class="text-muted" style="font-size: 0.725rem;">9 months</div>
              </td>
              <td>
                <div class="text-dark small"><i class="bi bi-hospital me-1 text-muted"></i>City Hospital</div>
                <small class="text-muted">Vaccination Clinic</small>
              </td>
              <td>
                <span class="text-dark fw-semibold small">22 Jul 2026</span>
                <div class="text-muted" style="font-size: 0.725rem;">09:15 AM</div>
              </td>
              <td>
                <span class="status-pill status-pill-vaccinated">
                  <i class="bi bi-check-circle-fill"></i> Vaccinated
                </span>
              </td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-light border rounded px-3" style="font-size: 0.785rem;"
                  data-bs-toggle="modal" data-bs-target="#updateStatusModal"
                  data-booking-id="BK-0001"
                  data-child-name="Bilal Raza"
                  data-parent="Usman Raza (+92 3335551234)"
                  data-vaccine="MR-1 (9 months)"
                  data-current-status="vaccinated">
                  <i class="bi bi-eye me-1"></i> Edit Status
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer (Exact match to Image 1 & 3) -->
      <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center pt-3 mt-2 border-top gap-2">
        <span class="text-muted small">Showing <strong>1 to 3</strong> of <strong>3</strong> entries</span>
        <nav aria-label="Table pagination">
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
            <li class="page-item active"><a class="page-link" href="#" style="background-color: #2563eb; border-color: #2563eb;">1</a></li>
            <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </main>

  <!-- ============================================================== -->
  <!-- MODAL: Update Status Modal                                     -->
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
            <label class="form-label fw-bold mb-2">Vaccine Administration Outcome <span class="text-danger">*</span></label>
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
            <button type="submit" class="btn btn-primary px-4 shadow-sm" style="background-color: #2563eb;">
              <i class="bi bi-check2-circle me-1"></i> Save Status Update
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php require_once __DIR__ . '/includes/footer.php'; ?>
