<?php
include '../databaseconnection.php';

$id = intval($_POST['id']);
$status = $_POST['status'] ?? $_GET['status'];
$remarks = $_POST['remarks'] ?? null;

/* VALIDATE */
if (!in_array($status, ['paid','rejected'])) {
    die("Invalid status");
}

/* REQUIRE REMARK IF REJECTED */
if ($status == 'rejected' && empty($remarks)) {
    die("Remark required");
}

/* UPDATE */
$stmt = $conn->prepare("
    UPDATE payments
    SET payment_status = ?, remarks = ?
    WHERE payment_id = ?
");

$stmt->bind_param("ssi", $status, $remarks, $id);
$stmt->execute();

/* GET TENANT */
$get = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT l.tenant_id
    FROM payments p
    JOIN leases l ON p.lease_id = l.lease_id
    WHERE p.payment_id = $id
"));

if ($get) {

    $msg = ($status == 'paid')
        ? "Your payment has been approved."
        : "Payment rejected: $remarks";

    mysqli_query($conn, "
        INSERT INTO notifications
        (user_id, notification_title, notification_message, notification_type)
        VALUES (
            {$get['tenant_id']},
            'Payment Update',
            '$msg',
            'payment'
        )
    ");
}

header("Location: paymentmanagement.php");
exit();
?>