<?php
include '../databaseconnection.php';

/* =========================
   GET ID (support GET + POST)
   ========================= */
$id = intval($_POST['id'] ?? $_GET['id'] ?? 0);

if ($id <= 0) {
    die("Invalid payment ID");
}

/* =========================
   GET STATUS (GET or POST)
   ========================= */
$status = $_POST['status'] ?? $_GET['status'] ?? null;

/* =========================
   GET REMARKS (only POST)
   ========================= */
$remarks = $_POST['remarks'] ?? null;

/* =========================
   VALIDATE STATUS
   ========================= */
if (!in_array($status, ['paid', 'rejected'])) {
    die("Invalid status");
}

/* =========================
   REQUIRE REMARK IF REJECT
   ========================= */
if ($status == 'rejected' && empty($remarks)) {
    die("Remark is required for rejection");
}

/* =========================
   UPDATE PAYMENT
   ========================= */
$stmt = $conn->prepare("
    UPDATE payments
    SET payment_status = ?, remarks = ?
    WHERE payment_id = ?
");

$stmt->bind_param("ssi", $status, $remarks, $id);
$stmt->execute();

/* =========================
   GET TENANT ID
   ========================= */
$get = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT l.tenant_id
    FROM payments p
    JOIN leases l ON p.lease_id = l.lease_id
    WHERE p.payment_id = $id
"));

/* =========================
   SEND NOTIFICATION
   ========================= */
if ($get) {

    $msg = ($status == 'paid')
        ? "Your payment has been approved."
        : "Your payment was rejected. Reason: $remarks";

    $stmt2 = $conn->prepare("
        INSERT INTO notifications
        (user_id, notification_title, notification_message, notification_type)
        VALUES (?, 'Payment Update', ?, 'payment')
    ");

    $stmt2->bind_param("is", $get['tenant_id'], $msg);
    $stmt2->execute();
}

/* =========================
   REDIRECT BACK
   ========================= */
header("Location: payments.php");
exit();
?>