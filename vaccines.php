<?php

require_once __DIR__ . '/db.php';

/* =========================================================
   ADD / TOGGLE VACCINE ACTIONS
   ========================================================= */

$message = '';
$messageType = '';

/* ADD VACCINE */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    /* ---------- Add Vaccine ---------- */
    if ($_POST['action'] === 'add_vaccine') {

        $vaccine_name = trim($_POST['vaccine_name'] ?? '');
        $description  = trim($_POST['description'] ?? '');
        $age_group    = trim($_POST['age_group'] ?? '');
        $stock_status = $_POST['stock_status'] ?? 'Available';

        if ($vaccine_name === '') {

            $message = 'Vaccine name is required.';
            $messageType = 'danger';

        } else {

            $stmt = mysqli_prepare(
                $connection,
                "INSERT INTO vaccines 
                (vaccine_name, description, age_group, stock_status)
                VALUES (?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $vaccine_name,
                $description,
                $age_group,
                $stock_status
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = 'Vaccine added successfully.';
                $messageType = 'success';

            } else {

                $message = 'Error adding vaccine: ' . mysqli_error($connection);
                $messageType = 'danger';
            }

            mysqli_stmt_close($stmt);
        }
    }


    /* ---------- Toggle Availability ---------- */
    if ($_POST['action'] === 'toggle_status') {

        $vaccine_id = intval($_POST['vaccine_id']);

        /* Get current status */
        $stmt = mysqli_prepare(
            $connection,
            "SELECT stock_status FROM vaccines WHERE vaccine_id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $vaccine_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $vaccine = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if ($vaccine) {

            if ($vaccine['stock_status'] === 'Available') {
                $newStatus = 'Unavailable';
            } else {
                $newStatus = 'Available';
            }

            /* Update status */
            $stmt = mysqli_prepare(
                $connection,
                "UPDATE vaccines 
                 SET stock_status = ?
                 WHERE vaccine_id = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "si",
                $newStatus,
                $vaccine_id
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = 'Vaccine status updated successfully.';
                $messageType = 'success';

            } else {

                $message = 'Error updating status: ' . mysqli_error($connection);
                $messageType = 'danger';
            }

            mysqli_stmt_close($stmt);
        }
    }
}


/* =========================================================
   GET VACCINES FROM DATABASE
   ========================================================= */

$vaccineQuery = "
    SELECT vaccine_id, vaccine_name, description, age_group, stock_status
    FROM vaccines
    ORDER BY vaccine_id DESC
";

$vaccineResult = mysqli_query($connection, $vaccineQuery);

if (!$vaccineResult) {
    die("Database Error: " . mysqli_error($connection));
}

$vaccines = [];

while ($row = mysqli_fetch_assoc($vaccineResult)) {
    $vaccines[] = $row;
}


/* =========================================================
   DASHBOARD COUNTS
   ========================================================= */

$totalVaccines = count($vaccines);

$availableVaccines = 0;
$unavailableVaccines = 0;

foreach ($vaccines as $vaccine) {

    if ($vaccine['stock_status'] === 'Available') {
        $availableVaccines++;
    } else {
        $unavailableVaccines++;
    }
}


