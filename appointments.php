<?php

require_once __DIR__ . '/db.php';

$message = '';
$messageType = '';

/* =========================================================
   UPDATE VACCINATION STATUS
   ========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'])
    && $_POST['action'] === 'update_vaccine_status') {

    $booking_id = intval($_POST['booking_id'] ?? 0);
    $vaccine_status = $_POST['vaccine_status'] ?? '';

    if ($booking_id <= 0) {

        $message = 'Invalid booking ID.';
        $messageType = 'danger';

    } elseif (!in_array($vaccine_status, ['vaccinated', 'not_vaccinated'], true)) {

        $message = 'Invalid vaccination status.';
        $messageType = 'danger';

    } else {

        /* Get child ID from booking */
        $stmt = mysqli_prepare(
            $connection,
            "SELECT child_id FROM bookings WHERE booking_id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $booking_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $booking = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);


        if (!$booking) {

            $message = 'Booking not found.';
            $messageType = 'danger';

        } else {

            $child_id = (int)$booking['child_id'];

            /* Convert form status to database status */
            if ($vaccine_status === 'vaccinated') {

                $recordStatus = 'Vaccinated';
                $bookingStatus = 'Completed';

            } else {

                $recordStatus = 'Not Vaccinated';
                $bookingStatus = 'Rejected';

            }


            $vaccination_date = date('Y-m-d');

            /* Remarks */
            $remarks = trim($_POST['admin_notes'] ?? '');

            if ($vaccine_status === 'not_vaccinated') {

                $reason = trim($_POST['reason'] ?? '');
                $followup = trim($_POST['followup_notes'] ?? '');

                if ($reason !== '') {
                    $remarks = 'Reason: ' . $reason;
                }

                if ($followup !== '') {

                    if ($remarks !== '') {
                        $remarks .= ' | ';
                    }

                    $remarks .= 'Follow-up: ' . $followup;
                }
            }


            /* Check existing vaccination record */
            $stmt = mysqli_prepare(
                $connection,
                "SELECT record_id
                 FROM vaccination_records
                 WHERE booking_id = ?
                 LIMIT 1"
            );

            mysqli_stmt_bind_param($stmt, "i", $booking_id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $existingRecord = mysqli_fetch_assoc($result);

            mysqli_stmt_close($stmt);


            /* UPDATE existing record */
            if ($existingRecord) {

                $stmt = mysqli_prepare(
                    $connection,
                    "UPDATE vaccination_records
                     SET vaccination_date = ?,
                         status = ?,
                         remarks = ?
                     WHERE booking_id = ?"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "sssi",
                    $vaccination_date,
                    $recordStatus,
                    $remarks,
                    $booking_id
                );

                $success = mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);

            }

            /* INSERT new record */
            else {

                $stmt = mysqli_prepare(
                    $connection,
                    "INSERT INTO vaccination_records
                    (booking_id, child_id, vaccination_date, status, remarks)
                    VALUES (?, ?, ?, ?, ?)"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "iisss",
                    $booking_id,
                    $child_id,
                    $vaccination_date,
                    $recordStatus,
                    $remarks
                );

                $success = mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);
            }


            /* Update booking status */
            if ($success) {

                $stmt = mysqli_prepare(
                    $connection,
                    "UPDATE bookings
                     SET status = ?,
                         approval_date = CURDATE()
                     WHERE booking_id = ?"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "si",
                    $bookingStatus,
                    $booking_id
                );

                $bookingUpdated = mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);


                if ($bookingUpdated) {

                    $message = 'Appointment status updated successfully.';
                    $messageType = 'success';

                } else {

                    $message = 'Vaccination record saved, but booking status could not be updated.';
                    $messageType = 'warning';

                }

            } else {

                $message = 'Error saving vaccination record: '
                         . mysqli_error($connection);

                $messageType = 'danger';
            }
        }
    }
}


/* =========================================================
   FILTER
   ========================================================= */

$filter = $_GET['filter'] ?? 'all';


/* =========================================================
   GET APPOINTMENTS
   ========================================================= */

