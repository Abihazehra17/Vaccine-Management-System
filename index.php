<?php
/**
 * Hospital Dashboard - Clinical Overview
 * VaxCare Vaccination Management System
 */

require_once __DIR__ . '/db.php';

$pageTitle = 'Hospital Dashboard - VaxCare Vaccination Management System';

/*  DASHBOARD METRICS & DATABASE COUNTS */
   

// Children Count
$children_count = 0;
$res = mysqli_query($connection, "SELECT COUNT(*) AS total FROM children");
if ($res && ($row = mysqli_fetch_assoc($res))) {
    $children_count = (int) $row['total'];
}

// Parents Count
$parents_count = 0;
$res = mysqli_query($connection, "SELECT COUNT(*) AS total FROM parents");
if ($res && ($row = mysqli_fetch_assoc($res))) {
    $parents_count = (int) $row['total'];
}

// Hospitals Count
$hospitals_count = 0;
$res = mysqli_query($connection, "SELECT COUNT(*) AS total FROM hospitals");
if ($res && ($row = mysqli_fetch_assoc($res))) {
    $hospitals_count = (int) $row['total'];
}

// Vaccines Count
$vaccines_count = 0;
$res = mysqli_query($connection, "SELECT COUNT(*) AS total FROM vaccines");
if ($res && ($row = mysqli_fetch_assoc($res))) {
    $vaccines_count = (int) $row['total'];
}

// Pending Appointments Count
$pending_count = 0;
$res = mysqli_query($connection, "SELECT COUNT(*) AS total FROM bookings WHERE status = 'Pending'");
if ($res && ($row = mysqli_fetch_assoc($res))) {
    $pending_count = (int) $row['total'];
}

// Vaccinated Children Count
$vaccinated_count = 0;
$res = mysqli_query($connection, "SELECT COUNT(*) AS total FROM vaccination_records WHERE status = 'Vaccinated'");
if ($res && ($row = mysqli_fetch_assoc($res))) {
    $vaccinated_count = (int) $row['total'];
}

// Total Appointments
$total_appointments = 0;
$res = mysqli_query($connection, "SELECT COUNT(*) AS total FROM bookings");
if ($res && ($row = mysqli_fetch_assoc($res))) {
    $total_appointments = (int) $row['total'];
}

/* =========================================================
   2. RECENT PENDING APPOINTMENTS (QUEUE)
   ========================================================= */
$appointmentQuery = "
    SELECT
        b.booking_id,
        b.child_id,
        b.hospital_id,
        b.vaccine_id,
        b.admin_id,
        b.booking_date,
        b.appointment_date,
        b.status,
        b.approval_date,
        c.child_name,
        c.gender,
        c.date_of_birth,
        p.name AS parent_name,
        p.phone AS parent_phone,
        v.vaccine_name,
        v.age_group,
        v.stock_status,
        h.hospital_name
    FROM bookings b
    LEFT JOIN children c ON b.child_id = c.child_id
    LEFT JOIN parents p ON c.parent_id = p.parent_id
    LEFT JOIN vaccines v ON b.vaccine_id = v.vaccine_id
    LEFT JOIN hospitals h ON b.hospital_id = h.hospital_id
    WHERE b.status = 'Pending'
    ORDER BY b.appointment_date ASC
    LIMIT 5
";

$appointmentResult = mysqli_query($connection, $appointmentQuery);
$appointments = [];
if ($appointmentResult) {
    while ($row = mysqli_fetch_assoc($appointmentResult)) {
        $appointments[] = $row;
    }
}

/* =========================================================
   3. RECENT REGISTERED CHILDREN
   ========================================================= */
