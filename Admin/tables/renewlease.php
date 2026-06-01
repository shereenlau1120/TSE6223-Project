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
   FETCH CURRENT LEASE
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
   RENEW LEASE
   ========================= */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $new_start_date = $_POST['lease_start_date'];
    $new_end_date   = $_POST['lease_end_date'];
    $monthly_rent   = $_POST['monthly_rent'];

    $lease_document = null;

    // Upload new agreement
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

    // Insert new lease
    $stmt = $conn->prepare("
        INSERT INTO leases
        (
            tenant_id,
            property_id,
            lease_start_date,
            lease_end_date,
            monthly_rent,
            lease_document,
            lease_status
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, 'active'
        )
    ");

    $stmt->bind_param(
        "iissds",
        $lease['tenant_id'],
        $lease['property_id'],
        $new_start_date,
        $new_end_date,
        $monthly_rent,
        $lease_document
    );

    if ($stmt->execute()) {

        // Expire previous lease
        mysqli_query(
            $conn,
            "UPDATE leases
             SET lease_status='expired'
             WHERE lease_id = $id"
        );

        header("Location: lease.php");
        exit();

    } else {

        $errorMessage = "Failed to renew lease.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Renew Lease</title>

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
    <div class="card-header bg-success text-white">
        <h4 class="mb-0">Renew Lease</h4>
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

        <h5 class="mb-5 text-success" style="text-decoration:underline; color:#198754 !important;">
                Old Lease Information
            </h5>

        <form id="renewLeaseForm"
              method="POST"
              enctype="multipart/form-data">

            <!-- LEASE ID -->
            <div class="mb-3">
                <label class="form-label">Current Lease ID</label>
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
                <?php } ?>
            </div>

            <!-- CURRENT LEASE PERIOD -->
            <div class="mb-3">
                <label class="form-label">Current Lease Period</label>

                <div class="form-control readonly-box">
                    <?= htmlspecialchars($lease['lease_start_date']) ?>
                    →
                    <?= htmlspecialchars($lease['lease_end_date']) ?>
                </div>
            </div>

            <!-- CURRENT RENT -->
            <div class="mb-3">
                <label class="form-label">Current Monthly Rent</label>

                <div class="form-control readonly-box">
                    RM <?= number_format($lease['monthly_rent'], 2) ?>
                </div>
            </div>

            <hr>

            <h5 class="mb-5 text-success" style="text-decoration:underline;">
                New Lease Information
            </h5>

            <!-- NEW START DATE -->
            <div class="mb-3">
                <label class="form-label">New Lease Start Date</label>

                <input type="date"
                       id="startDate"
                       name="lease_start_date"
                       class="form-control"
                       required>

                <div id="startDateError"
                     class="error-text"></div>
            </div>

            <!-- NEW END DATE -->
            <div class="mb-3">
                <label class="form-label">New Lease End Date</label>

                <input type="date"
                       id="endDate"
                       name="lease_end_date"
                       class="form-control"
                       required>

                <div id="endDateError"
                     class="error-text"></div>
            </div>

            <!-- NEW RENT -->
            <div class="mb-3">
                <label class="form-label">
                    New Monthly Rent (RM)
                </label>

                <input type="number"
                       step="0.01"
                       min="0"
                       id="rent"
                       name="monthly_rent"
                       class="form-control"
                       value="<?= htmlspecialchars($lease['monthly_rent']) ?>"
                       required>

                <div id="rentError"
                     class="error-text"></div>
            </div>

            <!-- NEW PDF -->
            <div class="mb-3">
                <label class="form-label">
                    New Lease Agreement (PDF)
                </label>

                <input type="file"
                       name="lease_document"
                       class="form-control"
                       accept=".pdf"
                       required>
            </div>

            <!-- BUTTONS -->
            <div class="d-flex justify-content-between">

                <a href="lease.php"
                   class="btn btn-secondary">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-success">
                    Renew Lease
                </button>

            </div>

        </form>

    </div>
</div>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById("renewLeaseForm")
.addEventListener("submit", function(e){

    let valid = true;

    const startDate = document.getElementById("startDate");
    const endDate = document.getElementById("endDate");
    const rent = document.getElementById("rent");

    document.querySelectorAll(".error-text")
        .forEach(el => el.textContent = "");

    document.querySelectorAll(".is-invalid")
        .forEach(el => el.classList.remove("is-invalid"));

    if(startDate.value >= endDate.value){

        endDate.classList.add("is-invalid");

        document.getElementById("endDateError").textContent =
            "End date must be later than start date.";

        valid = false;
    }

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