if ($filter === 'pending') {

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

        LEFT JOIN children c
            ON b.child_id = c.child_id

        LEFT JOIN parents p
            ON c.parent_id = p.parent_id

        LEFT JOIN vaccines v
            ON b.vaccine_id = v.vaccine_id

        LEFT JOIN hospitals h
            ON b.hospital_id = h.hospital_id

        WHERE b.status = 'Pending'

        ORDER BY b.appointment_date ASC
    ";

} else {

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

        LEFT JOIN children c
            ON b.child_id = c.child_id

        LEFT JOIN parents p
            ON c.parent_id = p.parent_id

        LEFT JOIN vaccines v
            ON b.vaccine_id = v.vaccine_id

        LEFT JOIN hospitals h
            ON b.hospital_id = h.hospital_id

        ORDER BY b.appointment_date DESC
    ";
}


$appointmentResult = mysqli_query(
    $connection,
    $appointmentQuery
);

if (!$appointmentResult) {
    die("Database Error: " . mysqli_error($connection));
}


$appointments = [];

while ($row = mysqli_fetch_assoc($appointmentResult)) {
    $appointments[] = $row;
}


/* =========================================================
   PENDING COUNT
   ========================================================= */

$pendingQuery = "
    SELECT COUNT(*) AS total
    FROM bookings
    WHERE status = 'Pending'
";

$pendingResult = mysqli_query($connection, $pendingQuery);
$pendingData = mysqli_fetch_assoc($pendingResult);

$pendingCount = (int)$pendingData['total'];


/* =========================================================
   TOTAL APPOINTMENTS
   ========================================================= */

$totalAppointments = count($appointments);


/* PAGE TITLE */

$pageTitle = 'Appointments Queue - VaxCare Hospital Portal';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';

?>


