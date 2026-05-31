<?php
session_start();
include '../databaseconnection.php';

if (isset($_POST['property_id'])) {
    $id = $_POST['property_id'];

    $query = mysqli_query($conn, "SELECT activation FROM properties WHERE property_id = $id");
    $row = mysqli_fetch_assoc($query);
    $newStatus = $row['activation'] == 'active' ? 'inactive' : 'active';

    mysqli_query($conn, "UPDATE properties SET activation='$newStatus' WHERE property_id = $id");
}

header("Location: propertymanagement.php");
exit();
?>