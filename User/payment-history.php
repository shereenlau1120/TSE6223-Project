<?php include 'includes/tenant-header.php'; ?>

<?php
$paymentQuery = mysqli_query($conn, "
    SELECT p.*, l.monthly_rent, l.lease_id
    FROM payments p
    JOIN leases l ON p.lease_id = l.lease_id
    WHERE l.tenant_id = $userId
    ORDER BY p.payment_date DESC
");

// Calculate summary
$totalPaid = 0;
$totalPending = 0;
$totalOverdue = 0;

$payments = [];
while($row = mysqli_fetch_assoc($paymentQuery)) {
    $payments[] = $row;
    if($row['payment_status'] == 'paid') $totalPaid += $row['payment_amount'];
    if($row['payment_status'] == 'pending') $totalPending += $row['payment_amount'];
    if($row['payment_status'] == 'overdue') $totalOverdue += $row['payment_amount'];
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Payment History</h2>
    
    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Paid</h6>
                        <h3 class="mb-0 text-success">RM <?php echo number_format($totalPaid, 2); ?></h3>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Pending Verification</h6>
                        <h3 class="mb-0 text-warning">RM <?php echo number_format($totalPending, 2); ?></h3>
                    </div>
                    <i class="fas fa-clock fa-2x text-warning"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Overdue</h6>
                        <h3 class="mb-0 text-danger">RM <?php echo number_format($totalOverdue, 2); ?></h3>
                    </div>
                    <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Payment Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> All Payments</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Payment Date</th>
                            <th>Month</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Receipt</th>
                         </tr>
                    </thead>
                    <tbody>
                        <?php if(count($payments) > 0): ?>
                            <?php foreach($payments as $pay): ?>
                            <tr>
                                <td><?php echo date('d M Y', strtotime($pay['payment_date'])); ?></td>
                                <td><?php echo date('F Y', strtotime($pay['payment_date'])); ?></td>
                                <td>RM <?php echo number_format($pay['payment_amount'], 2); ?></td>
                                <td>
                                    <?php 
                                        $methods = [
                                            'bank_transfer' => 'Bank Transfer',
                                            'online_banking' => 'Online Banking',
                                            'ewallet' => 'E-Wallet',
                                            'cash' => 'Cash'
                                        ];
                                        echo $methods[$pay['payment_method']] ?? ucfirst($pay['payment_method']);
                                    ?>
                                </td>
                                <td>
                                    <?php if($pay['payment_status'] == 'paid'): ?>
                                        <span class="badge bg-success">Paid</span>
                                    <?php elseif($pay['payment_status'] == 'pending'): ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php elseif($pay['payment_status'] == 'rejected'): ?>
                                        <span class="badge bg-danger">Rejected</span>
                                        <?php if($pay['remarks']): ?>
                                            <i class="fas fa-info-circle text-muted" title="<?php echo htmlspecialchars($pay['remarks']); ?>"></i>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Overdue</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($pay['receipt_file']): ?>
                                        <a href="../Admin/<?php echo $pay['receipt_file']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No file</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">No payment records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <a href="submit-payment.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Make New Payment
            </a>
        </div>
    </div>
</div>

<?php include 'includes/tenant-footer.php'; ?>