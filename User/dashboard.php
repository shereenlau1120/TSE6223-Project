<?php include 'includes/tenant-header.php'; ?>

<?php
// Get tenant's active lease
$leaseQuery = mysqli_query($conn, "
    SELECT l.*, p.property_name, p.address, p.property_image 
    FROM leases l
    JOIN properties p ON l.property_id = p.property_id
    WHERE l.tenant_id = $userId AND l.lease_status = 'active'
    LIMIT 1
");
$activeLease = mysqli_fetch_assoc($leaseQuery);

// Get upcoming payment (next month's rent)
$upcomingPayment = null;
if ($activeLease) {
    $nextMonth = date('Y-m-01', strtotime('+1 month'));
    $paymentCheck = mysqli_query($conn, "
        SELECT * FROM payments 
        WHERE lease_id = {$activeLease['lease_id']} 
        AND payment_date >= CURDATE()
        AND payment_status = 'pending'
        ORDER BY payment_date ASC
        LIMIT 1
    ");
    $upcomingPayment = mysqli_fetch_assoc($paymentCheck);
}

// Get recent maintenance requests
$maintenanceQuery = mysqli_query($conn, "
    SELECT * FROM maintenance_requests 
    WHERE tenant_id = $userId 
    ORDER BY request_date DESC 
    LIMIT 5
");

// Get recent payments
$paymentQuery = mysqli_query($conn, "
    SELECT p.*, l.monthly_rent 
    FROM payments p
    JOIN leases l ON p.lease_id = l.lease_id
    WHERE l.tenant_id = $userId
    ORDER BY p.payment_date DESC
    LIMIT 5
");

// Count statistics
$totalPaid = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT SUM(payment_amount) AS total 
    FROM payments p
    JOIN leases l ON p.lease_id = l.lease_id
    WHERE l.tenant_id = $userId AND p.payment_status = 'paid'
"))['total'] ?? 0;

$pendingMaintenance = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM maintenance_requests 
    WHERE tenant_id = $userId AND request_status != 'completed'
"))['total'] ?? 0;

$overduePayment = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM payments p
    JOIN leases l ON p.lease_id = l.lease_id
    WHERE l.tenant_id = $userId AND p.payment_status = 'overdue'
"))['total'] ?? 0;
?>

<div class="container-fluid">
    <h2 class="mb-4">Welcome back, <?php echo htmlspecialchars($userName); ?>!</h2>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Paid</h6>
                        <h3 class="mb-0">RM <?php echo number_format($totalPaid, 2); ?></h3>
                    </div>
                    <i class="fas fa-wallet fa-2x text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Pending Maintenance</h6>
                        <h3 class="mb-0"><?php echo $pendingMaintenance; ?></h3>
                    </div>
                    <i class="fas fa-tools fa-2x text-warning"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Overdue Payments</h6>
                        <h3 class="mb-0 text-danger"><?php echo $overduePayment; ?></h3>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Monthly Rent</h6>
                        <h3 class="mb-0">RM <?php echo number_format($activeLease['monthly_rent'] ?? 0, 2); ?></h3>
                    </div>
                    <i class="fas fa-home fa-2x text-primary"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Active Rental Card -->
    <?php if($activeLease): ?>
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-home"></i> Current Rental</h5>
                </div>
                <div class="card-body">
                    <h4><?php echo htmlspecialchars($activeLease['property_name']); ?></h4>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($activeLease['address']); ?></p>
                    <p><i class="fas fa-calendar"></i> Lease Period: <?php echo date('d M Y', strtotime($activeLease['lease_start_date'])); ?> - <?php echo date('d M Y', strtotime($activeLease['lease_end_date'])); ?></p>
                    <p><i class="fas fa-money-bill"></i> Monthly Rent: <strong>RM <?php echo number_format($activeLease['monthly_rent'], 2); ?></strong></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-bell"></i> Upcoming Payment</h5>
                </div>
                <div class="card-body">
                    <?php if($upcomingPayment): ?>
                        <p>Payment due for: <strong><?php echo date('F Y', strtotime($upcomingPayment['payment_date'])); ?></strong></p>
                        <p>Amount: <strong>RM <?php echo number_format($upcomingPayment['payment_amount'], 2); ?></strong></p>
                        <p>Status: <span class="badge bg-warning"><?php echo ucfirst($upcomingPayment['payment_status']); ?></span></p>
                        <a href="submit-payment.php" class="btn btn-primary mt-2">Submit Payment Now</a>
                    <?php else: ?>
                        <p class="text-success">No pending payments! All up to date.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> You don't have an active lease. Please contact the admin for assistance.
    </div>
    <?php endif; ?>
    
    <!-- Recent Maintenance Requests -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tools"></i> Recent Maintenance Requests</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Issue</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($req = mysqli_fetch_assoc($maintenanceQuery)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($req['issue_title']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($req['request_date'])); ?></td>
                                    <td>
                                        <?php if($req['request_status'] == 'pending'): ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php elseif($req['request_status'] == 'in_progress'): ?>
                                            <span class="badge bg-info">In Progress</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Completed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if(mysqli_num_rows($maintenanceQuery) == 0): ?>
                                <tr>
                                    <td colspan="3" class="text-center">No maintenance requests yet.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="maintenance-status.php" class="btn btn-sm btn-outline-primary">View All Requests</a>
                    <a href="maintenance-request.php" class="btn btn-sm btn-primary float-end">New Request</a>
                </div>
            </div>
        </div>
        
        <!-- Recent Payments -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Recent Payments</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($pay = mysqli_fetch_assoc($paymentQuery)): ?>
                                <tr>
                                    <td><?php echo date('d M Y', strtotime($pay['payment_date'])); ?></td>
                                    <td>RM <?php echo number_format($pay['payment_amount'], 2); ?></td>
                                    <td>
                                        <?php if($pay['payment_status'] == 'paid'): ?>
                                            <span class="badge bg-success">Paid</span>
                                        <?php elseif($pay['payment_status'] == 'pending'): ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Overdue</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if(mysqli_num_rows($paymentQuery) == 0): ?>
                                <tr>
                                    <td colspan="3" class="text-center">No payment records yet.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="payment-history.php" class="btn btn-sm btn-outline-primary">View All Payments</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle sidebar
document.getElementById('toggleSidebar').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('collapsed');
    document.getElementById('mainContent').classList.toggle('expanded');
});
</script>

<?php include 'includes/tenant-footer.php'; ?>