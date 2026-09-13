<?php
/**
 * Vaccine Catalog & Stock Availability Management
 * VaxCare Hospital Portal
 */

require_once __DIR__ . '/db.php';

$message = '';
$messageType = '';

/* =========================================================
   1. ADD / TOGGLE VACCINE ACTIONS
   ========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Add New Vaccine
    if ($_POST['action'] === 'add_vaccine') {
        $vaccine_name = trim($_POST['vaccine_name'] ?? '');
        $description  = trim($_POST['description'] ?? '');
        $age_group    = trim($_POST['age_group'] ?? '');
        $stock_status = $_POST['stock_status'] ?? 'Available';

        if (empty($vaccine_name)) {
            $message = 'Vaccine name is required.';
            $messageType = 'danger';
        } else {
            $stmt = mysqli_prepare(
                $connection,
                "INSERT INTO vaccines (vaccine_name, description, age_group, stock_status) VALUES (?, ?, ?, ?)"
            );
            mysqli_stmt_bind_param($stmt, "ssss", $vaccine_name, $description, $age_group, $stock_status);

            if (mysqli_stmt_execute($stmt)) {
                $message = 'Vaccine has been added successfully to catalog.';
                $messageType = 'success';
            } else {
                $message = 'Error adding vaccine: ' . mysqli_error($connection);
                $messageType = 'danger';
            }
            mysqli_stmt_close($stmt);
        }
    }

    // Toggle Stock Availability
    if ($_POST['action'] === 'toggle_status') {
        $vaccine_id = intval($_POST['vaccine_id'] ?? 0);

        if ($vaccine_id > 0) {
            $stmt = mysqli_prepare($connection, "SELECT stock_status FROM vaccines WHERE vaccine_id = ?");
            mysqli_stmt_bind_param($stmt, "i", $vaccine_id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $vaccine = mysqli_fetch_assoc($res);
            mysqli_stmt_close($stmt);

            if ($vaccine) {
                $newStatus = ($vaccine['stock_status'] === 'Available') ? 'Unavailable' : 'Available';

                $updateStmt = mysqli_prepare($connection, "UPDATE vaccines SET stock_status = ? WHERE vaccine_id = ?");
                mysqli_stmt_bind_param($updateStmt, "si", $newStatus, $vaccine_id);

                if (mysqli_stmt_execute($updateStmt)) {
                    $message = 'Vaccine availability status updated.';
                    $messageType = 'success';
                } else {
                    $message = 'Error updating status: ' . mysqli_error($connection);
                    $messageType = 'danger';
                }
                mysqli_stmt_close($updateStmt);
            }
        }
    }
}

/* =========================================================
   2. FETCH VACCINE LIST & STATS
   ========================================================= */
$vaccines = [];
$vaccineQuery = "SELECT vaccine_id, vaccine_name, description, age_group, stock_status FROM vaccines ORDER BY vaccine_id DESC";
$vaccineResult = mysqli_query($connection, $vaccineQuery);

if ($vaccineResult) {
    while ($row = mysqli_fetch_assoc($vaccineResult)) {
        $vaccines[] = $row;
    }
}

$totalVaccines = count($vaccines);
$availableVaccines = 0;
$unavailableVaccines = 0;

foreach ($vaccines as $v) {
    if ($v['stock_status'] === 'Available') {
        $availableVaccines++;
    } else {
        $unavailableVaccines++;
    }
}

$pageTitle = 'List of Vaccines - VaxCare Hospital Portal';
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
                    <div class="p-2 rounded-2" style="background: rgba(34, 197, 94, 0.15); color: #22c55e;">
                        <i class="bi bi-eyedropper fs-5"></i>
                    </div>
                    <h4 class="fw-bold mb-0" style="color: var(--vax-text-main);">
                        List of Vaccine & Availability
                    </h4>
                </div>
                <div class="small" style="color: var(--vax-text-muted);">
                    Dashboard / <span class="text-secondary">Hospital Vaccine Stock & Clinical Roster</span>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-success d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#addVaccineModal">
                    <i class="bi bi-plus-circle"></i>
                    <span>Add Vaccine</span>
                </button>
                <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" onclick="window.print()">
                    <i class="bi bi-printer"></i>
                    <span>Print Vaccine List</span>
                </button>
            </div>
        </div>

        <!-- Notification Message -->
        <?php if ($message !== ''): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi <?php echo ($messageType === 'success') ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-danger'; ?>"></i>
                    <div><?php echo htmlspecialchars($message); ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Summary Statistics -->
        <div class="row g-3 mb-4 mt-1">
            <div class="col-6 col-md-4">
                <div class="stat-card-vax">
                    <div class="stat-tile-icon stat-tile-purple">
                        <i class="bi bi-capsule"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Catalog Vaccines</span>
                        <span class="stat-number"><?php echo $totalVaccines; ?> Types</span>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-4">
                <div class="stat-card-vax">
                    <div class="stat-tile-icon stat-tile-green">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Available In Stock</span>
                        <span class="stat-number"><?php echo $availableVaccines; ?></span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="stat-card-vax">
                    <div class="stat-tile-icon stat-tile-red">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Unavailable</span>
                        <span class="stat-number"><?php echo $unavailableVaccines; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vaccine Catalog Table Card -->
        <div class="card-vax p-3">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
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
                            <th class="text-end">AVAILABILITY TOGGLE</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (count($vaccines) > 0): ?>
                        <?php foreach ($vaccines as $vaccine): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($vaccine['vaccine_name']); ?></strong>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($vaccine['description'] ?? 'N/A'); ?>
                                </td>
                                <td>
                                    <span class="text-secondary small"><?php echo htmlspecialchars($vaccine['age_group'] ?? 'All Ages'); ?></span>
                                </td>
                                <td>
                                    <?php if ($vaccine['stock_status'] === 'Available'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success">
                                            <i class="bi bi-check-circle me-1"></i> Available
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger">
                                            <i class="bi bi-x-circle me-1"></i> Unavailable
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="vaccine_id" value="<?php echo $vaccine['vaccine_id']; ?>">
                                        <button type="submit" class="btn btn-sm <?php echo ($vaccine['stock_status'] === 'Available') ? 'btn-outline-danger' : 'btn-outline-success'; ?>">
                                            <?php if ($vaccine['stock_status'] === 'Available'): ?>
                                                <i class="bi bi-slash-circle me-1"></i> Mark Unavailable
                                            <?php else: ?>
                                                <i class="bi bi-check2 me-1"></i> Mark Available
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-capsule fs-3 d-block mb-2"></i>
                                No vaccines registered in database.
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

<!-- Modal: Add New Vaccine -->
<div class="modal fade" id="addVaccineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2 text-success"></i> Add New Vaccine
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_vaccine">

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Vaccine Name <span class="text-danger">*</span></label>
                        <input type="text" name="vaccine_name" class="form-control" placeholder="e.g. Oral Polio Vaccine (OPV)" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="e.g. Protects against poliomyelitis virus"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Target Age Group</label>
                        <input type="text" name="age_group" class="form-control" placeholder="e.g. At Birth, 6, 10, 14 Weeks">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">Stock Status</label>
                        <select name="stock_status" class="form-select">
                            <option value="Available" selected>Available</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-plus-circle me-1"></i> Add Vaccine
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>