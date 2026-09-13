<?php
/**
 * Appointments Queue & Status Verification
 * VaxCare Hospital Portal
 */

require_once __DIR__ . '/db.php';

$message = '';
$messageType = '';

/*  1. UPDATE VACCINATION STATUS (POST) */
  
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_vaccine_status') {
    $booking_id     = intval($_POST['booking_id'] ?? 0);
    $vaccine_status = $_POST['vaccine_status'] ?? '';

    if ($booking_id <= 0) {
        $message = 'Invalid booking reference ID.';
        $messageType = 'danger';
    } elseif (!in_array($vaccine_status, ['vaccinated', 'not_vaccinated'], true)) {
        $message = 'Invalid vaccination status selected.';
        $messageType = 'danger';
    } else {
        // Retrieve booking details
        $stmt = mysqli_prepare($connection, "SELECT child_id, hospital_id FROM bookings WHERE booking_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $booking_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $booking = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);

        if (!$booking) {
            $message = 'Booking record was not found.';
            $messageType = 'danger';
        } else {
            $child_id = (int) $booking['child_id'];

            if ($vaccine_status === 'vaccinated') {
                $recordStatus  = 'Vaccinated';
                $bookingStatus = 'Completed';
            } else {
                $recordStatus  = 'Not Vaccinated';
                $bookingStatus = 'Rejected';
            }

            $vaccination_date = date('Y-m-d');
            $remarks = trim($_POST['admin_notes'] ?? '');

            if ($vaccine_status === 'not_vaccinated') {
                $reason   = trim($_POST['reason'] ?? '');
                $followup = trim($_POST['followup_notes'] ?? '');

                if ($reason !== '') {
                    $remarks = 'Reason: ' . ucfirst(str_replace('_', ' ', $reason));
                }
                if ($followup !== '') {
                    $remarks .= ($remarks !== '' ? ' | ' : '') . 'Follow-up: ' . $followup;
                }
            }

            // Check if vaccination record already exists for this booking
            $checkStmt = mysqli_prepare($connection, "SELECT record_id FROM vaccination_records WHERE booking_id = ? LIMIT 1");
            mysqli_stmt_bind_param($checkStmt, "i", $booking_id);
            mysqli_stmt_execute($checkStmt);
            $existing = mysqli_fetch_assoc(mysqli_stmt_get_result($checkStmt));
            mysqli_stmt_close($checkStmt);

            if ($existing) {
                // Update existing record
                $recStmt = mysqli_prepare(
                    $connection,
                    "UPDATE vaccination_records SET vaccination_date = ?, status = ?, remarks = ? WHERE booking_id = ?"
                );
                mysqli_stmt_bind_param($recStmt, "sssi", $vaccination_date, $recordStatus, $remarks, $booking_id);
                $recSuccess = mysqli_stmt_execute($recStmt);
                mysqli_stmt_close($recStmt);
            } else {
                // Insert new record
                $recStmt = mysqli_prepare(
                    $connection,
                    "INSERT INTO vaccination_records (booking_id, child_id, vaccination_date, status, remarks)
                     VALUES (?, ?, ?, ?, ?)"
                );
                mysqli_stmt_bind_param($recStmt, "iisss", $booking_id, $child_id, $vaccination_date, $recordStatus, $remarks);
                $recSuccess = mysqli_stmt_execute($recStmt);
                mysqli_stmt_close($recStmt);
            }

            if ($recSuccess) {
                // Update booking status
                $bkStmt = mysqli_prepare($connection, "UPDATE bookings SET status = ?, approval_date = CURDATE() WHERE booking_id = ?");
                mysqli_stmt_bind_param($bkStmt, "si", $bookingStatus, $booking_id);
                $bkSuccess = mysqli_stmt_execute($bkStmt);
                mysqli_stmt_close($bkStmt);

                $message = 'Appointment status and vaccination record successfully updated.';
                $messageType = 'success';
            } else {
                $message = 'Error saving vaccination outcome: ' . mysqli_error($connection);
                $messageType = 'danger';
            }
        }
    }
}

/* =========================================================
   2. FILTER & QUERY APPOINTMENTS
   ========================================================= */
$filter = $_GET['filter'] ?? 'all';

