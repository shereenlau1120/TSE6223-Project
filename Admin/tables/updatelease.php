<?php
session_start();
include '../databaseconnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: lease.php");
    exit();
}

/* =========================
   FETCH LEASE
   ========================= */
$result = mysqli_query(
    $conn,
    "SELECT
        l.*,
        p.property_name,
        p.property_image,
        p.address,
        u.full_name AS tenant_name
     FROM leases l
     LEFT JOIN properties p ON l.property_id = p.property_id
     LEFT JOIN users u ON l.tenant_id = u.user_id
     WHERE l.lease_id = $id"
);

$lease = mysqli_fetch_assoc($result);

if (!$lease) {
    die("Lease not found.");
}

/* =========================
   UPDATE LEASE
   ========================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $monthly_rent = $_POST['monthly_rent'];
    $lease_status = $_POST['lease_status'];

    $lease_document = $lease['lease_document'];

    // Upload new PDF
    if (!empty($_FILES['lease_document']['name'])) {

        $folder = "../uploads/leases/";

        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES["lease_document"]["name"]);
        $targetFile = $folder . $fileName;

        if (move_uploaded_file($_FILES["lease_document"]["tmp_name"], $targetFile)) {

            $lease_document = "uploads/leases/" . $fileName;
        }
    }

    $stmt = $conn->prepare("
        UPDATE leases
        SET
            monthly_rent = ?,
            lease_status = ?,
            lease_document = ?
        WHERE lease_id = ?
    ");

    $stmt->bind_param(
        "dssi",
        $monthly_rent,
        $lease_status,
        $lease_document,
        $id
    );

    if ($stmt->execute()) {

        header("Location: lease.php");
        exit();

    } else {

        $errorMessage = "Failed to update lease.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Update Lease</title>

<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/kaiadmin.min.css">

<style>

.is-invalid{
    border:2px solid #dc3545 !important;
}

.error-text{
    color:#dc3545;
    font-size:12px;
    margin-top:3px;
}

.preview-image{
    max-width:250px;
    border-radius:10px;
}

.readonly-box{
    background:#f8f9fa;
    color:#6c757d;
    pointer-events:none;
    cursor:not-allowed;
}

</style>
</head>

<body>

<div class="container mt-4">

<div class="card">

    <!-- HEADER -->
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">Update Lease</h4>
    </div>

    <div class="card-body">

        <?php if (!empty($errorMessage)) { ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <strong>Error!</strong>
                <?= htmlspecialchars($errorMessage) ?>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"></button>
            </div>
        <?php } ?>

        <form id="leaseForm" method="POST" enctype="multipart/form-data">

            <!-- LEASE ID -->
            <div class="mb-3">
                <label class="form-label">Lease ID</label>
                <div class="form-control readonly-box">
                    <?= htmlspecialchars($lease['lease_id']) ?>
                </div>
            </div>

            <!-- TENANT -->
            <div class="mb-3">
                <label class="form-label">Tenant Name</label>
                <div class="form-control readonly-box">
                    <?= htmlspecialchars($lease['tenant_name']) ?>
                </div>
            </div>

            <!-- PROPERTY -->
            <div class="mb-3">
                <label class="form-label">Property Name</label>
                <div class="form-control readonly-box">
                    <?= htmlspecialchars($lease['property_name']) ?>
                </div>
            </div>

            <!-- ADDRESS -->
            <div class="mb-3">
                <label class="form-label">Property Address</label>
                <div class="form-control readonly-box">
                    <?= htmlspecialchars($lease['address']) ?>
                </div>
            </div>

            <!-- PROPERTY IMAGE -->
            <div class="mb-3">
                <label class="form-label">Property Image</label><br>

                <?php if (!empty($lease['property_image'])) { ?>

                    <img src="../<?= htmlspecialchars($lease['property_image']) ?>"
                         class="preview-image">

                <?php } else { ?>

                    <span class="text-muted">No image available</span>

                <?php } ?>
            </div>

            <!-- START DATE -->
            <div class="mb-3">
                <label class="form-label">Lease Start Date</label>
                <div class="form-control readonly-box">
                    <?= htmlspecialchars($lease['lease_start_date']) ?>
                </div>
            </div>

            <!-- END DATE -->
            <div class="mb-3">
                <label class="form-label">Lease End Date</label>
                <div class="form-control readonly-box">
                    <?= htmlspecialchars($lease['lease_end_date']) ?>
                </div>
            </div>

            <!-- RENT -->
            <div class="mb-3">
                <label class="form-label">Monthly Rent (RM)</label>

                <input type="number"
                       step="0.01"
                       min="0"
                       id="rent"
                       name="monthly_rent"
                       class="form-control"
                       value="<?= htmlspecialchars($lease['monthly_rent']) ?>"
                       required>

                <div id="rentError" class="error-text"></div>
            </div>

            <!-- STATUS -->
            <div class="mb-3">

                <label class="form-label">Lease Status</label>

                <select name="lease_status"
                        class="form-control">

                    <option value="active"
                        <?= ($lease['lease_status'] == 'active') ? 'selected' : ''; ?>>
                        Active
                    </option>

                    <option value="expired"
                        <?= ($lease['lease_status'] == 'expired') ? 'selected' : ''; ?>>
                        Expired
                    </option>

                    <option value="terminated"
                        <?= ($lease['lease_status'] == 'terminated') ? 'selected' : ''; ?>>
                        Terminated
                    </option>

                </select>

            </div>

            <!-- CURRENT PDF -->
            <div class="mb-3">

                <label class="form-label">
                    Current Lease Agreement
                </label><br>

                <?php if (!empty($lease['lease_document'])) { ?>
                            <a href="../uploads/<?= htmlspecialchars($lease['lease_document']); ?>"
                              target="_blank"
                              class="btn btn-sm btn-info">
                              View Current Lease Document (PDF)
                            </a>
                          <?php } else { ?>

                    <span class="text-muted">
                        No document uploaded
                    </span>

                <?php } ?>

            </div>

            <!-- NEW PDF -->
            <div class="mb-3">

                <label class="form-label">
                    Upload New Lease Agreement (Optional)
                </label>

                <input type="file"
                       name="lease_document"
                       class="form-control"
                       accept=".pdf">

            </div>

            <!-- BUTTONS -->
            <div class="d-flex justify-content-between">

                <a href="lease.php"
                   class="btn btn-secondary">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-success">
                    Update Lease
                </button>

            </div>

        </form>

    </div>
</div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById("leaseForm").addEventListener("submit", function(e){

    let valid = true;

    const rent = document.getElementById("rent");

    document.querySelectorAll(".error-text")
        .forEach(el => el.textContent = "");

    rent.classList.remove("is-invalid");

    if(parseFloat(rent.value) <= 0){

        rent.classList.add("is-invalid");

        document.getElementById("rentError").textContent =
            "Monthly rent must be greater than RM 0.";

        valid = false;
    }

    if(!valid){
        e.preventDefault();
    }
});
</script>

</body>
</html>
```
