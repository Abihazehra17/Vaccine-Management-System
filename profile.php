<?php
/**
 * Hospital Profile Management
 * Matches the 'hospitals' database table structure in 'vms'.
 * Once saved, displays ONLY 'Update' and 'Delete' actions.
 */

require_once __DIR__ . '/db.php';

$message = '';
$messageType = '';

// Check for redirect notifications
if (isset($_GET['deleted'])) {
    $message = "Hospital profile was deleted successfully.";
    $messageType = "success";
}

/* =========================================================
   1. DELETE HOSPITAL ACTION
   ========================================================= */
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $delete_id = intval($_GET['hospital_id'] ?? 0);

    if ($delete_id > 0) {
        $stmt = mysqli_prepare($connection, "DELETE FROM hospitals WHERE hospital_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $delete_id);

        if (mysqli_stmt_execute($stmt)) {
            // If the deleted hospital was in session, clear it
            if (isset($_SESSION['hospital_id']) && $_SESSION['hospital_id'] == $delete_id) {
                unset($_SESSION['hospital_id']);
                unset($_SESSION['hospital_name']);
                unset($_SESSION['hospital_email']);
            }
            mysqli_stmt_close($stmt);
            header("Location: profile.php?deleted=1");
            exit;
        } else {
            $message = "Unable to delete hospital: " . mysqli_error($connection);
            $messageType = "danger";
            mysqli_stmt_close($stmt);
        }
    }
}

/* =========================================================
   2. SAVE (ADD) OR UPDATE HOSPITAL PROFILE
   ========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action        = $_POST['action'] ?? '';
    $hospital_id   = intval($_POST['hospital_id'] ?? 0);
    $role_id       = intval($_POST['role_id'] ?? 3); // 3 = Hospital role
    $hospital_name = trim($_POST['hospital_name'] ?? '');
    $username      = trim($_POST['username'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $location      = trim($_POST['location'] ?? '');
    $address       = trim($_POST['address'] ?? '');
    $status        = $_POST['status'] ?? 'Active';
    $password      = $_POST['password'] ?? '';

    // Validate essential fields
    if (empty($hospital_name) || empty($username)) {
        $message = "Hospital Name and Username are required fields.";
        $messageType = "danger";
    } else {
        // --- ACTION: SAVE NEW HOSPITAL ---
        if ($action === 'save') {
            if (empty($password)) {
                $message = "Password is required when creating a hospital profile.";
                $messageType = "danger";
            } else {
                // Check if username or email already exists
                $checkStmt = mysqli_prepare($connection, "SELECT hospital_id FROM hospitals WHERE username = ? OR (email = ? AND email != '') LIMIT 1");
                mysqli_stmt_bind_param($checkStmt, "ss", $username, $email);
                mysqli_stmt_execute($checkStmt);
                mysqli_stmt_store_result($checkStmt);

                if (mysqli_stmt_num_rows($checkStmt) > 0) {
                    $message = "A hospital with this username or email already exists in the system.";
                    $messageType = "danger";
                    mysqli_stmt_close($checkStmt);
                } else {
                    mysqli_stmt_close($checkStmt);

                    $insertStmt = mysqli_prepare(
                        $connection,
                        "INSERT INTO hospitals (role_id, hospital_name, address, location, phone, email, username, password, status)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                    );

                    mysqli_stmt_bind_param(
                        $insertStmt,
                        "issssssss",
                        $role_id,
                        $hospital_name,
                        $address,
                        $location,
                        $phone,
                        $email,
                        $username,
                        $password,
                        $status
                    );

                    if (mysqli_stmt_execute($insertStmt)) {
                        $new_id = mysqli_insert_id($connection);
                        $_SESSION['hospital_id'] = $new_id;
                        $_SESSION['hospital_name'] = $hospital_name;
                        $_SESSION['hospital_email'] = $email;
                        $message = "Hospital profile registered and saved successfully.";
                        $messageType = "success";
                    } else {
                        $message = "Failed to save hospital profile: " . mysqli_error($connection);
                        $messageType = "danger";
                    }
                    mysqli_stmt_close($insertStmt);
                }
            }
        }

        // --- ACTION: UPDATE EXISTING HOSPITAL ---
        elseif ($action === 'update' && $hospital_id > 0) {
            // Check username or email uniqueness against other hospitals
            $checkStmt = mysqli_prepare($connection, "SELECT hospital_id FROM hospitals WHERE (username = ? OR (email = ? AND email != '')) AND hospital_id != ? LIMIT 1");
            mysqli_stmt_bind_param($checkStmt, "ssi", $username, $email, $hospital_id);
            mysqli_stmt_execute($checkStmt);
            mysqli_stmt_store_result($checkStmt);

            if (mysqli_stmt_num_rows($checkStmt) > 0) {
                $message = "Another facility is already using this username or email.";
                $messageType = "danger";
                mysqli_stmt_close($checkStmt);
            } else {
                mysqli_stmt_close($checkStmt);

                if (!empty($password)) {
                    $updateStmt = mysqli_prepare(
                        $connection,
                        "UPDATE hospitals SET
                            role_id = ?,
                            hospital_name = ?,
                            address = ?,
                            location = ?,
                            phone = ?,
                            email = ?,
                            username = ?,
                            password = ?,
                            status = ?
                         WHERE hospital_id = ?"
                    );
                    mysqli_stmt_bind_param(
                        $updateStmt,
                        "issssssssi",
                        $role_id,
                        $hospital_name,
                        $address,
                        $location,
                        $phone,
                        $email,
                        $username,
                        $password,
                        $status,
                        $hospital_id
                    );
                } else {
                    $updateStmt = mysqli_prepare(
                        $connection,
                        "UPDATE hospitals SET
                            role_id = ?,
                            hospital_name = ?,
                            address = ?,
                            location = ?,
                            phone = ?,
                            email = ?,
                            username = ?,
                            status = ?
                         WHERE hospital_id = ?"
                    );
                    mysqli_stmt_bind_param(
                        $updateStmt,
                        "isssssssi",
                        $role_id,
                        $hospital_name,
                        $address,
                        $location,
                        $phone,
                        $email,
                        $username,
                        $status,
                        $hospital_id
                    );
                }

                if (mysqli_stmt_execute($updateStmt)) {
                    $_SESSION['hospital_name'] = $hospital_name;
                    $_SESSION['hospital_email'] = $email;
                    $message = "Hospital profile updated successfully.";
                    $messageType = "success";
                } else {
                    $message = "Failed to update profile: " . mysqli_error($connection);
                    $messageType = "danger";
                }
                mysqli_stmt_close($updateStmt);
            }
        }
    }
}

/* =========================================================
   3. RETRIEVE ACTIVE HOSPITAL PROFILE
   ========================================================= */
