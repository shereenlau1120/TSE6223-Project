<?php
session_start();
include '../databaseconnection.php';

if (isset($_POST['user_id'])) {
    $id = $_POST['user_id'];

    $query = mysqli_query($conn, "SELECT status FROM users WHERE user_id = $id");
    $row = mysqli_fetch_assoc($query);
    $newStatus = $row['status'] == 'active' ? 'inactive' : 'active';

    mysqli_query($conn, "UPDATE users SET status='$newStatus' WHERE user_id = $id");
}

header("Location: tenantmanagement.php");
exit();
?>