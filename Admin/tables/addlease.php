<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

include '../databaseconnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $tenant_id = intval($_POST['tenant_id']);
    $property_id = intval($_POST['property_id']);
    $lease_start_date = $_POST['lease_start_date'];
    $lease_end_date = $_POST['lease_end_date'];
    $monthly_rent = floatval($_POST['monthly_rent']);
    $lease_document = "";

    // Validation
    if (
    empty($tenant_id) ||
    empty($property_id) ||
    empty($lease_start_date) ||
    empty($lease_end_date) ||
    empty($monthly_rent)
    ) {
    die("All fields are required.");
    }

if (!isset($_FILES['lease_document']) || $_FILES['lease_document']['error'] != 0) {
    die("Lease agreement PDF is required.");
}

    // End date must be after start date
    if (strtotime($lease_end_date) <= strtotime($lease_start_date)) {
        die("Lease end date must be after start date.");
    }

    // Check tenant exists
    $tenantCheck = $conn->prepare("
        SELECT user_id
        FROM users
        WHERE user_id = ?
        AND role = 'tenant'
        AND status = 'active'
    ");
    $tenantCheck->bind_param("i", $tenant_id);
    $tenantCheck->execute();
    $tenantCheck->store_result();

    if ($tenantCheck->num_rows == 0) {
        die("Invalid tenant selected.");
    }

    // Check property exists and available
    $propertyCheck = $conn->prepare("
        SELECT property_id
        FROM properties
        WHERE property_id = ?
        AND occupancy_status = 'available'
        AND activation = 'active'
    ");
    $propertyCheck->bind_param("i", $property_id);
    $propertyCheck->execute();
    $propertyCheck->store_result();

    if ($propertyCheck->num_rows == 0) {
        die("Selected property is not available.");
    }

    $uploadDir = "../uploads/leases/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$fileExt = strtolower(pathinfo(
    $_FILES['lease_document']['name'],
    PATHINFO_EXTENSION
));

if ($fileExt !== "pdf") {
    die("Only PDF files are allowed.");
}

$fileName = time() . "_" . preg_replace(
    "/[^a-zA-Z0-9._-]/",
    "",
    $_FILES['lease_document']['name']
);

$targetFile = $uploadDir . $fileName;

if (!move_uploaded_file(
    $_FILES['lease_document']['tmp_name'],
    $targetFile
)) {
    die("Failed to upload PDF.");
}

$lease_document = "leases/" . $fileName;

    // Begin transaction
    mysqli_begin_transaction($conn);

    try {

        // Insert lease
        $stmt = $conn->prepare("
            INSERT INTO leases (
            tenant_id,
            property_id,
            lease_start_date,
            lease_end_date,
            monthly_rent,
            lease_document,
            lease_status
         )
        VALUES (?, ?, ?, ?, ?, ?, 'active')
        ");

        $stmt->bind_param(
            "iissds",
            $tenant_id,
            $property_id,
            $lease_start_date,
            $lease_end_date,
            $monthly_rent,
            $lease_document
        );

        $stmt->execute();

        // Update property occupancy
        $updateProperty = $conn->prepare("
            UPDATE properties
            SET occupancy_status = 'rented'
            WHERE property_id = ?
        ");

        $updateProperty->bind_param("i", $property_id);
        $updateProperty->execute();

        mysqli_commit($conn);

        header("Location: lease.php");
        exit();

    } catch (Exception $e) {

        mysqli_rollback($conn);

        die("Failed to create lease.");
    }
}
?>