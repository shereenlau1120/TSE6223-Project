<?php

include '../databaseconnection.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename=monthly_income_report.csv');

$output = fopen("php://output", "w");

fputcsv(
    $output,
    [
        'Month',
        'Total Income (RM)'
    ]
);

$query = mysqli_query(
    $conn,
    "SELECT
        DATE_FORMAT(payment_date,'%Y-%m') AS month,
        SUM(payment_amount) AS total_income
     FROM payments
     WHERE payment_status='paid'
     GROUP BY DATE_FORMAT(payment_date,'%Y-%m')
     ORDER BY month ASC"
);

while($row = mysqli_fetch_assoc($query))
{
    fputcsv(
        $output,
        [
            $row['month'],
            number_format($row['total_income'],2)
        ]
    );
}

fclose($output);
exit;