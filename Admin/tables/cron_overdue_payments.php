<?php
include '../databaseconnection.php';

/* STEP 1: FIND OVERDUE PAYMENTS */
$result = mysqli_query($conn, "
    SELECT p.payment_id, l.tenant_id
    FROM payments p
    JOIN leases l ON p.lease_id = l.lease_id
    WHERE p.payment_status = 'pending'
    AND p.payment_date < CURDATE() - INTERVAL 3 DAY
");

/* STEP 2: UPDATE + NOTIFY */
while ($row = mysqli_fetch_assoc($result)) {

    $payment_id = $row['payment_id'];
    $tenant_id = $row['tenant_id'];

    /* update status */
    mysqli_query($conn, "
        UPDATE payments
        SET payment_status = 'overdue'
        WHERE payment_id = $payment_id
    ");

    /* insert notification */
    mysqli_query($conn, "
        INSERT INTO notifications
        (user_id, notification_title, notification_message, notification_type)
        VALUES (
            $tenant_id,
            'Payment Overdue',
            'Your rental payment is overdue. Please upload receipt immediately.',
            'payment'
        )
    ");
}

echo "Overdue check completed.";
?>