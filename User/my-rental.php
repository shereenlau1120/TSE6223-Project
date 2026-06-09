<?php include 'includes/tenant-header.php'; ?>

<?php
// Get tenant's active lease with property details
$leaseQuery = mysqli_query($conn, "
    SELECT l.*, p.property_name, p.address, p.property_type, 
           p.number_of_rooms, p.property_description, p.property_image
    FROM leases l
    JOIN properties p ON l.property_id = p.property_id
    WHERE l.tenant_id = $userId AND l.lease_status = 'active'
    LIMIT 1
");
$lease = mysqli_fetch_assoc($leaseQuery);
?>

<div class="container-fluid">
    <h2 class="mb-4">My Rental Details</h2>
    
    <?php if($lease): ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-building"></i> Property Information</h5>
                </div>
                <div class="card-body">
                    <img src="../Admin/<?php echo htmlspecialchars($lease['property_image']); ?>" class="img-fluid rounded mb-3" style="height: 250px; width: 100%; object-fit: cover;">
                    <h4><?php echo htmlspecialchars($lease['property_name']); ?></h4>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($lease['address']); ?></p>
                    <p><i class="fas fa-building"></i> Property Type: <?php echo ucfirst($lease['property_type']); ?></p>
                    <p><i class="fas fa-bed"></i> Number of Rooms: <?php echo $lease['number_of_rooms']; ?></p>
                    <p><i class="fas fa-align-left"></i> Description: <?php echo nl2br(htmlspecialchars($lease['property_description'])); ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-contract"></i> Lease Agreement</h5>
                </div>
                <div class="card-body">
                    <p><i class="fas fa-calendar-alt"></i> Start Date: <strong><?php echo date('d F Y', strtotime($lease['lease_start_date'])); ?></strong></p>
                    <p><i class="fas fa-calendar-alt"></i> End Date: <strong><?php echo date('d F Y', strtotime($lease['lease_end_date'])); ?></strong></p>
                    <p><i class="fas fa-money-bill"></i> Monthly Rent: <strong>RM <?php echo number_format($lease['monthly_rent'], 2); ?></strong></p>
                    <p><i class="fas fa-clock"></i> Time Remaining: 
                        <?php
                        $endDate = new DateTime($lease['lease_end_date']);
                        $today = new DateTime();
                        $diff = $today->diff($endDate);
                        echo $diff->y . ' years, ' . $diff->m . ' months, ' . $diff->d . ' days remaining';
                        ?>
                    </p>
                    <?php if($lease['lease_document']): ?>
                        <a href="../Admin/<?php echo $lease['lease_document']; ?>" class="btn btn-outline-primary" target="_blank">
                            <i class="fas fa-download"></i> Download Lease Document
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-question-circle"></i> Need Help?</h5>
                </div>
                <div class="card-body">
                    <p>For any questions regarding your lease or rental:</p>
                    <p><i class="fas fa-envelope"></i> Email: admin@gmail.com</p>
                    <p><i class="fas fa-phone"></i> Phone: +60-125845236</p>
                    <a href="maintenance-request.php" class="btn btn-warning">Report Maintenance Issue</a>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> You don't have an active lease. Please contact the property manager.
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/tenant-footer.php'; ?>