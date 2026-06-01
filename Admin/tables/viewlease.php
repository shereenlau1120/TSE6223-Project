<?php
session_start();
include '../databaseconnection.php';

if (!isset($_GET['id'])) {
    header("Location: leasemanagement.php");
    exit();
}

$id = intval($_GET['id']);

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
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Lease</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">

<div class="card">
    <div class="card-header bg-info text-white">
        <h3>Lease Details</h3>
    </div>

    <div class="card-body">

        <p>
            <strong>Lease ID:</strong>
            <?= htmlspecialchars($lease['lease_id']); ?>
        </p>

        <p>
            <strong>Tenant Name:</strong>
            <?= htmlspecialchars($lease['tenant_name']); ?>
        </p>

        <p>
            <strong>Property ID:</strong>
            <?= htmlspecialchars($lease['property_id']); ?>
        </p>

        <p>
            <strong>Property Name:</strong>
            <?= htmlspecialchars($lease['property_name']); ?>
        </p>

        <p>
            <strong>Property Address:</strong>
            <?= htmlspecialchars($lease['address']); ?>
        </p>

        <p>
            <strong>Property Image:</strong><br>
            <?php if (!empty($lease['property_image'])) { ?>
                <img src="../<?= htmlspecialchars($lease['property_image']); ?>" alt="Property Image" class="img-fluid" style="max-width:400px;">
            <?php } else { ?>
                <span class="text-muted">No image available</span>
            <?php } ?>
        </p>

        <p>
            <strong>Lease Start Date:</strong>
            <?= htmlspecialchars($lease['lease_start_date']); ?>
        </p>

        <p>
            <strong>Lease End Date:</strong>
            <?= htmlspecialchars($lease['lease_end_date']); ?>
        </p>

        <p>
            <strong>Monthly Rent:</strong>
            RM <?= number_format($lease['monthly_rent'], 2); ?>
        </p>

        <p>
            <strong>Lease Status:</strong>
            <?= htmlspecialchars($lease['lease_status']); ?>
        </p>

        <p>
            <strong>Created At:</strong>
            <?= htmlspecialchars($lease['created_at']); ?>
        </p>

        <p>
            <strong>Lease Document:</strong><br>

            <?php if (!empty($lease['lease_document'])) { ?>
                            <a href="../uploads/<?= htmlspecialchars($lease['lease_document']); ?>"
                              target="_blank"
                              class="btn btn-sm btn-info">
                              View PDF
                            </a>
                          <?php } else { ?>
                            <span class="text-muted">No file</span>
                          <?php } ?>
        </p>

    </div>

    <div class="card-footer">
        <a href="lease.php" class="btn btn-secondary">
            Back
        </a>
    </div>

</div>

</div>

</body>
</html>