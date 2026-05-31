<?php
session_start();
include '../databaseconnection.php';

if (!isset($_GET['id'])) {
    header("Location: propertymanagement.php");
    exit();
}

$id = intval($_GET['id']);

$result = mysqli_query(
    $conn,
    "SELECT * FROM users WHERE user_id = $id"
);

$tenant = mysqli_fetch_assoc($result);

if (!$tenant) {
    die("Tenant not found.");
}
?>

<!DOCTYPE html>

<html>
<head>
    <title>View Tenant</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">

<div class="card">
    <div class="card-header bg-info text-white">
        <h3>Tenant Details</h3>
    </div>

    <div class="card-body">

        <p><strong>Name:</strong> <?= htmlspecialchars($tenant['full_name']); ?></p>

        <p><strong>Email:</strong> <?= htmlspecialchars($tenant['email']); ?></p>

        <p><strong>Phone Number:</strong> <?= htmlspecialchars($tenant['phone_number']); ?></p>

        <p><strong>Status:</strong>
            <?= htmlspecialchars($tenant['status']); ?>
        </p>

        <?php if (!empty($tenant['pictures'])) { ?>
            <img src="../<?= htmlspecialchars($tenant['pictures']); ?>"
                 width="300"
                 class="img-thumbnail">
        <?php } ?>

    </div>

    <div class="card-footer">
        <a href="tenantmanagement.php" class="btn btn-secondary">
            Back
        </a>
    </div>

</div>

</div>

</body>
</html>

