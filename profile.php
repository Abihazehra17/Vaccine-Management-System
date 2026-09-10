<?php
require_once __DIR__ . '/db.php';

$message = '';
$messageType = '';

/* =========================
   DELETE HOSPITAL
========================= */
if (isset($_GET['delete'])) {

    $hospital_id = (int) $_GET['delete'];

    $stmt = mysqli_prepare(
        $connection,
        "DELETE FROM hospitals WHERE hospital_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $hospital_id);

    if (mysqli_stmt_execute($stmt)) {
        $message = "Hospital deleted successfully.";
        $messageType = "success";
    } else {
        $message = "Unable to delete hospital.";
        $messageType = "danger";
    }

    mysqli_stmt_close($stmt);
}


/* =========================
   ADD / UPDATE HOSPITAL
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    $hospital_id  = (int)($_POST['hospital_id'] ?? 0);
    $role_id      = (int)($_POST['role_id'] ?? 0);
    $hospital_name = trim($_POST['hospital_name'] ?? '');
    $address       = trim($_POST['address'] ?? '');
    $location      = trim($_POST['location'] ?? '');
    $phone         = trim($_POST['phone'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $username      = trim($_POST['username'] ?? '');
    $password      = $_POST['password'] ?? '';
    $status        = $_POST['status'] ?? 'Active';


    /* ADD */
    if ($action === 'add') {

        if ($hospital_name === '' || $username === '' || $password === '') {

            $message = "Hospital Name, Username and Password are required.";
            $messageType = "danger";

        } else {

            $stmt = mysqli_prepare(
                $connection,
                "INSERT INTO hospitals
                (role_id, hospital_name, address, location, phone, email, username, password, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
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

            if (mysqli_stmt_execute($stmt)) {
                $message = "Hospital added successfully.";
                $messageType = "success";
            } else {
                $message = "Unable to add hospital: " . mysqli_error($connection);
                $messageType = "danger";
            }

            mysqli_stmt_close($stmt);
        }
    }


    /* UPDATE */
    if ($action === 'update') {

        if ($hospital_id <= 0 || $hospital_name === '' || $username === '') {

            $message = "Hospital Name and Username are required.";
            $messageType = "danger";

        } else {

            /*
             * Password blank ho to purana password same rahega.
             */
            if ($password !== '') {

                $stmt = mysqli_prepare(
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
                    $stmt,
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

                $stmt = mysqli_prepare(
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
                    $stmt,
                    "isssssss i",
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

            if (mysqli_stmt_execute($stmt)) {
                $message = "Hospital updated successfully.";
                $messageType = "success";
            } else {
                $message = "Unable to update hospital: " . mysqli_error($connection);
                $messageType = "danger";
            }

            mysqli_stmt_close($stmt);
        }
    }
}


/* =========================
   GET HOSPITAL
========================= */

$editHospital = null;

if (isset($_GET['edit'])) {

    $edit_id = (int) $_GET['edit'];

    $stmt = mysqli_prepare(
        $connection,
        "SELECT hospital_id, role_id, hospital_name, address, location,
                phone, email, username, status
         FROM hospitals
         WHERE hospital_id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $edit_id);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $editHospital = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
}


/* =========================
   GET ALL HOSPITALS
========================= */

$hospitals = [];

$result = mysqli_query(
    $connection,
    "SELECT hospital_id, role_id, hospital_name, address, location,
            phone, email, username, status
     FROM hospitals
     ORDER BY hospital_id DESC"
);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $hospitals[] = $row;
    }
}

$pageTitle = "Hospital Directory - VaxCare Hospital Portal";

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<style>

.hospital-page {
    padding: 30px;
}

.hospital-form-card {
    background: #fff;
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.08);
    margin-bottom: 30px;
}

.hospital-form-title {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 25px;
}

.form-label {
    font-weight: 500;
}

.form-control,
.form-select {
    border-radius: 8px;
    padding: 11px 13px;
}

.form-buttons {
    margin-top: 25px;
    display: flex;
    gap: 10px;
}

.hospital-list-card {
    background: #fff;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.08);
}

.hospital-item {
    border: 1px solid #e8e8e8;
    border-radius: 10px;
    padding: 18px;
    margin-bottom: 15px;
}

.hospital-item h5 {
    margin-bottom: 10px;
}

.hospital-info {
    margin: 5px 0;
    color: #555;
}

.hospital-actions {
    margin-top: 12px;
    display: flex;
    gap: 8px;
}

</style>


<div class="hospital-page">

    <!-- PAGE HEADING -->
    <div class="mb-4">
        <h2>Hospital Directory</h2>
        <p class="text-muted">
            Manage hospital information
        </p>
    </div>


    <!-- MESSAGE -->
    <?php if ($message): ?>

        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <!-- =========================
         HOSPITAL FORM
    ========================== -->

    <div class="hospital-form-card">

        <div class="hospital-form-title">

            <?php if ($editHospital): ?>

                <i class="bi bi-pencil-square"></i>
                Update Hospital

            <?php else: ?>

                <i class="bi bi-hospital"></i>
                Hospital Information

            <?php endif; ?>

        </div>


        <form method="POST">

            <?php if ($editHospital): ?>

                <input type="hidden"
                       name="action"
                       value="update">

                <input type="hidden"
                       name="hospital_id"
                       value="<?php echo $editHospital['hospital_id']; ?>">

            <?php else: ?>

                <input type="hidden"
                       name="action"
                       value="add">

            <?php endif; ?>


            <div class="row g-3">


                <!-- ROLE ID -->
                <div class="col-md-6">

                    <label class="form-label">
                        Role ID
                    </label>

                    <input
                        type="number"
                        name="role_id"
                        class="form-control"
                        value="<?php
                        echo htmlspecialchars(
                            $editHospital['role_id'] ?? ''
                        );
                        ?>"
                        required
                    >

                </div>


                <!-- HOSPITAL NAME -->
                <div class="col-md-6">

                    <label class="form-label">
                        Hospital Name
                    </label>

                    <input
                        type="text"
                        name="hospital_name"
                        class="form-control"
                        value="<?php
                        echo htmlspecialchars(
                            $editHospital['hospital_name'] ?? ''
                        );
                        ?>"
                        required
                    >

                </div>


                <!-- ADDRESS -->
                <div class="col-md-6">

                    <label class="form-label">
                        Address
                    </label>

                    <textarea
                        name="address"
                        class="form-control"
                        rows="3"
                    ><?php
                    echo htmlspecialchars(
                        $editHospital['address'] ?? ''
                    );
                    ?></textarea>

                </div>


                <!-- LOCATION -->
                <div class="col-md-6">

                    <label class="form-label">
                        Location
                    </label>

                    <input
                        type="text"
                        name="location"
                        class="form-control"
                        value="<?php
                        echo htmlspecialchars(
                            $editHospital['location'] ?? ''
                        );
                        ?>"
                    >

                </div>


                <!-- PHONE -->
                <div class="col-md-6">

                    <label class="form-label">
                        Phone
                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                        value="<?php
                        echo htmlspecialchars(
                            $editHospital['phone'] ?? ''
                        );
                        ?>"
                    >

                </div>


                <!-- EMAIL -->
                <div class="col-md-6">

                    <label class="form-label">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?php
                        echo htmlspecialchars(
                            $editHospital['email'] ?? ''
                        );
                        ?>"
                    >

                </div>


                <!-- USERNAME -->
                <div class="col-md-6">

                    <label class="form-label">
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        value="<?php
                        echo htmlspecialchars(
                            $editHospital['username'] ?? ''
                        );
                        ?>"
                        required
                    >

                </div>


                <!-- PASSWORD -->
                <div class="col-md-6">

                    <label class="form-label">
                        Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        <?php echo !$editHospital ? 'required' : ''; ?>
                    >

                    <?php if ($editHospital): ?>

                        <small class="text-muted">
                            Leave blank to keep the current password.
                        </small>

                    <?php endif; ?>

                </div>


                <!-- STATUS -->
                <div class="col-md-6">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select"
                    >

                        <option
                            value="Active"
                            <?php
                            echo (($editHospital['status'] ?? 'Active') === 'Active')
                                ? 'selected'
                                : '';
                            ?>
                        >
                            Active
                        </option>

                        <option
                            value="Inactive"
                            <?php
                            echo (($editHospital['status'] ?? '') === 'Inactive')
                                ? 'selected'
                                : '';
                            ?>
                        >
                            Inactive
                        </option>

                    </select>

                </div>

            </div>


            <!-- BUTTONS -->

            <div class="form-buttons">

                <?php if ($editHospital): ?>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-save"></i>
                        Update Hospital
                    </button>

                    <a
                        href="profile.php"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>

                    <a
                        href="profile.php?delete=<?php echo $editHospital['hospital_id']; ?>"
                        class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to delete this hospital?');"
                    >
                        <i class="bi bi-trash"></i>
                        Delete
                    </a>

                <?php else: ?>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-plus-circle"></i>
                        Add Hospital
                    </button>

                <?php endif; ?>

            </div>

        </form>

    </div>


    <!-- =========================
         EXISTING HOSPITALS
    ========================== -->

    <div class="hospital-list-card">

        <h4 class="mb-4">
            Hospitals
        </h4>

        <?php if (empty($hospitals)): ?>

            <div class="text-muted">
                No hospitals have been added yet.
            </div>

        <?php else: ?>

            <?php foreach ($hospitals as $hospital): ?>

                <div class="hospital-item">

                    <h5>
                        <?php
                        echo htmlspecialchars(
                            $hospital['hospital_name']
                        );
                        ?>
                    </h5>

                    <div class="hospital-info">
                        <strong>Location:</strong>
                        <?php
                        echo htmlspecialchars(
                            $hospital['location'] ?? ''
                        );
                        ?>
                    </div>

                    <div class="hospital-info">
                        <strong>Phone:</strong>
                        <?php
                        echo htmlspecialchars(
                            $hospital['phone'] ?? ''
                        );
                        ?>
                    </div>

                    <div class="hospital-info">
                        <strong>Email:</strong>
                        <?php
                        echo htmlspecialchars(
                            $hospital['email'] ?? ''
                        );
                        ?>
                    </div>

                    <div class="hospital-info">
                        <strong>Status:</strong>
                        <?php
                        echo htmlspecialchars(
                            $hospital['status'] ?? ''
                        );
                        ?>
                    </div>


                    <div class="hospital-actions">

                        <a
                            href="profile.php?edit=<?php echo $hospital['hospital_id']; ?>"
                            class="btn btn-sm btn-primary"
                        >
                            <i class="bi bi-pencil"></i>
                            Update
                        </a>

                        <a
                            href="profile.php?delete=<?php echo $hospital['hospital_id']; ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure you want to delete this hospital?');"
                        >
                            <i class="bi bi-trash"></i>
                            Delete
                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>


<?php
require_once __DIR__ . '/includes/footer.php';
?>