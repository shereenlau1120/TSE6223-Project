<?php include 'includes/tenant-header.php'; ?>

<?php
$requestsQuery = mysqli_query($conn, "
    SELECT rr.*, p.property_name, p.property_image, p.rental_price
    FROM rent_requests rr
    JOIN properties p ON rr.property_id = p.property_id
    WHERE rr.tenant_id = $userId
    ORDER BY rr.request_date DESC
");
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-clock"></i> My Rent Requests</h2>
    
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Property</th>
                            <th>Request Date</th>
                            <th>Status</th>
                            <th>Admin Remark</th>
                            <th>Processed Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($requestsQuery) > 0): ?>
                            <?php while($req = mysqli_fetch_assoc($requestsQuery)): ?>
                            <tr>
                                <td>
                                    <img src="../Admin/<?php echo $req['property_image']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;" class="me-2">
                                    <?php echo htmlspecialchars($req['property_name']); ?>
                                    <br>
                                    <small class="text-muted">RM <?php echo number_format($req['rental_price'], 2); ?>/month</small>
                                </td>
                                <td><?php echo date('d M Y, h:i A', strtotime($req['request_date'])); ?></td>
                                <td>
                                    <?php if($req['request_status'] == 'pending'): ?>
                                        <span class="badge bg-warning">Pending Review</span>
                                    <?php elseif($req['request_status'] == 'approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $req['admin_remark'] ? htmlspecialchars($req['admin_remark']) : '-'; ?></td>
                                <td><?php echo $req['processed_date'] ? date('d M Y', strtotime($req['processed_date'])) : '-'; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">You haven't submitted any rent requests yet.</p>
                                    <a href="available-properties.php" class="btn btn-primary">Browse Properties</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/tenant-footer.php'; ?>