$childrenQuery = "SELECT child_id, child_name, gender, date_of_birth FROM children ORDER BY child_id DESC LIMIT 3";
$childrenResult = mysqli_query($connection, $childrenQuery);
$recent_children = [];
if ($childrenResult) {
    while ($row = mysqli_fetch_assoc($childrenResult)) {
        $recent_children[] = $row;
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="p-3 p-md-4">
        <!-- Top Overview Header -->
        <div class="d-flex flex-column flex-xl-row justify-content-between align-items-start align-items-xl-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--vax-text-main);">
                    Hospital Clinical Overview
                </h4>
                <p class="mb-0 small" style="color: var(--vax-text-muted);">
                    Vaccination monitoring, scheduled child appointments, hospital directory & reports
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="appointments.php" class="btn btn-sm rounded-pill text-white shadow-sm d-flex align-items-center gap-2" style="background-color: #2563eb; font-size: 0.8rem; font-weight: 600;">
                    <i class="bi bi-file-earmark-medical"></i>
                    <span>Vaccination Reports</span>
                </a>
            </div>
        </div>

        <!-- Pending Appointments Alert Banner -->
        <div class="alert-banner-vax mb-4">
            <div class="alert-banner-left">
                <div class="alert-icon-box">
                    <i class="bi bi-hospital"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark" style="font-size: 0.925rem;">
                        Pending Child Vaccination Appointments
                    </div>
                    <small class="text-muted">
                        There are <strong><?php echo $pending_count; ?> child appointment(s)</strong> awaiting vaccination status update in your clinic queue.
                    </small>
                </div>
            </div>
            <a href="appointments.php?filter=pending" class="alert-banner-btn">
                <span>Review Queue</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <!-- 6 Stat Cards -->
        <div class="row g-3 mb-4">
            <!-- Children -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card-vax">
                    <div class="stat-tile-icon stat-tile-blue">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Children</span>
                        <span class="stat-number"><?php echo $children_count; ?></span>
                    </div>
                </div>
            </div>

            <!-- Parents -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card-vax">
                    <div class="stat-tile-icon stat-tile-cyan">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Parents</span>
                        <span class="stat-number"><?php echo $parents_count; ?></span>
                    </div>
                </div>
            </div>

            <!-- Hospital -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card-vax">
                    <div class="stat-tile-icon stat-tile-green">
                        <i class="bi bi-hospital-fill"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Hospital</span>
                        <span class="stat-number" style="font-size: 1.15rem;">Active</span>
                    </div>
                </div>
            </div>

            <!-- Vaccines -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card-vax">
                    <div class="stat-tile-icon stat-tile-purple">
                        <i class="bi bi-capsule"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Vaccines</span>
                        <span class="stat-number"><?php echo $vaccines_count; ?></span>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card-vax">
                    <div class="stat-tile-icon stat-tile-orange">
                        <i class="bi bi-clock-fill"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Pending</span>
                        <span class="stat-number"><?php echo $pending_count; ?></span>
                    </div>
                </div>
            </div>

            <!-- Vaccinated -->
            <div class="col-6 col-md-4 col-xl-2">
                <div class="stat-card-vax">
                    <div class="stat-tile-icon stat-tile-red">
                        <i class="bi bi-shield-fill-check"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Vaccinated</span>
                        <span class="stat-number"><?php echo $vaccinated_count; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Section: Today's Appointments & Recent Children -->
        <div class="row g-4 mb-4">
            <!-- Appointments Table -->
            <div class="col-12 col-xl-8">
                <div class="card-vax h-100">
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-journal-medical text-warning fs-5"></i>
                            <h6 class="fw-bold mb-0" style="color: var(--vax-text-main);">
                                Today's Patient Appointment Queue
                            </h6>
                        </div>
                        <a href="appointments.php" class="btn btn-outline-primary btn-sm rounded-pill px-3" style="font-size: 0.775rem;">
                            View All (<?php echo $total_appointments; ?>)
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
                            <?php if (count($appointments) > 0): ?>
                                <?php foreach ($appointments as $appointment): ?>
                                    <tr>
                                        <td>
                                            <span class="booking-ref">#<?php echo htmlspecialchars($appointment['booking_id']); ?></span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($appointment['child_name'] ?? 'Unknown Child'); ?></div>
                                            <small class="text-muted">Parent: <?php echo htmlspecialchars($appointment['parent_name'] ?? 'N/A'); ?></small>
                                        </td>
                                        <td>
                                            <span class="vaccine-badge-purple"><?php echo htmlspecialchars($appointment['vaccine_name'] ?? 'N/A'); ?></span>
                                            <div class="text-muted" style="font-size: 0.725rem;"><?php echo htmlspecialchars($appointment['age_group'] ?? 'N/A'); ?></div>
                                        </td>
                                        <td>
                                            <div class="text-dark small">
                                                <i class="bi bi-hospital me-1 text-muted"></i>
                                                <?php echo htmlspecialchars($appointment['hospital_name'] ?? 'N/A'); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($appointment['appointment_date'])): ?>
                                                <span class="text-dark fw-semibold small"><?php echo date('d M Y', strtotime($appointment['appointment_date'])); ?></span>
                                                <div class="text-muted" style="font-size: 0.725rem;"><?php echo date('h:i A', strtotime($appointment['appointment_date'])); ?></div>
                                            <?php else: ?>
                                                <span class="text-muted">Not scheduled</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" style="background-color: #2563eb; font-size: 0.775rem;"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#updateStatusModal"
                                                    data-booking-id="<?php echo htmlspecialchars($appointment['booking_id']); ?>"
                                                    data-child-name="<?php echo htmlspecialchars($appointment['child_name'] ?? '', ENT_QUOTES); ?>"
                                                    data-parent="<?php echo htmlspecialchars(($appointment['parent_name'] ?? '') . ' (' . ($appointment['parent_phone'] ?? '') . ')', ENT_QUOTES); ?>"
                                                    data-vaccine="<?php echo htmlspecialchars($appointment['vaccine_name'] ?? '', ENT_QUOTES); ?>"
                                                    data-current-status="pending">
                                                <i class="bi bi-pencil-square me-1"></i> Update
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                                        No pending appointments found in queue.
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Children -->
            <div class="col-12 col-xl-4">
                <div class="card-vax h-100">
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-person-hearts text-primary fs-5"></i>
                            <h6 class="fw-bold mb-0" style="color: var(--vax-text-main);">
                                Recent Children
                            </h6>
                        </div>
                        <a href="appointments.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3" style="font-size: 0.775rem;">
                            View All
                        </a>
                    </div>

                    <div class="p-3 d-flex flex-column gap-3">
                    <?php if (count($recent_children) > 0): ?>
                        <?php foreach ($recent_children as $child): ?>
                            <?php
                            $ageText = 'Age not available';
                            if (!empty($child['date_of_birth'])) {
                                $dob = new DateTime($child['date_of_birth']);
                                $diff = (new DateTime())->diff($dob);
                                if ($diff->y > 0) {
                                    $ageText = 'Age: ' . $diff->y . ' yr';
                                } elseif ($diff->m > 0) {
                                    $ageText = 'Age: ' . $diff->m . ' mos';
                                } else {
                                    $ageText = 'Age: ' . $diff->d . ' days';
                                }
                            }
                            ?>
                            <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between bg-body-tertiary">
                                <div>
                                    <h6 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($child['child_name'] ?? 'Unknown Child'); ?></h6>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($child['gender'] ?? 'N/A'); ?> • <?php echo $ageText; ?>
                                    </small>
                                </div>
                                <a href="appointments.php" class="btn btn-sm btn-light border rounded px-3" style="font-size: 0.75rem;">
                                    Profile
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-person-x fs-3 d-block mb-2"></i>
                            No children registered yet.
                        </div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Navigation Cards -->
        <div class="row g-3">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="quick-nav-card">
                    <i class="bi bi-calendar2-week text-primary quick-nav-icon"></i>
                    <div class="quick-nav-title">Date of Vaccination</div>
                    <div class="quick-nav-subtitle">Upcoming vaccination dates of all children</div>
                    <a href="appointments.php" class="quick-nav-btn">Open Schedule</a>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="quick-nav-card">
                    <i class="bi bi-eyedropper text-success quick-nav-icon"></i>
                    <div class="quick-nav-title">List of Vaccine</div>
                    <div class="quick-nav-subtitle">View catalog & toggle availability</div>
                    <a href="vaccines.php" class="quick-nav-btn">View Vaccines</a>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="quick-nav-card">
                    <i class="bi bi-hospital text-info quick-nav-icon"></i>
                    <div class="quick-nav-title">Hospitals Directory</div>
                    <div class="quick-nav-subtitle">View facility license & clinic location</div>
                    <a href="profile.php" class="quick-nav-btn">Manage Hospital</a>
                </div>
            </div>

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

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
</div>

<!-- Modal: Update Child Vaccination Status -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <form action="appointments.php" method="POST">
                <input type="hidden" name="action" value="update_vaccine_status">
                <input type="hidden" name="booking_id" id="statusBookingId" value="">

                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="updateStatusModalLabel">
                            <i class="bi bi-shield-check text-primary me-2"></i>
                            Update Child Vaccination Status
                        </h5>
                        <small class="text-muted">
                            Booking Reference: <span id="modalBookingIdDisplay" class="fw-bold text-primary">#</span>
                        </small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Patient Summary -->
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

                    <!-- Outcome Options -->
                    <label class="form-label fw-bold mb-2">
                        Vaccine Administration Status <span class="text-danger">*</span>
                    </label>

                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-6">
                            <div class="form-check p-3 border rounded-3 h-100 bg-white shadow-sm position-relative">
                                <input class="form-check-input ms-0 me-2" type="radio" name="vaccine_status" id="statusVaccinated" value="vaccinated" checked>
                                <label class="form-check-label fw-bold text-success stretched-link" for="statusVaccinated">
                                    <i class="bi bi-check-circle-fill me-1"></i> Vaccinated (Completed)
                                </label>
                                <div class="text-muted small mt-1 ms-4">
                                    Child attended hospital and vaccine dose was successfully administered.
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-check p-3 border rounded-3 h-100 bg-white shadow-sm position-relative">
                                <input class="form-check-input ms-0 me-2" type="radio" name="vaccine_status" id="statusNotVaccinated" value="not_vaccinated">
                                <label class="form-check-label fw-bold text-danger stretched-link" for="statusNotVaccinated">
                                    <i class="bi bi-x-circle-fill me-1"></i> Not Vaccinated (Missed)
                                </label>
                                <div class="text-muted small mt-1 ms-4">
                                    Child did not attend, was unwell, or dose was postponed.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vaccinated Details -->
                    <div id="vaccinatedDetailsSection" class="p-3 rounded-3 border bg-light mb-3">
                        <h6 class="fw-bold text-success mb-2">
                            <i class="bi bi-clipboard2-pulse me-1"></i> Dose Administration Details
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Clinical Observations & Parent Advice</label>
                                <input type="text" name="admin_notes" class="form-control form-control-sm" placeholder="e.g. Well tolerated, advised to monitor temperature for 24h">
                            </div>
                        </div>
                    </div>

                    <!-- Not Vaccinated Details -->
                    <div id="notVaccinatedReasonSection" class="p-3 rounded-3 border bg-light mb-3 d-none">
                        <h6 class="fw-bold text-danger mb-2">
                            <i class="bi bi-exclamation-triangle me-1"></i> Non-Administration Reason
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Reason for Absence or Postponement <span class="text-danger">*</span></label>
                                <select name="reason" class="form-select form-select-sm">
                                    <option value="absent">Parent / Child Absent (No Show)</option>
                                    <option value="unwell">Child Unwell / High Fever</option>
                                    <option value="rescheduled">Parent Requested Reschedule</option>
                                    <option value="stock_out">Vaccine Temporarily Out of Stock</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Follow-Up Instructions for Parent</label>
                                <input type="text" name="followup_notes" class="form-control form-control-sm" placeholder="e.g. Advised to return next Wednesday">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('updateStatusModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            const bookingId = button.getAttribute('data-booking-id') || '';
            const childName = button.getAttribute('data-child-name') || '';
            const parent    = button.getAttribute('data-parent') || '';
            const vaccine   = button.getAttribute('data-vaccine') || '';

            document.getElementById('statusBookingId').value = bookingId;
            document.getElementById('modalBookingIdDisplay').textContent = '#' + bookingId;
            document.getElementById('modalChildName').textContent = childName;
            document.getElementById('modalParentName').textContent = parent;
            document.getElementById('modalVaccineName').textContent = vaccine;
            document.getElementById('statusVaccinated').checked = true;

            updateStatusSections();
        });
    }

    const vaccinatedRadio = document.getElementById('statusVaccinated');
    const notVaccinatedRadio = document.getElementById('statusNotVaccinated');

    function updateStatusSections() {
        const vaccinatedSection = document.getElementById('vaccinatedDetailsSection');
        const notVaccinatedSection = document.getElementById('notVaccinatedReasonSection');

        if (vaccinatedRadio && vaccinatedRadio.checked) {
            if (vaccinatedSection) vaccinatedSection.classList.remove('d-none');
            if (notVaccinatedSection) notVaccinatedSection.classList.add('d-none');
        } else {
            if (vaccinatedSection) vaccinatedSection.classList.add('d-none');
            if (notVaccinatedSection) notVaccinatedSection.classList.remove('d-none');
        }
    }

    if (vaccinatedRadio) vaccinatedRadio.addEventListener('change', updateStatusSections);
    if (notVaccinatedRadio) notVaccinatedRadio.addEventListener('change', updateStatusSections);
});
</script>