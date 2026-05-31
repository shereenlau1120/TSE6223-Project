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
    "SELECT * FROM properties WHERE property_id = $id"
);

$property = mysqli_fetch_assoc($result);

if (!$property) {
    die("Property not found.");
}
?>

<!DOCTYPE html>

<html>
<head>
    <title>View Property</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">

<div class="card">
    <div class="card-header bg-info text-white">
        <h3>Property Details</h3>
    </div>

    <div class="card-body">

        <p><strong>Name:</strong> <?= htmlspecialchars($property['property_name']); ?></p>

        <p><strong>Type:</strong> <?= htmlspecialchars($property['property_type']); ?></p>

        <p><strong>Address:</strong> <?= htmlspecialchars($property['address']); ?></p>

        <p><strong>Rental Price:</strong>
            RM <?= number_format($property['rental_price'],2); ?>
        </p>

        <p><strong>Rooms:</strong>
            <?= htmlspecialchars($property['number_of_rooms']); ?>
        </p>

        <p><strong>Description:</strong><br>
            <?= nl2br(htmlspecialchars($property['property_description'])); ?>
        </p>

        <p><strong>Occupancy:</strong>
            <?= htmlspecialchars($property['occupancy_status']); ?>
        </p>

        <p><strong>Activation:</strong>
            <?= htmlspecialchars($property['activation']); ?>
        </p>

        <?php if (!empty($property['property_image'])) { ?>
            <img src="../<?= htmlspecialchars($property['property_image']); ?>"
                 width="300"
                 class="img-thumbnail">
        <?php } ?>

    </div>

    <div class="card-footer">
        <a href="propertymanagement.php" class="btn btn-secondary">
            Back
        </a>
    </div>

</div>

</div>

</body>
</html>
?>