$whereClause = "";
if ($filter === 'pending') {
    $whereClause = "WHERE b.status = 'Pending'";
} elseif ($filter === 'completed') {
    $whereClause = "WHERE b.status = 'Completed'";
}

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
        v.description AS vaccine_description,
        v.age_group,
        v.stock_status,
        h.hospital_name
    FROM bookings b
    LEFT JOIN children c ON b.child_id = c.child_id
    LEFT JOIN parents p ON c.parent_id = p.parent_id
    LEFT JOIN vaccines v ON b.vaccine_id = v.vaccine_id
    LEFT JOIN hospitals h ON b.hospital_id = h.hospital_id
    {$whereClause}
    ORDER BY b.appointment_date ASC, b.booking_id DESC
";

$appointmentResult = mysqli_query($connection, $appointmentQuery);
$appointments = [];
if ($appointmentResult) {
    while ($row = mysqli_fetch_assoc($appointmentResult)) {
        $appointments[] = $row;
    }
}

// Pending appointments count
$pendingRes = mysqli_query($connection, "SELECT COUNT(*) AS total FROM bookings WHERE status = 'Pending'");
$pendingCount = 0;
if ($pendingRes && ($pRow = mysqli_fetch_assoc($pendingRes))) {
    $pendingCount = (int) $pRow['total'];
}

