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

    // Server-side duplicate check
    $stmtCheck = $conn->prepare("SELECT property_id FROM properties WHERE property_name = ? AND property_id != ?");
    $stmtCheck->bind_param("si", $name, $property_id);
    $stmtCheck->execute();
    $stmtCheck->store_result();

    if ($stmtCheck->num_rows > 0) {
        // Name already exists
        $errorMessage = "This property name already exists.";
    } else {

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
.is-invalid { border: 2px solid #dc3545 !important; }
.error-text { color: #dc3545; font-size: 12px; margin-top: 3px; }
.preview-image { max-width:250px; border-radius:10px; }
</style>
</head>
<body>
<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Update Property</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($errorMessage)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> <?= htmlspecialchars($errorMessage) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php } ?>
            <form id="updatePropertyForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="property_id" value="<?= $property['property_id'] ?>">
                
                <div class="mb-3">
                    <label class="form-label">Property Name</label>
                    <input id="propertyName" type="text" class="form-control" name="name" value="<?= htmlspecialchars($property['property_name']); ?>" required>
                    <div id="nameError" class="error-text"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Property Type</label>
                    <select id="propertyType" name="type" class="form-control" required>
                        <option value="residential" <?= $property['property_type']=="residential" ? "selected" : "" ?>>Residential</option>
                        <option value="commercial" <?= $property['property_type']=="commercial" ? "selected" : "" ?>>Commercial</option>
                    </select>
                    <div id="typeError" class="error-text"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input id="propertyAddress" type="text" class="form-control" name="address" value="<?= htmlspecialchars($property['address']); ?>" required>
                    <div id="addressError" class="error-text"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Rental Price</label>
                    <input id="propertyPrice" type="number" step="0.01" class="form-control" name="price" value="<?= $property['rental_price']; ?>" required>
                    <div id="priceError" class="error-text"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Number of Rooms</label>
                    <input id="propertyRooms" type="number" class="form-control" name="rooms" value="<?= $property['number_of_rooms']; ?>" required>
                    <div id="roomsError" class="error-text"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Property Description</label>
                    <textarea id="propertyDescription" class="form-control" name="description" rows="3" required><?= htmlspecialchars($property['property_description']); ?></textarea>
                    <div id="descriptionError" class="error-text"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Occupancy Status</label>
                    <select id="propertyOccupancy" name="occupancy" class="form-control" required>
                        <option value="available" <?= $property['occupancy_status']=="available" ? "selected" : "" ?>>Available</option>
                        <option value="rented" <?= $property['occupancy_status']=="rented" ? "selected" : "" ?>>Rented</option>
                    </select>
                    <div id="occupancyError" class="error-text"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Current Image</label><br>
                    <?php if($property['property_image']) { ?>
                        <img src="../<?= htmlspecialchars($property['property_image']); ?>" class="preview-image">
                    <?php } ?>
                </div>

                <div class="mb-3">
                    <label class="form-label">Replace Image (Optional)</label>
                    <input id="propertyImage" type="file" class="form-control" name="Image">
                    <div id="imageError" class="error-text"></div>
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

<!-- Validation JS -->
<script>
document.getElementById("updatePropertyForm").addEventListener("submit", function(e){

    let valid = true;

    // reset UI
    document.querySelectorAll(".error-text").forEach(el => el.textContent = "");

    const name = document.getElementById("propertyName");
    const address = document.getElementById("propertyAddress");
    const price = document.getElementById("propertyPrice");
    const rooms = document.getElementById("propertyRooms");
    const description = document.getElementById("propertyDescription");
    const image = document.getElementById("propertyImage");

    // NAME (letters only)
    if(name.value.trim() === ""){
        name.classList.add("is-invalid");
        document.getElementById("nameError").textContent = "Property name is required.";
        valid = false;
    }
    else if(!/^[A-Za-z\s]+$/.test(name.value.trim())){
        name.classList.add("is-invalid");
        document.getElementById("nameError").textContent = "Only letters and spaces are allowed.";
        valid = false;
    }

    // ADDRESS
    if(address.value.trim().length < 5){
        address.classList.add("is-invalid");
        document.getElementById("addressError").textContent = "Address must be at least 5 characters long.";
        valid = false;
    }

    // PRICE
    if(price.value === "" || parseFloat(price.value) <= 0){
        price.classList.add("is-invalid");
        document.getElementById("priceError").textContent = "Price must be greater than 0.";
        valid = false;
    }

    // ROOMS
    if(rooms.value === "" || !Number.isInteger(Number(rooms.value)) || Number(rooms.value) <= 0){
        rooms.classList.add("is-invalid");
        document.getElementById("roomsError").textContent = "Rooms must be a positive whole number.";
        valid = false;
    }

    // DESCRIPTION
    if(description.value.trim().length < 10){
        description.classList.add("is-invalid");
        document.getElementById("descriptionError").textContent = "Description must be at least 10 characters long.";
        valid = false;
    }

    // IMAGE (optional)
    if(image.files.length > 0){
        const allowed = ["jpg","jpeg","png","gif","webp"];
        const ext = image.files[0].name.split(".").pop().toLowerCase();

        if(!allowed.includes(ext)){
            image.classList.add("is-invalid");
            document.getElementById("imageError").textContent = "Only JPG, JPEG, PNG, GIF and WEBP files are allowed.";
            valid = false;
        }
    }

    if(!valid){
        e.preventDefault();
    }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("updatePropertyForm");
    const nameField = document.getElementById("propertyName");
    const nameError = document.getElementById("nameError");
    const currentId = <?= $property['property_id'] ?>;

    let nameValid = true;
    let timeout;

    // ======================
    // AJAX duplicate check
    // ======================
    nameField.addEventListener("input", function () {

        clearTimeout(timeout);

        timeout = setTimeout(() => {

            const value = nameField.value.trim();
            if (value === "") return;

            fetch("updateproperty.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: `action=check_name&property_name=${encodeURIComponent(value)}&current_id=${currentId}`
            })
            .then(res => res.json())
            .then(data => {

                if (data.exists) {
                    nameValid = false;
                    nameField.classList.add("is-invalid");
                    nameError.textContent = "Property name already exists.";
                } else {
                    nameValid = true;
                    nameField.classList.remove("is-invalid");
                    nameError.textContent = "";
                }
            });

        }, 300);
    });

    // ======================
    // FORM SUBMIT
    // ======================
    form.addEventListener("submit", function (e) {

        let valid = true;

        if (!nameValid) {
            valid = false;
            nameError.textContent = "Property name already exists.";
            nameField.classList.add("is-invalid");
        }

        if (!valid) {
            e.preventDefault();
        }
    });

});
</script>
</body>
</html>