/* Page title */
$pageTitle = 'List of Vaccine - VaxCare Hospital Portal';

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

                    <div class="p-2 rounded-2"
                         style="background: rgba(34, 197, 94, 0.15); color: #22c55e;">

                        <i class="bi bi-eyedropper fs-5"></i>

                    </div>

                    <h4 class="fw-bold mb-0"
                        style="color: var(--vax-text-main);">

                        List of Vaccine & Availability

                    </h4>

                </div>

                <div class="small"
                     style="color: var(--vax-text-muted);">

                    Dashboard /
                    <span class="text-secondary">
                        Hospital Vaccine Stock & Clinical Roster
                    </span>

                </div>

            </div>


            <div class="d-flex gap-2">

                <!-- Add Vaccine Button -->
                <button type="button"
                        class="btn btn-sm btn-success d-flex align-items-center gap-1"
                        data-bs-toggle="modal"
                        data-bs-target="#addVaccineModal">

                    <i class="bi bi-plus-circle"></i>

                    <span>Add Vaccine</span>

                </button>


                <!-- Print -->
                <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1"
                        onclick="window.print()">

                    <i class="bi bi-printer"></i>

                    <span>Print Vaccine List</span>

                </button>

            </div>

        </div>


        <!-- Message -->
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
             4 SUMMARY STATS
             ===================================================== -->

        <div class="row g-3 mb-4 mt-1">

            <!-- Total Vaccines -->
            <div class="col-6 col-md-3">

                <div class="stat-card-vax">

                    <div class="stat-tile-icon stat-tile-purple">

                        <i class="bi bi-capsule"></i>

                    </div>

                    <div class="stat-info">

                        <span class="stat-label">
                            Catalog Vaccines
                        </span>

                        <span class="stat-number">
                            <?php echo $totalVaccines; ?> Types
                        </span>

                    </div>

                </div>

            </div>


            <!-- Available -->
            <div class="col-6 col-md-3">

                <div class="stat-card-vax">

                    <div class="stat-tile-icon stat-tile-green">

                        <i class="bi bi-check-circle-fill"></i>

                    </div>

                    <div class="stat-info">

                        <span class="stat-label">
                            Available Vaccines
                        </span>

                        <span class="stat-number">
                            <?php echo $availableVaccines; ?>
                        </span>

                    </div>

                </div>

            </div>


            <!-- Database Status -->
            <!-- <div class="col-6 col-md-3">

                <div class="stat-card-vax">

                    <div class="stat-tile-icon stat-tile-blue">

                        <i class="bi bi-database"></i>

                    </div>

                    <div class="stat-info">

                        <span class="stat-label">
                            Database Status
                        </span>

                        <span class="stat-number">
                            Connected
                        </span>

                    </div>

                </div>

            </div> -->


            <!-- Unavailable -->
            <div class="col-6 col-md-3">

                <div class="stat-card-vax">

                    <div class="stat-tile-icon stat-tile-red">

                        <i class="bi bi-exclamation-triangle-fill"></i>

                    </div>

                    <div class="stat-info">

                        <span class="stat-label">
                            Unavailable
                        </span>

                        <span class="stat-number">
                            <?php echo $unavailableVaccines; ?>
                        </span>

                    </div>

                </div>

            </div>

        </div>


        <!-- =====================================================
             VACCINE TABLE
             ===================================================== -->

        <div class="card-vax p-3">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h6 class="fw-bold mb-0 text-dark">

                    EPI Immunization Vaccine Catalog

                </h6>

                <span class="badge bg-light text-secondary border">

                    Database: vms

                </span>

            </div>


            <div class="table-responsive">

                <table class="table-vax">

                    <thead>

                        <tr>

                            <th>VACCINE NAME</th>

                            <th>DESCRIPTION</th>

                            <th>ELIGIBLE AGE SCHEDULE</th>

                            <th>STATUS</th>

                            <th class="text-end">
                                AVAILABILITY TOGGLE
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if (count($vaccines) > 0): ?>

                        <?php foreach ($vaccines as $vaccine): ?>

                            <tr>

                                <!-- Vaccine Name -->
                                <td>

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $vaccine['vaccine_name']
                                        );
                                        ?>
                                    </strong>

                                </td>


                                <!-- Description -->
                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $vaccine['description'] ?? 'N/A'
                                    );

                                    ?>

                                </td>


                                <!-- Age Group -->
                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $vaccine['age_group'] ?? 'N/A'
                                    );

                                    ?>

                                </td>


                                <!-- Status -->
                                <td>

                                    <?php if ($vaccine['stock_status'] === 'Available'): ?>

                                        <span class="badge bg-success">
                                            Available
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-danger">
                                            Unavailable
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- Toggle -->
                                <td class="text-end">

                                    <form method="POST"
                                          style="display:inline;">

                                        <input type="hidden"
                                               name="action"
                                               value="toggle_status">

                                        <input type="hidden"
                                               name="vaccine_id"
                                               value="<?php
                                               echo $vaccine['vaccine_id'];
                                               ?>">

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-primary">

                                            <?php if ($vaccine['stock_status'] === 'Available'): ?>

                                                Mark Unavailable

                                            <?php else: ?>

                                                Mark Available

                                            <?php endif; ?>

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                    <?php else: ?>

                        <tr>

                            <td colspan="5"
                                class="text-center py-4">

                                No vaccines found in database.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>


    <?php require_once __DIR__ . '/includes/footer.php'; ?>

</div>


<!-- =========================================================
     ADD VACCINE MODAL
     ========================================================= -->

<div class="modal fade"
     id="addVaccineModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title fw-bold">

                    <i class="bi bi-plus-circle me-2"></i>

                    Add New Vaccine

                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>


            <form method="POST">

                <div class="modal-body">

                    <input type="hidden"
                           name="action"
                           value="add_vaccine">


                    <!-- Vaccine Name -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Vaccine Name

                        </label>

                        <input type="text"
                               name="vaccine_name"
                               class="form-control"
                               placeholder="e.g. BCG"
                               required>

                    </div>


                    <!-- Description -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Description

                        </label>

                        <textarea name="description"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Enter vaccine description"></textarea>

                    </div>


                    <!-- Age Group -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Age Group

                        </label>

                        <input type="text"
                               name="age_group"
                               class="form-control"
                               placeholder="e.g. Newborn - 1 year">

                    </div>


                    <!-- Status -->
                    <div class="mb-3">

                        <label class="form-label fw-semibold">

                            Stock Status

                        </label>

                        <select name="stock_status"
                                class="form-select">

                            <option value="Available">
                                Available
                            </option>

                            <option value="Unavailable">
                                Unavailable
                            </option>

                        </select>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                            class="btn btn-success">

                        <i class="bi bi-plus-circle me-1"></i>

                        Add Vaccine

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>