$hospital = null;
$target_hospital_id = 0;

if (isset($_GET['hospital_id']) && intval($_GET['hospital_id']) > 0) {
    $target_hospital_id = intval($_GET['hospital_id']);
} elseif (isset($_SESSION['hospital_id']) && intval($_SESSION['hospital_id']) > 0) {
    $target_hospital_id = intval($_SESSION['hospital_id']);
}

// Load by identified ID
if ($target_hospital_id > 0) {
    $stmt = mysqli_prepare(
        $connection,
        "SELECT hospital_id, role_id, hospital_name, address, location, phone, email, username, status
         FROM hospitals WHERE hospital_id = ? LIMIT 1"
    );
    mysqli_stmt_bind_param($stmt, "i", $target_hospital_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $hospital = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
}

// Fallback to first existing hospital in DB (if not deleted/requested new)
if (!$hospital && !isset($_GET['deleted']) && !isset($_GET['new'])) {
    $fallbackRes = mysqli_query(
        $connection,
        "SELECT hospital_id, role_id, hospital_name, address, location, phone, email, username, status
         FROM hospitals ORDER BY hospital_id ASC LIMIT 1"
    );
    if ($fallbackRes && mysqli_num_rows($fallbackRes) > 0) {
        $hospital = mysqli_fetch_assoc($fallbackRes);
        $_SESSION['hospital_id'] = $hospital['hospital_id'];
        $_SESSION['hospital_name'] = $hospital['hospital_name'];
        $_SESSION['hospital_email'] = $hospital['email'];
    }
}

$pageTitle = 'Hospital Profile - VaxCare Hospital Portal';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<div class="main-wrapper">
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="p-3 p-md-4">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="p-2 rounded-2" style="background: rgba(37, 99, 235, 0.12); color: #2563eb;">
                        <i class="bi bi-building fs-5"></i>
                    </div>
                    <h4 class="fw-bold mb-0" style="color: var(--vax-text-main);">
                        Hospital Profile & Facility Setup
                    </h4>
                </div>
                <div class="small" style="color: var(--vax-text-muted);">
                    Dashboard / <span class="text-secondary">Hospital Configuration & Clinic Directory</span>
                </div>
            </div>

            <?php if ($hospital): ?>
                <span class="badge px-3 py-2 rounded-pill <?php echo ($hospital['status'] === 'Active') ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger'; ?>">
                    <i class="bi bi-circle-fill me-1" style="font-size: 0.55rem;"></i>
                    Facility Status: <?php echo htmlspecialchars($hospital['status']); ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Alert Notification -->
        <?php if ($message !== ''): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi <?php echo ($messageType === 'success') ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-danger'; ?> fs-5"></i>
                    <div><?php echo htmlspecialchars($message); ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Hospital Profile Form Card -->
        <div class="card-vax p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                <div>
                    <h5 class="fw-bold mb-1 text-dark">
                        <i class="bi bi-hospital me-2 text-primary"></i>
                        <?php echo $hospital ? 'Edit Hospital Facility Details' : 'Register New Hospital Facility'; ?>
                    </h5>
                    <p class="text-muted small mb-0">
                        Information synchronized with the MySQL <code>vms</code> database <code>hospitals</code> table.
                    </p>
                </div>
                <?php if ($hospital): ?>
                    <span class="badge bg-light text-secondary border">
                        Hospital ID: #<?php echo htmlspecialchars($hospital['hospital_id']); ?>
                    </span>
                <?php endif; ?>
            </div>

            <form action="profile.php" method="POST">
                <?php if ($hospital): ?>
                    <!-- Update Action & Target ID -->
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="hospital_id" value="<?php echo htmlspecialchars($hospital['hospital_id']); ?>">
                <?php else: ?>
                    <!-- Save Action -->
                    <input type="hidden" name="action" value="save">
                <?php endif; ?>

                <!-- Default Role ID (3 for Hospital) -->
                <input type="hidden" name="role_id" value="<?php echo htmlspecialchars($hospital['role_id'] ?? '3'); ?>">

                <div class="row g-3">
                    <!-- 1. Hospital Name -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold text-muted">
                            Hospital / Facility Name <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-building"></i></span>
                            <input type="text" 
                                   class="form-control border-start-0" 
                                   name="hospital_name" 
                                   placeholder="e.g. Shed Hospital" 
                                   value="<?php echo htmlspecialchars($hospital['hospital_name'] ?? ''); ?>" 
                                   required>
                        </div>
                    </div>

                    <!-- 2. Username -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold text-muted">
                            Portal Username <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person"></i></span>
                            <input type="text" 
                                   class="form-control border-start-0" 
                                   name="username" 
                                   placeholder="e.g. shedhospital" 
                                   value="<?php echo htmlspecialchars($hospital['username'] ?? ''); ?>" 
                                   required>
                        </div>
                    </div>

                    <!-- 3. Official Email -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold text-muted">
                            Official Email Address
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-envelope"></i></span>
                            <input type="email" 
                                   class="form-control border-start-0" 
                                   name="email" 
                                   placeholder="e.g. contact@hospital.org" 
                                   value="<?php echo htmlspecialchars($hospital['email'] ?? ''); ?>">
                        </div>
                    </div>

                    <!-- 4. Phone Number -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold text-muted">
                            Official Contact Phone
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-telephone"></i></span>
                            <input type="text" 
                                   class="form-control border-start-0" 
                                   name="phone" 
                                   placeholder="e.g. 021-35539234" 
                                   value="<?php echo htmlspecialchars($hospital['phone'] ?? ''); ?>">
                        </div>
                    </div>

                    <!-- 5. Location / Area -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold text-muted">
                            Location / Area / District
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-geo-alt"></i></span>
                            <input type="text" 
                                   class="form-control border-start-0" 
                                   name="location" 
                                   placeholder="e.g. North Karachi" 
                                   value="<?php echo htmlspecialchars($hospital['location'] ?? ''); ?>">
                        </div>
                    </div>

                    <!-- 6. Status -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold text-muted">
                            Operating Status
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-activity"></i></span>
                            <select class="form-select border-start-0" name="status">
                                <option value="Active" <?php echo (($hospital['status'] ?? 'Active') === 'Active') ? 'selected' : ''; ?>>Active</option>
                                <option value="Inactive" <?php echo (($hospital['status'] ?? '') === 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- 7. Physical Address -->
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">
                            Complete Physical Address
                        </label>
                        <textarea class="form-control" 
                                  name="address" 
                                  rows="2" 
                                  placeholder="e.g. Sector-11-E, North Karachi"><?php echo htmlspecialchars($hospital['address'] ?? ''); ?></textarea>
                    </div>

                    <!-- 8. Password -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold text-muted">
                            Password <?php echo $hospital ? '<span class="text-muted fw-normal">(leave blank to keep existing password)</span>' : '<span class="text-danger">*</span>'; ?>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock"></i></span>
                            <input type="password" 
                                   class="form-control border-start-0 border-end-0" 
                                   id="hospitalPasswordInput" 
                                   name="password" 
                                   placeholder="<?php echo $hospital ? '••••••••' : 'Enter login password'; ?>" 
                                   <?php echo $hospital ? '' : 'required'; ?>>
                            <button class="btn btn-outline-secondary border-start-0 toggle-password-btn" 
                                    type="button" 
                                    data-target="hospitalPasswordInput" 
                                    title="Show/Hide Password">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="d-flex flex-wrap align-items-center gap-2 mt-4 pt-3 border-top">
                    <?php if ($hospital): ?>
                        <!-- If hospital is saved/exists: ONLY UPDATE and DELETE buttons -->
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" style="background-color: #2563eb;">
                            <i class="bi bi-pencil-square me-1"></i> Update Hospital
                        </button>

                        <a href="profile.php?action=delete&hospital_id=<?php echo htmlspecialchars($hospital['hospital_id']); ?>" 
                           class="btn btn-outline-danger px-4 py-2 fw-semibold" 
                           onclick="return confirm('Are you sure you want to delete this hospital profile? This will remove the facility from the database.');">
                            <i class="bi bi-trash3 me-1"></i> Delete Hospital
                        </a>
                    <?php else: ?>
                        <!-- If no hospital exists: Save button -->
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" style="background-color: #2563eb;">
                            <i class="bi bi-save me-1"></i> Save Hospital Profile
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </main>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>
</div>