<div class="main-wrapper">

    <?php require_once __DIR__ . '/includes/navbar.php'; ?>


    <main class="p-3 p-md-4">


        <!-- =====================================================
             PAGE HEADER
             ===================================================== -->

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">

            <div>

                <div class="d-flex align-items-center gap-2 mb-1">

                    <div class="p-2 rounded-2"
                         style="background: rgba(245, 158, 11, 0.15); color: #f59e0b;">

                        <i class="bi bi-calendar-check-fill fs-5"></i>

                    </div>

                    <h4 class="fw-bold mb-0"
                        style="color: var(--vax-text-main);">

                        Child Appointments Queue

                    </h4>

                </div>


                <div class="small"
                     style="color: var(--vax-text-muted);">

                    Dashboard /

                    <span class="text-secondary">

                        Hospital Appointments Verification & Status

                    </span>

                </div>

            </div>


            <!-- BUTTONS -->

            <div class="d-flex gap-2">

                <a href="appointments.php?filter=pending"
                   class="btn btn-sm rounded-2 text-white shadow-sm d-flex align-items-center gap-1"
                   style="background-color: #f59e0b; font-size: 0.8rem; font-weight: 600;">

                    <i class="bi bi-clock-history"></i>

                    <span>
                        Pending Queue (<?php echo $pendingCount; ?>)
                    </span>

                </a>


                <a href="appointments.php"
                   class="btn btn-sm rounded-2 btn-outline-primary d-flex align-items-center gap-1"
                   style="font-size: 0.8rem; font-weight: 600;">

                    <i class="bi bi-list-ul"></i>

                    <span>
                        All Appointment History
                    </span>

                </a>

            </div>

        </div>


        <!-- =====================================================
             SUCCESS / ERROR MESSAGE
             ===================================================== -->

        <?php if ($message !== ''): ?>

            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show"
                 role="alert">

                <?php echo htmlspecialchars($message); ?>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert">
                </button>

            </div>

        <?php endif; ?>


        <!-- =====================================================
             SECTION TITLE
             ===================================================== -->

        <h6 class="fw-bold text-dark mt-4 mb-3"
            style="font-size: 1.05rem;">

            <?php if ($filter === 'pending'): ?>

                Pending Child Appointments Requiring Hospital Action

            <?php else: ?>

                All Child Appointments

            <?php endif; ?>

        </h6>


        <!-- =====================================================
             TABLE CARD
             ===================================================== -->

        <div class="card-vax p-3">


            <!-- SEARCH -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-3">

                <div class="d-flex align-items-center gap-2 small text-muted">

                    <span>Show</span>

                    <select class="form-select form-select-sm"
                            style="width: 70px;">

                        <option value="10" selected>
                            10
                        </option>

                        <option value="25">
                            25
                        </option>

                        <option value="50">
                            50
                        </option>

                    </select>

                    <span>
                        entries
                    </span>

                </div>


                <div class="d-flex align-items-center gap-2">

                    <input type="search"
                           id="tableSearchInput"
                           class="form-control form-control-sm"
                           placeholder="Search records..."
                           style="max-width: 240px;">

                </div>

            </div>


            <!-- =================================================
                 APPOINTMENT TABLE
                 ================================================= -->

            <div class="table-responsive">

                <table class="table-vax"
                       id="appointmentsTable">

                    <thead>

                        <tr>

                            <th>
                                BOOKING REF
                                <i class="bi bi-arrow-down-up text-muted ms-1"
                                   style="font-size: 0.65rem;"></i>
                            </th>

                            <th>
                                CHILD PROFILE
                                <i class="bi bi-arrow-down-up text-muted ms-1"
                                   style="font-size: 0.65rem;"></i>
                            </th>

                            <th>
                                REQUESTED VACCINE
                                <i class="bi bi-arrow-down-up text-muted ms-1"
                                   style="font-size: 0.65rem;"></i>
                            </th>

                            <th>
                                TARGET HOSPITAL
                                <i class="bi bi-arrow-down-up text-muted ms-1"
                                   style="font-size: 0.65rem;"></i>
                            </th>

                            <th>
                                REQUESTED APPT DATE
                                <i class="bi bi-arrow-down-up text-muted ms-1"
                                   style="font-size: 0.65rem;"></i>
                            </th>

                            <th>
                                STATUS & APPROVAL
                                <i class="bi bi-arrow-down-up text-muted ms-1"
                                   style="font-size: 0.65rem;"></i>
                            </th>

                            <th class="text-end">
                                ACTION / DECISION
                            </th>

                        </tr>

                    </thead>


                    <tbody id="vaxcareTableBody">


                    <?php if (count($appointments) > 0): ?>


                        <?php foreach ($appointments as $appointment): ?>


                            <tr>


                                <!-- BOOKING REF -->

                                <td>

                                    <span class="booking-ref">

                                        #<?php
                                        echo $appointment['booking_id'];
                                        ?>

                                    </span>


                                    <div class="text-muted"
                                         style="font-size: 0.725rem;">

                                        Booked:

                                        <?php

                                        if (!empty($appointment['booking_date'])) {

                                            echo date(
                                                'd M Y',
                                                strtotime(
                                                    $appointment['booking_date']
                                                )
                                            );

                                        } else {

                                            echo 'N/A';

                                        }

                                        ?>

                                    </div>

                                </td>


                                <!-- CHILD -->

                                <td>

                                    <div class="fw-bold text-dark">

                                        <?php

                                        echo htmlspecialchars(
                                            $appointment['child_name']
                                            ?? 'Unknown Child'
                                        );

                                        ?>

                                    </div>


                                    <small class="text-muted">

                                        Parent:

                                        <?php

                                        echo htmlspecialchars(
                                            $appointment['parent_name']
                                            ?? 'N/A'
                                        );

                                        ?>


                                        <?php
                                        if (!empty($appointment['parent_phone'])):
                                        ?>

                                            (

                                            <?php

                                            echo htmlspecialchars(
                                                $appointment['parent_phone']
                                            );

                                            ?>

                                            )

                                        <?php endif; ?>

                                    </small>

                                </td>


                                <!-- VACCINE -->

                                <td>

                                    <span class="vaccine-badge-purple">

                                        <?php

                                        echo htmlspecialchars(
                                            $appointment['vaccine_name']
                                            ?? 'N/A'
                                        );

                                        ?>

                                    </span>


                                    <div class="text-muted"
                                         style="font-size: 0.725rem;">

                                        <?php

                                        echo htmlspecialchars(
                                            $appointment['age_group']
                                            ?? 'N/A'
                                        );

                                        ?>

                                    </div>

                                </td>


                                <!-- HOSPITAL -->

                                <td>

                                    <div class="text-dark small">

                                        <i class="bi bi-hospital me-1 text-muted"></i>

                                        <?php

                                        echo htmlspecialchars(
                                            $appointment['hospital_name']
                                            ?? 'N/A'
                                        );

                                        ?>

                                    </div>

                                </td>


                                <!-- APPOINTMENT DATE -->

                                <td>

                                    <?php
                                    if (!empty($appointment['appointment_date'])):
                                    ?>

                                        <span class="text-dark fw-semibold small">

                                            <?php

                                            echo date(
                                                'd M Y',
                                                strtotime(
                                                    $appointment['appointment_date']
                                                )
                                            );

                                            ?>

                                        </span>


                                        <div class="text-muted"
                                             style="font-size: 0.725rem;">

                                            <?php

                                            echo date(
                                                'h:i A',
                                                strtotime(
                                                    $appointment['appointment_date']
                                                )
                                            );

                                            ?>

                                        </div>


                                    <?php else: ?>


                                        <span class="text-muted">

                                            Not scheduled

                                        </span>


                                    <?php endif; ?>

                                </td>


                                <!-- STATUS -->

                                <td>

                                    <?php
                                    $status = $appointment['status']
                                              ?? 'Pending';
                                    ?>


                                    <?php if ($status === 'Pending'): ?>

                                        <span class="status-pill status-pill-pending">

                                            <i class="bi bi-clock-fill"></i>

                                            Pending

                                        </span>


                                    <?php elseif ($status === 'Approved'): ?>

                                        <span class="status-pill status-pill-approved">

                                            <i class="bi bi-check-circle-fill"></i>

                                            Approved

                                        </span>


                                    <?php elseif ($status === 'Completed'): ?>

                                        <span class="status-pill status-pill-vaccinated">

                                            <i class="bi bi-check-circle-fill"></i>

                                            Completed

                                        </span>


                                    <?php elseif ($status === 'Rejected'): ?>

                                        <span class="status-pill status-pill-rejected">

                                            <i class="bi bi-x-circle-fill"></i>

                                            Rejected

                                        </span>


                                    <?php else: ?>

                                        <span class="badge bg-secondary">

                                            <?php
                                            echo htmlspecialchars($status);
                                            ?>

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- ACTION -->

                                <td class="text-end">


                                    <?php if ($status === 'Completed'): ?>


                                        <button type="button"
                                                class="btn btn-sm btn-light border rounded px-3"

                                                data-bs-toggle="modal"
                                                data-bs-target="#updateStatusModal"

                                                data-booking-id="<?php
                                                echo $appointment['booking_id'];
                                                ?>"

                                                data-child-name="<?php
                                                echo htmlspecialchars(
                                                    $appointment['child_name']
                                                    ?? '',
                                                    ENT_QUOTES
                                                );
                                                ?>"

                                                data-parent="<?php
                                                echo htmlspecialchars(
                                                    ($appointment['parent_name']
                                                    ?? '')
                                                    . ' (' .
                                                    ($appointment['parent_phone']
                                                    ?? '')
                                                    . ')',
                                                    ENT_QUOTES
                                                );
                                                ?>"

                                                data-vaccine="<?php
                                                echo htmlspecialchars(
                                                    $appointment['vaccine_name']
                                                    ?? '',
                                                    ENT_QUOTES
                                                );
                                                ?>"

                                                data-current-status="vaccinated">


                                            <i class="bi bi-pencil me-1"></i>

                                            Edit Status


                                        </button>


                                    <?php else: ?>


                                        <div class="d-inline-flex flex-column flex-sm-row gap-1">


                                            <!-- VACCINATED -->

                                            <button type="button"
                                                    class="btn-vax-approve"

                                                    data-bs-toggle="modal"
                                                    data-bs-target="#updateStatusModal"

                                                    data-booking-id="<?php
                                                    echo $appointment['booking_id'];
                                                    ?>"

                                                    data-child-name="<?php
                                                    echo htmlspecialchars(
                                                        $appointment['child_name']
                                                        ?? '',
                                                        ENT_QUOTES
                                                    );
                                                    ?>"

                                                    data-parent="<?php
                                                    echo htmlspecialchars(
                                                        ($appointment['parent_name']
                                                        ?? '')
                                                        . ' (' .
                                                        ($appointment['parent_phone']
                                                        ?? '')
                                                        . ')',
                                                        ENT_QUOTES
                                                    );
                                                    ?>"

                                                    data-vaccine="<?php
                                                    echo htmlspecialchars(
                                                        $appointment['vaccine_name']
                                                        ?? '',
                                                        ENT_QUOTES
                                                    );
                                                    ?>"

                                                    data-current-status="vaccinated">

                                                <i class="bi bi-check2"></i>

                                                Vaccinated

                                            </button>


                                            <!-- MISSED -->

                                            <button type="button"
                                                    class="btn-vax-reject"

                                                    data-bs-toggle="modal"
                                                    data-bs-target="#updateStatusModal"

                                                    data-booking-id="<?php
                                                    echo $appointment['booking_id'];
                                                    ?>"

                                                    data-child-name="<?php
                                                    echo htmlspecialchars(
                                                        $appointment['child_name']
                                                        ?? '',
                                                        ENT_QUOTES
                                                    );
                                                    ?>"

                                                    data-parent="<?php
                                                    echo htmlspecialchars(
                                                        ($appointment['parent_name']
                                                        ?? '')
                                                        . ' (' .
                                                        ($appointment['parent_phone']
                                                        ?? '')
                                                        . ')',
                                                        ENT_QUOTES
                                                    );
                                                    ?>"

                                                    data-vaccine="<?php
                                                    echo htmlspecialchars(
                                                        $appointment['vaccine_name']
                                                        ?? '',
                                                        ENT_QUOTES
                                                    );
                                                    ?>"

                                                    data-current-status="not_vaccinated">

                                                <i class="bi bi-x-lg"></i>

                                                Missed

                                            </button>


                                        </div>


                                    <?php endif; ?>


                                </td>


                            </tr>


                        <?php endforeach; ?>


                    <?php else: ?>


                        <tr>

                            <td colspan="7"
                                class="text-center py-4 text-muted">

                                <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>

                                No appointments found in database.

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>

            </div>


            <!-- =================================================
                 PAGINATION
                 ================================================= -->

            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center pt-3 mt-2 border-top gap-2">

                <span class="text-muted small">

                    Showing

                    <strong>
                        <?php echo count($appointments); ?>
                    </strong>

                    entries

                </span>


                <nav aria-label="Table pagination">

                    <ul class="pagination pagination-sm mb-0">

                        <li class="page-item disabled">

                            <a class="page-link" href="#">
                                Previous
                            </a>

                        </li>


                        <li class="page-item active">

                            <a class="page-link"
                               href="#"
                               style="background-color: #2563eb; border-color: #2563eb;">

                                1

                            </a>

                        </li>


                        <li class="page-item disabled">

                            <a class="page-link" href="#">
                                Next
                            </a>

                        </li>

                    </ul>

                </nav>

            </div>


        </div>

    </main>


    <!-- =========================================================
         UPDATE STATUS MODAL
         ========================================================= -->

    <div class="modal fade"
         id="updateStatusModal"
         tabindex="-1"
         aria-labelledby="updateStatusModalLabel"
         aria-hidden="true">


        <div class="modal-dialog modal-dialog-centered modal-lg">


            <div class="modal-content border-0 shadow">


                <form action="appointments.php"
                      method="POST">


                    <input type="hidden"
                           name="action"
                           value="update_vaccine_status">


                    <input type="hidden"
                           name="booking_id"
                           id="statusBookingId"
                           value="">


                    <!-- MODAL HEADER -->

                    <div class="modal-header bg-light">

                        <div>

                            <h5 class="modal-title fw-bold text-dark"
                                id="updateStatusModalLabel">

                                <i class="bi bi-shield-check text-primary me-2"></i>

                                Update Child Vaccination Status

                            </h5>


                            <small class="text-muted">

                                Booking Reference:

                                <span id="modalBookingIdDisplay"
                                      class="fw-bold text-primary">

                                    #

                                </span>

                            </small>

                        </div>


                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">

                        </button>

                    </div>


                    <!-- MODAL BODY -->

                    <div class="modal-body p-4">


                        <!-- PATIENT INFO -->

                        <div class="p-3 rounded-3 border mb-4 bg-body-tertiary">

                            <div class="row g-2">


                                <div class="col-6 col-md-4">

                                    <small class="text-muted d-block">
                                        Child Name
                                    </small>

                                    <strong class="text-dark"
                                            id="modalChildName">

                                        Child Name

                                    </strong>

                                </div>


                                <div class="col-6 col-md-4">

                                    <small class="text-muted d-block">
                                        Prescribed Vaccine
                                    </small>

                                    <strong class="text-primary"
                                            id="modalVaccineName">

                                        Vaccine Name

                                    </strong>

                                </div>


                                <div class="col-12 col-md-4">

                                    <small class="text-muted d-block">
                                        Parent Contact
                                    </small>

                                    <strong class="text-dark"
                                            id="modalParentName">

                                        Parent Name

                                    </strong>

                                </div>


                            </div>

                        </div>


                        <!-- STATUS -->

                        <label class="form-label fw-bold mb-2">

                            Vaccine Administration Outcome

                            <span class="text-danger">*</span>

                        </label>


                        <div class="row g-3 mb-4">


                            <!-- VACCINATED -->

                            <div class="col-12 col-md-6">

                                <div class="form-check p-3 border rounded-3 h-100 bg-white shadow-sm position-relative">


                                    <input class="form-check-input ms-0 me-2"
                                           type="radio"
                                           name="vaccine_status"
                                           id="statusVaccinated"
                                           value="vaccinated"
                                           checked>


                                    <label class="form-check-label fw-bold text-success stretched-link"
                                           for="statusVaccinated">

                                        <i class="bi bi-check-circle-fill me-1"></i>

                                        Vaccinated (Completed)

                                    </label>


                                    <div class="text-muted small mt-1 ms-4">

                                        Child attended hospital and vaccine dose was successfully administered.

                                    </div>


                                </div>

                            </div>


                            <!-- NOT VACCINATED -->

                            <div class="col-12 col-md-6">

                                <div class="form-check p-3 border rounded-3 h-100 bg-white shadow-sm position-relative">


                                    <input class="form-check-input ms-0 me-2"
                                           type="radio"
                                           name="vaccine_status"
                                           id="statusNotVaccinated"
                                           value="not_vaccinated">


                                    <label class="form-check-label fw-bold text-danger stretched-link"
                                           for="statusNotVaccinated">

                                        <i class="bi bi-x-circle-fill me-1"></i>

                                        Not Vaccinated (Missed)

                                    </label>


                                    <div class="text-muted small mt-1 ms-4">

                                        Child did not attend, was unwell, or dose was postponed.

                                    </div>


                                </div>

                            </div>


                        </div>


                        <!-- VACCINATED DETAILS -->

                        <div id="vaccinatedDetailsSection"
                             class="p-3 rounded-3 border bg-light mb-3">


                            <h6 class="fw-bold text-success mb-3">

                                <i class="bi bi-clipboard2-pulse me-1"></i>

                                Dose Administration Details

                            </h6>


                            <div class="row g-3">


                                <div class="col-12">

                                    <label class="form-label small fw-semibold">

                                        Clinical Observations & Parent Advice

                                    </label>


                                    <input type="text"
                                           name="admin_notes"
                                           class="form-control form-control-sm"
                                           placeholder="e.g. Well tolerated, advised to monitor temperature for 24h">

                                </div>


                            </div>

                        </div>


                        <!-- NOT VACCINATED REASON -->

                        <div id="notVaccinatedReasonSection"
                             class="p-3 rounded-3 border bg-light mb-3 d-none">


                            <h6 class="fw-bold text-danger mb-3">

                                <i class="bi bi-exclamation-triangle me-1"></i>

                                Non-Administration Reason

                            </h6>


                            <div class="row g-3">


                                <div class="col-12">

                                    <label class="form-label small fw-semibold">

                                        Reason for Absence or Postponement

                                        <span class="text-danger">*</span>

                                    </label>


                                    <select name="reason"
                                            class="form-select form-select-sm">


                                        <option value="absent">

                                            Parent / Child Absent (No Show)

                                        </option>


                                        <option value="unwell">

                                            Child Unwell / High Fever

                                        </option>


                                        <option value="rescheduled">

                                            Parent Requested Reschedule

                                        </option>


                                        <option value="stock_out">

                                            Vaccine Temporarily Out of Stock

                                        </option>


                                    </select>

                                </div>


                                <div class="col-12">

                                    <label class="form-label small fw-semibold">

                                        Follow-Up Instructions for Parent

                                    </label>


                                    <input type="text"
                                           name="followup_notes"
                                           class="form-control form-control-sm"
                                           placeholder="e.g. Advised to return next Wednesday">

                                </div>


                            </div>

                        </div>


                    </div>


                    <!-- MODAL FOOTER -->

                    <div class="modal-footer bg-light">


                        <button type="button"
                                class="btn btn-light border px-4"
                                data-bs-dismiss="modal">

                            Cancel

                        </button>


                        <button type="submit"
                                class="btn btn-primary px-4 shadow-sm"
                                style="background-color: #2563eb;">

                            <i class="bi bi-check2-circle me-1"></i>

                            Save Status Update

                        </button>


                    </div>


                </form>


            </div>

        </div>

    </div>


    <?php require_once __DIR__ . '/includes/footer.php'; ?>

