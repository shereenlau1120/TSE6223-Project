<?php
include '../databaseconnection.php';

$id = intval($_POST['id']);
$status = $_POST['status'];
$remark = $_POST['remark'];

if(!in_array($status, ['pending','in_progress','completed'])){
    die("Invalid status");
}

/* update completed date if completed */
if($status == 'completed'){
    $query = "
        UPDATE maintenance_requests
        SET request_status='$status',
            admin_remark='$remark',
            completed_date=NOW()
        WHERE request_id=$id
    ";
} else {
    $query = "
        UPDATE maintenance_requests
        SET request_status='$status',
            admin_remark='$remark'
        WHERE request_id=$id
    ";
}

mysqli_query($conn, $query);

/* GET TENANT */
$get = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT tenant_id FROM maintenance_requests WHERE request_id=$id
"));

if($get){

    $msg = "Your maintenance request status updated to: $status";

    mysqli_query($conn, "
        INSERT INTO notifications
        (user_id, notification_title, notification_message, notification_type)
        VALUES (
            {$get['tenant_id']},
            'Maintenance Update',
            '$msg',
            'maintenance'
        )
    ");
}

header("Location: maintenancerequest.php");
exit();
?>