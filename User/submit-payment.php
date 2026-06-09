<?php include 'includes/tenant-header.php'; ?>

<?php
// Get active lease
$leaseQuery = mysqli_query($conn, "
    SELECT l.*, p.property_name 
    FROM leases l
    JOIN properties p ON l.property_id = p.property_id
    WHERE l.tenant_id = $userId AND l.lease_status = 'active'
    LIMIT 1
");
$lease = mysqli_fetch_assoc($leaseQuery);

$message = '';
$messageType = '';

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_payment'])) {
    $paymentMonth = mysqli_real_escape_string($conn, $_POST['payment_month']);
    $paymentAmount = mysqli_real_escape_string($conn, $_POST['payment_amount']);
    $paymentMethod = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    // Handle file upload
    $receiptFile = null;
    if (isset($_FILES['receipt_file']) && $_FILES['receipt_file']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $filename = $_FILES['receipt_file']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $receiptFile = 'uploads/payments/' . time() . '_' . basename($filename);
            $uploadPath = '../Admin/' . $receiptFile;
            
            // Create directory if not exists
            if (!file_exists('../Admin/uploads/payments')) {
                mkdir('../Admin/uploads/payments', 0777, true);
            }
            
            if (move_uploaded_file($_FILES['receipt_file']['tmp_name'], $uploadPath)) {
                $insertQuery = "INSERT INTO payments (lease_id, payment_date, payment_amount, payment_method, payment_status, receipt_file) 
                                VALUES ({$lease['lease_id']}, '$paymentMonth', '$paymentAmount', '$paymentMethod', 'pending', '$receiptFile')";
                
                if (mysqli_query($conn, $insertQuery)) {
                    $message = "Payment submitted successfully! Admin will verify your payment soon.";
                    $messageType = "success";
                } else {
                    $message = "Error submitting payment: " . mysqli_error($conn);
                    $messageType = "danger";
                }
            } else {
                $message = "Error uploading receipt file.";
                $messageType = "danger";
            }
        } else {
            $message = "Invalid file type. Please upload JPG, PNG, or PDF.";
            $messageType = "danger";
        }
    } else {
        $message = "Please upload your payment receipt.";
        $messageType = "danger";
    }
}

// Get payment months (next 3 months)
$paymentMonths = [];
for ($i = 1; $i <= 3; $i++) {
    $date = date('Y-m-01', strtotime("+$i months"));
    $paymentMonths[] = $date;
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Submit Rental Payment</h2>
    
    <?php if($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if($lease): ?>
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-upload"></i> Payment Form</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Property</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($lease['property_name']); ?>" disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Monthly Rent</label>
                            <input type="text" class="form-control" value="RM <?php echo number_format($lease['monthly_rent'], 2); ?>" disabled>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Month <span class="text-danger">*</span></label>
                            <select name="payment_month" class="form-select" required>
                                <option value="">Select month</option>
                                <?php foreach($paymentMonths as $month): ?>
                                    <option value="<?php echo $month; ?>">
                                        <?php echo date('F Y', strtotime($month)); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">You can pay up to 3 months in advance.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Amount <span class="text-danger">*</span></label>
                            <input type="number" name="payment_amount" class="form-control" value="<?php echo $lease['monthly_rent']; ?>" step="0.01" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select" required>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="online_banking">Online Banking</option>
                                <option value="ewallet">E-Wallet (Touch n Go, GrabPay)</option>
                                <option value="cash">Cash (at office)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Payment Receipt <span class="text-danger">*</span></label>
                            <input type="file" name="receipt_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
                            <small class="text-muted">Upload a clear screenshot or photo of your payment receipt (JPG, PNG, or PDF).</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Bank Account Details:</strong><br>
                            Bank: Maybank<br>
                            Account Name: MLK Property Group<br>
                            Account Number: 5123-4567-8901
                        </div>
                        
                        <button type="submit" name="submit_payment" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Payment
                        </button>
                        <a href="payment-history.php" class="btn btn-secondary">View Payment History</a>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Important Notes</h5>
                </div>
                <div class="card-body">
                    <ul>
                        <li>Payment is due on the 1st of each month.</li>
                        <li>Late payments will be marked as "Overdue".</li>
                        <li>After submitting, admin will verify your payment.</li>
                        <li>You will receive a notification when your payment is approved.</li>
                        <li>Keep your receipt as proof of payment.</li>
                    </ul>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-headset"></i> Need Help?</h5>
                </div>
                <div class="card-body">
                    <p>Contact the management team:</p>
                    <p><i class="fas fa-envelope"></i> admin@gmail.com</p>
                    <p><i class="fas fa-phone"></i> +60-125845236</p>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> You don't have an active lease. Cannot submit payment.
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/tenant-footer.php'; ?>