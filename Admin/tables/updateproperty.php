<?php
session_start();
include '../databaseconnection.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get property ID from GET or POST
$property_id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['property_id'] ?? 0);

// Fetch property data
$result = mysqli_query($conn, "SELECT * FROM properties WHERE property_id = $property_id");
$property = mysqli_fetch_assoc($result);

if (!$property) {
    die("Property not found.");
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $address = $_POST['address'];
    $price = $_POST['price'];
    $rooms = $_POST['rooms'];
    $description = $_POST['description'];
    $occupancy = $_POST['occupancy'];

    $imagePath = $property['property_image'];

    // Handle new image upload
    if (!empty($_FILES['Image']['name'])) {
        $folder = "../assets/img/";
        $fileName = time() . "_" . basename($_FILES["Image"]["name"]);
        $targetFile = $folder . $fileName;

        if (move_uploaded_file($_FILES["Image"]["tmp_name"], $targetFile)) {
            $imagePath = "assets/img/" . $fileName;
        }
    }

    $stmt = $conn->prepare("
        UPDATE properties
        SET property_name=?, property_type=?, address=?, rental_price=?, number_of_rooms=?, property_description=?, occupancy_status=?, property_image=?
        WHERE property_id=?
    ");

    $stmt->bind_param(
        "sssdisssi",
        $name,
        $type,
        $address,
        $price,
        $rooms,
        $description,
        $occupancy,
        $imagePath,
        $property_id
    );

    if ($stmt->execute()) {
        header("Location: propertymanagement.php");
        exit();
    } else {
        echo "Error updating property.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Property</title>
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/kaiadmin.min.css">
<style>
.preview-image{max-width:250px;border-radius:10px;}
</style>
</head>
<body>
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Update Property</h4>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="property_id" value="<?= $property['property_id'] ?>">
                
                <div class="mb-3">
                    <label class="form-label">Property Name</label>
                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($property['property_name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Property Type</label>
                    <select name="type" class="form-control" required>
                        <option value="residential" <?= $property['property_type']=="residential" ? "selected" : "" ?>>Residential</option>
                        <option value="commercial" <?= $property['property_type']=="commercial" ? "selected" : "" ?>>Commercial</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($property['address']); ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Rental Price</label>
                    <input type="number" step="0.01" class="form-control" name="price" value="<?= $property['rental_price']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Number of Rooms</label>
                    <input type="number" class="form-control" name="rooms" value="<?= $property['number_of_rooms']; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Property Description</label>
                    <textarea class="form-control" name="description" rows="3" required><?= htmlspecialchars($property['property_description']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Occupancy Status</label>
                    <select name="occupancy" class="form-control" required>
                        <option value="available" <?= $property['occupancy_status']=="available" ? "selected" : "" ?>>Available</option>
                        <option value="rented" <?= $property['occupancy_status']=="rented" ? "selected" : "" ?>>Rented</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Current Image</label><br>
                    <?php if($property['property_image']) { ?>
                        <img src="../<?= htmlspecialchars($property['property_image']); ?>" class="preview-image">
                    <?php } ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Replace Image (Optional)</label>
                    <input type="file" class="form-control" name="Image">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="propertymanagement.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Update Property</button>
                </div>

            </form>
        </div>
    </div>
</div>
<script src="../assets/js/core/bootstrap.min.js"></script>
</body>
</html>