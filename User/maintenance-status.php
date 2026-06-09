<?php include 'includes/tenant-header.php'; ?>

<?php
$requestQuery = mysqli_query($conn, "
    SELECT mr.*, p.property_name
    FROM maintenance_requests mr
    JOIN properties p ON mr.property_id = p.property_id
    WHERE mr.tenant_id = $userId
    ORDER BY mr.request_date DESC
");
?>

<div class="container-fluid">
    <h2 class="mb-4">Maintenance Request Status</h2>
    
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-clipboard-list"></i> My Requests</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Request ID</th>
                            <th>Property</th>
                            <th>Issue</th>
                            <th>Date</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Admin Remark</th>
                            <th>Image</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($requestQuery) > 0): ?>
                            <?php while($req = mysqli_fetch_assoc($requestQuery)): ?>
                            <tr>
                                <td>#<?php echo $req['request_id']; ?></td>
                                <td><?php echo htmlspecialchars($req['property_name']); ?></td>
                                <td><?php echo htmlspecialchars($req['issue_title']); ?></td>
                                <td><?php echo date('d M Y', strtotime($req['request_date'])); ?></td>
                                <td>
                                    <?php if($req['priority_level'] == 'high'): ?>
                                        <span class="badge bg-danger">High</span>
                                    <?php elseif($req['priority_level'] == 'medium'): ?>
                                        <span class="badge bg-warning">Medium</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Low</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($req['request_status'] == 'pending'): ?>
                                        <span class="badge bg-warning">Pending Review</span>
                                    <?php elseif($req['request_status'] == 'in_progress'): ?>
                                        <span class="badge bg-info">In Progress</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Completed</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $req['admin_remark'] ? htmlspecialchars($req['admin_remark']) : '-'; ?>
                                </td>
                                <td>
                                    <?php if($req['issue_image']): ?>
                                        <a href="../Admin/<?php echo $req['issue_image']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-image"></i> View
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    No maintenance requests found.
                                    <br>
                                    <a href="maintenance-request.php" class="btn btn-primary mt-2">
                                        <i class="fas fa-plus"></i> Submit First Request
                                    </a>
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