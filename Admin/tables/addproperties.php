<?php
session_start();
include '..\databaseconnection.php';

// redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (isset($_POST['add_property'])) {

    $name = $_POST['name'];
    $type = $_POST['type'];
    $address = $_POST['address'];
    $price = $_POST['price'];
    $rooms = $_POST['rooms'];
    $description = $_POST['description'];
    $occupancy = $_POST['occupancy'];

    $imagePath = null;

    // handle image upload
    if (!empty($_FILES['Image']['name'])) {

        $folder = "../assets/img/";
        $fileName = time() . "_" . basename($_FILES["Image"]["name"]);

        $targetFile = $folder . $fileName;

        if (move_uploaded_file($_FILES["Image"]["tmp_name"], $targetFile)) {
            $imagePath = "assets/img/" . $fileName;
        }
    }

    // Check if property name already exists
    $checkStmt = $conn->prepare(
    "SELECT property_id FROM properties WHERE property_name = ?"
    );
    $checkStmt->bind_param("s", $name);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
    header("Location: propertymanagement.php?error=duplicate_name");
    exit();
}

    // insert into database
    $stmt = $conn->prepare("
        INSERT INTO properties 
        (property_name, property_type, address, rental_price, number_of_rooms, property_description, occupancy_status, property_image, activation)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')
    ");

    $stmt->bind_param("sssddsss", $name, $type, $address, $price, $rooms, $description, $occupancy, $imagePath);

    $stmt->execute();

    header("Location: propertymanagement.php");
    exit();
}
?>