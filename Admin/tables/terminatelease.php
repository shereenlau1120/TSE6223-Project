<?php

session_start();
include '../databaseconnection.php';

$id = intval($_GET['id']);

/* get property */
$getLease = mysqli_query(
    $conn,
    "SELECT property_id
     FROM leases
     WHERE lease_id = $id"
);

$lease = mysqli_fetch_assoc($getLease);

/* terminate lease */
mysqli_query(
    $conn,
    "UPDATE leases
     SET lease_status='terminated'
     WHERE lease_id = $id"
);

/* property available again */
mysqli_query(
    $conn,
    "UPDATE properties
     SET occupancy_status='available'
     WHERE property_id=".$lease['property_id']
);

header("Location: lease.php");
exit();