$pageTitle = 'Appointments Queue - VaxCare Hospital Portal';
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
                    <div class="p-2 rounded-2" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">
                        <i class="bi bi-calendar-check-fill fs-5"></i>
                    </div>
                    <h4 class="fw-bold mb-0" style="color: var(--vax-text-main);">
                        Child Appointments Queue
                    </h4>
                </div>
                <div class="small" style="color: var(--vax-text-muted);">
                    Dashboard / <span class="text-secondary">Hospital Appointments Verification & Status</span>
                </div>
            </div>

            <!-- Action Buttons / Filter Links -->
            <div class="d-flex flex-wrap gap-2">
                <a href="appointments.php?filter=pending" class="btn btn-sm rounded-2 text-dark shadow-sm d-flex align-items-center gap-1 <?php echo ($filter === 'pending') ? 'btn-warning fw-bold' : 'btn-light border'; ?>" style="<?php echo ($filter === 'pending') ? 'background-color: #f59e0b; color: #fff !important;' : ''; ?>">
                    <i class="bi bi-clock-history"></i>
                    <span>Pending Queue (<?php echo $pendingCount; ?>)</span>
                </a>

                <a href="appointments.php" class="btn btn-sm rounded-2 <?php echo ($filter === 'all') ? 'btn-primary' : 'btn-outline-primary'; ?> d-flex align-items-center gap-1">
                    <i class="bi bi-list-ul"></i>
                    <span>All Appointments</span>
                </a>
            </div>
        </div>

        <!-- Alert Notification -->
        <?php if ($message !== ''): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi <?php echo ($messageType === 'success') ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-danger'; ?>"></i>
                    <div><?php echo htmlspecialchars($message); ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Appointments Table Card -->
        <div class="card-vax p-3">
            <div class="table-responsive">
                <table class="table-vax">
                    <thead>
                        <tr>
                            <th>BOOKING REF</th>
                            <th>CHILD PROFILE</th>
                            <th>VACCINE</th>
                            <th>TARGET HOSPITAL</th>
                            <th>APPT DATE</th>
                            <th>STATUS</th>
                            <th class="text-end">ACTION</th>
                        </tr>
                    </thead>
                    <tbody id="vaxcareTableBody">
                    <?php if (count($appointments) > 0): ?>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <!-- Booking Ref -->
                                <td>
                                    <span class="booking-ref">#<?php echo $appointment['booking_id']; ?></span>
                                    <div class="text-muted" style="font-size: 0.725rem;">
                                        Booked: <?php echo !empty($appointment['booking_date']) ? date('d M Y', strtotime($appointment['booking_date'])) : 'N/A'; ?>
                                    </div>
                                </td>

                                <!-- Child & Parent -->
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($appointment['child_name'] ?? 'Unknown Child'); ?></div>
                                    <small class="text-muted">
                                        Parent: <?php echo htmlspecialchars($appointment['parent_name'] ?? 'N/A'); ?>
                                        <?php if (!empty($appointment['parent_phone'])): ?>
                                            (<?php echo htmlspecialchars($appointment['parent_phone']); ?>)
                                        <?php endif; ?>
                                    </small>
                                </td>

                                <!-- Vaccine -->
                                <td>
                                    <span class="vaccine-badge-purple"><?php echo htmlspecialchars($appointment['vaccine_name'] ?? 'N/A'); ?></span>
                                    <div class="text-muted" style="font-size: 0.725rem;">
                                        <?php echo htmlspecialchars($appointment['age_group'] ?? 'N/A'); ?>
                                    </div>
                                </td>

                                <!-- Hospital -->
                                <td>
                                    <div class="text-dark small">
                                        <i class="bi bi-hospital me-1 text-muted"></i>
                                        <?php echo htmlspecialchars($appointment['hospital_name'] ?? 'N/A'); ?>
                                    </div>
                                </td>

                                <!-- Appointment Date -->
                                <td>
                                    <?php if (!empty($appointment['appointment_date'])): ?>
                                        <span class="text-dark fw-semibold small"><?php echo date('d M Y', strtotime($appointment['appointment_date'])); ?></span>
                                        <div class="text-muted" style="font-size: 0.725rem;">
                                            <?php echo date('h:i A', strtotime($appointment['appointment_date'])); ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">Not scheduled</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Status -->
                                <td>
                                    <?php
                                    $status = $appointment['status'] ?? 'Pending';
                                    if ($status === 'Pending') {
                                        echo '<span class="status-pill status-pill-pending"><i class="bi bi-clock-fill me-1"></i> Pending</span>';
                                    } elseif ($status === 'Approved') {
                                        echo '<span class="status-pill status-pill-approved"><i class="bi bi-check-circle-fill me-1"></i> Approved</span>';
                                    } elseif ($status === 'Completed') {
                                        echo '<span class="status-pill status-pill-vaccinated"><i class="bi bi-check-circle-fill me-1"></i> Completed</span>';
                                    } elseif ($status === 'Rejected') {
                                        echo '<span class="status-pill status-pill-rejected"><i class="bi bi-x-circle-fill me-1"></i> Rejected</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>';
                                    }
                                    ?>
                                </td>

                                <!-- Actions -->
                                <td class="text-end">
                                    <?php if ($status === 'Completed'): ?>
                                        <button type="button" class="btn btn-sm btn-light border rounded px-3"
                                                data-bs-toggle="modal"
                                                data-bs-target="#updateStatusModal"
                                                data-booking-id="<?php echo $appointment['booking_id']; ?>"
                                                data-child-name="<?php echo htmlspecialchars($appointment['child_name'] ?? '', ENT_QUOTES); ?>"
                                                data-parent="<?php echo htmlspecialchars(($appointment['parent_name'] ?? '') . ' (' . ($appointment['parent_phone'] ?? '') . ')', ENT_QUOTES); ?>"
                                                data-vaccine="<?php echo htmlspecialchars($appointment['vaccine_name'] ?? '', ENT_QUOTES); ?>"
                                                data-current-status="vaccinated">
                                            <i class="bi bi-pencil me-1"></i> Edit Status
                                        </button>
                                    <?php else: ?>
                                        <div class="d-inline-flex flex-column flex-sm-row gap-1">
                                            <button type="button" class="btn-vax-approve"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#updateStatusModal"
                                                    data-booking-id="<?php echo $appointment['booking_id']; ?>"
                                                    data-child-name="<?php echo htmlspecialchars($appointment['child_name'] ?? '', ENT_QUOTES); ?>"
                                                    data-parent="<?php echo htmlspecialchars(($appointment['parent_name'] ?? '') . ' (' . ($appointment['parent_phone'] ?? '') . ')', ENT_QUOTES); ?>"
                                                    data-vaccine="<?php echo htmlspecialchars($appointment['vaccine_name'] ?? '', ENT_QUOTES); ?>"
                                                    data-current-status="vaccinated">
                                                <i class="bi bi-check2 me-1"></i> Vaccinated
                                            </button>

                                            <button type="button" class="btn-vax-reject"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#updateStatusModal"
                                                    data-booking-id="<?php echo $appointment['booking_id']; ?>"
                                                    data-child-name="<?php echo htmlspecialchars($appointment['child_name'] ?? '', ENT_QUOTES); ?>"
                                                    data-parent="<?php echo htmlspecialchars(($appointment['parent_name'] ?? '') . ' (' . ($appointment['parent_phone'] ?? '') . ')', ENT_QUOTES); ?>"
                                                    data-vaccine="<?php echo htmlspecialchars($appointment['vaccine_name'] ?? '', ENT_QUOTES); ?>"
                                                    data-current-status="not_vaccinated">
                                                <i class="bi bi-x-lg me-1"></i> Missed
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                                No appointments found in database matching criteria.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination Indicator -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center pt-3 mt-2 border-top gap-2">
                <span class="text-muted small">
                    Showing <strong><?php echo count($appointments); ?></strong> record(s)
                </span>
                <span class="badge bg-light text-secondary border">Queue Sync: Live</span>
            </div>
        </div>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