</div>


<!-- =========================================================
     SEARCH JAVASCRIPT
     ========================================================= -->

<script>

document.addEventListener('DOMContentLoaded', function () {

    /* -----------------------------------------
       Search Table
       ----------------------------------------- */

    const searchInput =
        document.getElementById('tableSearchInput');

    const tableBody =
        document.getElementById('vaxcareTableBody');


    if (searchInput && tableBody) {

        searchInput.addEventListener('input', function () {

            const searchValue =
                this.value.toLowerCase().trim();

            const rows =
                tableBody.querySelectorAll('tr');


            rows.forEach(function (row) {

                const rowText =
                    row.textContent.toLowerCase();

                if (rowText.includes(searchValue)) {

                    row.style.display = '';

                } else {

                    row.style.display = 'none';

                }

            });

        });

    }


    /* -----------------------------------------
       Status Modal
       ----------------------------------------- */

    const modal =
        document.getElementById('updateStatusModal');


    if (modal) {

        modal.addEventListener(
            'show.bs.modal',
            function (event) {

                const button =
                    event.relatedTarget;


                if (!button) {
                    return;
                }


                const bookingId =
                    button.getAttribute(
                        'data-booking-id'
                    );


                const childName =
                    button.getAttribute(
                        'data-child-name'
                    );


                const parent =
                    button.getAttribute(
                        'data-parent'
                    );


                const vaccine =
                    button.getAttribute(
                        'data-vaccine'
                    );


                const currentStatus =
                    button.getAttribute(
                        'data-current-status'
                    );


                document.getElementById(
                    'statusBookingId'
                ).value = bookingId;


                document.getElementById(
                    'modalBookingIdDisplay'
                ).textContent = '#' + bookingId;


                document.getElementById(
                    'modalChildName'
                ).textContent = childName;


                document.getElementById(
                    'modalParentName'
                ).textContent = parent;


                document.getElementById(
                    'modalVaccineName'
                ).textContent = vaccine;


                /* Select correct status */

                const vaccinatedRadio =
                    document.getElementById(
                        'statusVaccinated'
                    );


                const notVaccinatedRadio =
                    document.getElementById(
                        'statusNotVaccinated'
                    );


                if (currentStatus === 'not_vaccinated') {

                    notVaccinatedRadio.checked = true;

                } else {

                    vaccinatedRadio.checked = true;

                }


                updateStatusSections();

            }

        );

    }


    /* -----------------------------------------
       Show / Hide Status Sections
       ----------------------------------------- */

    const vaccinatedRadio =
        document.getElementById(
            'statusVaccinated'
        );


    const notVaccinatedRadio =
        document.getElementById(
            'statusNotVaccinated'
        );


    function updateStatusSections() {

        const vaccinatedSection =
            document.getElementById(
                'vaccinatedDetailsSection'
            );


        const notVaccinatedSection =
            document.getElementById(
                'notVaccinatedReasonSection'
            );


        if (vaccinatedRadio.checked) {

            vaccinatedSection.classList.remove(
                'd-none'
            );

            notVaccinatedSection.classList.add(
                'd-none'
            );

        } else {

            vaccinatedSection.classList.add(
                'd-none'
            );

            notVaccinatedSection.classList.remove(
                'd-none'
            );

        }

    }


    if (vaccinatedRadio) {

        vaccinatedRadio.addEventListener(
            'change',
            updateStatusSections
        );

    }


    if (notVaccinatedRadio) {

        notVaccinatedRadio.addEventListener(
            'change',
            updateStatusSections
        );

    }


});

</script>