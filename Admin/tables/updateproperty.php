<?php
session_start();
include '../databaseconnection.php';

// redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// check POST request
if (isset($_POST['property_id'])) {

    $property_id = intval($_POST['property_id']);
    $name = $_POST['name'];
    $type = $_POST['type'];
    $address = $_POST['address'];
    $price = $_POST['price'];
    $rooms = $_POST['rooms'];
    $description = $_POST['description'];
    $occupancy = $_POST['occupancy'];

    // get current image path
    $currentImageQuery = mysqli_query($conn, "SELECT property_image FROM properties WHERE property_id = $property_id");
    $currentImage = mysqli_fetch_assoc($currentImageQuery)['property_image'];

    $imagePath = $currentImage;

    // handle new image upload if any
    if (!empty($_FILES['Image']['name'])) {
        $folder = "../assets/img/";
        $fileName = time() . "_" . basename($_FILES["Image"]["name"]);
        $targetFile = $folder . $fileName;

        if (move_uploaded_file($_FILES["Image"]["tmp_name"], $targetFile)) {
            $imagePath = "assets/img/" . $fileName;
        }
    }

    // update database
    $stmt = $conn->prepare("
        UPDATE properties 
        SET property_name = ?, property_type = ?, address = ?, rental_price = ?, 
            number_of_rooms = ?, property_description = ?, occupancy_status = ?, property_image = ?
        WHERE property_id = ?
    ");

    $stmt->bind_param("sssdiisss", $name, $type, $address, $price, $rooms, $description, $occupancy, $imagePath, $property_id);

    if ($stmt->execute()) {
        header("Location: propertymanagement.php");
        exit();
    } else {
        die("Error updating property: " . $conn->error);
    }
}
?>