</div>

<!-- Modal: Update Vaccination Status -->
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

                    <!-- Status Selection -->
                    <label class="form-label fw-bold mb-2">
                        Vaccine Administration Outcome <span class="text-danger">*</span>
                    </label>

                    <div class="row g-3 mb-4">
                        <!-- Vaccinated -->
                        <div class="col-12 col-md-6">
                            <div class="form-check p-3 border rounded-3 h-100 bg-white shadow-sm position-relative">
                                <input class="form-check-input ms-0 me-2" type="radio" name="vaccine_status" id="statusVaccinated" value="vaccinated" checked>
                                <label class="form-check-label fw-bold text-success stretched-link" for="statusVaccinated">
                                    <i class="bi bi-check-circle-fill me-1"></i> Vaccinated (Completed)
                                </label>
                                <div class="text-muted small mt-1 ms-4">
                                    Child attended clinic and dose was successfully administered.
                                </div>
                            </div>
                        </div>

                        <!-- Not Vaccinated -->
                        <div class="col-12 col-md-6">
                            <div class="form-check p-3 border rounded-3 h-100 bg-white shadow-sm position-relative">
                                <input class="form-check-input ms-0 me-2" type="radio" name="vaccine_status" id="statusNotVaccinated" value="not_vaccinated">
                                <label class="form-check-label fw-bold text-danger stretched-link" for="statusNotVaccinated">
                                    <i class="bi bi-x-circle-fill me-1"></i> Not Vaccinated (Missed)
                                </label>
                                <div class="text-muted small mt-1 ms-4">
                                    Child did not attend, was unwell, or dosage was postponed.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vaccinated Observation Details -->
                    <div id="vaccinatedDetailsSection" class="p-3 rounded-3 border bg-light mb-3">
                        <h6 class="fw-bold text-success mb-2">
                            <i class="bi bi-clipboard2-pulse me-1"></i> Clinical Observations
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Parent Advice & Clinical Remarks</label>
                                <input type="text" name="admin_notes" class="form-control form-control-sm" placeholder="e.g. Well tolerated, advised to monitor temperature for 24h">
                            </div>
                        </div>
                    </div>

                    <!-- Missed Dose Details -->
                    <div id="notVaccinatedReasonSection" class="p-3 rounded-3 border bg-light mb-3 d-none">
                        <h6 class="fw-bold text-danger mb-2">
                            <i class="bi bi-exclamation-triangle me-1"></i> Reason for Absence / Cancellation
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Reason <span class="text-danger">*</span></label>
                                <select name="reason" class="form-select form-select-sm">
                                    <option value="absent">Parent / Child Absent (No Show)</option>
                                    <option value="unwell">Child Unwell / High Fever</option>
                                    <option value="rescheduled">Parent Requested Reschedule</option>
                                    <option value="stock_out">Vaccine Temporarily Out of Stock</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold">Follow-Up Instructions</label>
                                <input type="text" name="followup_notes" class="form-control form-control-sm" placeholder="e.g. Advised to return next week">
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

            const bookingId     = button.getAttribute('data-booking-id') || '';
            const childName     = button.getAttribute('data-child-name') || '';
            const parent        = button.getAttribute('data-parent') || '';
            const vaccine       = button.getAttribute('data-vaccine') || '';
            const currentStatus = button.getAttribute('data-current-status') || 'vaccinated';

            document.getElementById('statusBookingId').value = bookingId;
            document.getElementById('modalBookingIdDisplay').textContent = '#' + bookingId;
            document.getElementById('modalChildName').textContent = childName;
            document.getElementById('modalParentName').textContent = parent;
            document.getElementById('modalVaccineName').textContent = vaccine;

            if (currentStatus === 'not_vaccinated') {
                document.getElementById('statusNotVaccinated').checked = true;
            } else {
                document.getElementById('statusVaccinated').checked = true;
            }
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