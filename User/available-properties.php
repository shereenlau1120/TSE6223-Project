<?php include 'includes/tenant-header.php'; ?>

<?php
// Get all available properties (not rented)
$propertiesQuery = mysqli_query($conn, "
    SELECT * FROM properties 
    WHERE occupancy_status = 'available' AND activation = 'active'
    ORDER BY property_id DESC
");

// Check if tenant already has an active lease
$hasActiveLease = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM leases 
    WHERE tenant_id = $userId AND lease_status = 'active'
"))['total'] > 0;

// Handle rent request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_rent'])) {
    $propertyId = mysqli_real_escape_string($conn, $_POST['property_id']);
    
    // Check if tenant already has active lease
    if ($hasActiveLease) {
        $message = "You already have an active lease. Please contact admin to terminate your current lease first.";
        $messageType = "danger";
    } else {
        // Insert rent request (will be approved by admin)
        $insertQuery = "INSERT INTO rent_requests (tenant_id, property_id, request_date, request_status) 
                        VALUES ($userId, $propertyId, NOW(), 'pending')";
        
        if (mysqli_query($conn, $insertQuery)) {
            $message = "Your rent request has been submitted! Admin will review and contact you soon.";
            $messageType = "success";
            
            // Create notification for admin
            $adminQuery = mysqli_query($conn, "SELECT user_id FROM users WHERE role = 'admin' LIMIT 1");
            $admin = mysqli_fetch_assoc($adminQuery);
            if ($admin) {
                mysqli_query($conn, "
                    INSERT INTO notifications (user_id, notification_title, notification_message, notification_type, is_read) 
                    VALUES ({$admin['user_id']}, 'New Rent Request', 'Tenant {$userName} has requested to rent a property.', 'system', 0)
                ");
            }
        } else {
            $message = "Error submitting request: " . mysqli_error($conn);
            $messageType = "danger";
        }
    }
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-building"></i> Available Properties for Rent</h2>
        <a href="my-rental.php" class="btn btn-outline-primary">
            <i class="fas fa-home"></i> View My Current Rental
        </a>
    </div>
    
    <?php if(isset($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if($hasActiveLease): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> 
            <strong>Note:</strong> You already have an active lease. You cannot rent another property until your current lease ends or is terminated.
            <br><br>
            <a href="my-rental.php" class="btn btn-warning btn-sm">View My Current Lease</a>
        </div>
    <?php endif; ?>
    
    <?php if(mysqli_num_rows($propertiesQuery) > 0): ?>
        <div class="row">
            <?php while($property = mysqli_fetch_assoc($propertiesQuery)): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card property-card h-100">
                    <img src="../Admin/<?php echo htmlspecialchars($property['property_image']); ?>" 
                         class="card-img-top" 
                         style="height: 200px; object-fit: cover;"
                         alt="<?php echo htmlspecialchars($property['property_name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($property['property_name']); ?></h5>
                        <p class="card-text text-muted">
                            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars(substr($property['address'], 0, 50)); ?>...
                        </p>
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Property Type</small>
                                <p><strong><?php echo ucfirst($property['property_type']); ?></strong></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Rooms</small>
                                <p><strong><?php echo $property['number_of_rooms'] ?? 'N/A'; ?> rooms</strong></p>
                            </div>
                        </div>
                        <div class="price-tag mb-3">
                            <h4 class="text-primary">RM <?php echo number_format($property['rental_price'], 2); ?></h4>
                            <small class="text-muted">per month</small>
                        </div>
                        <p class="card-text">
                            <small><?php echo htmlspecialchars(substr($property['property_description'], 0, 100)); ?>...</small>
                        </p>
                    </div>
                    <div class="card-footer bg-white border-0 pb-3">
                        <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#rentModal<?php echo $property['property_id']; ?>"
                            <?php echo $hasActiveLease ? 'disabled' : ''; ?>>
                            <i class="fas fa-hand-peace"></i> Request to Rent
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Rent Request Modal -->
            <div class="modal fade" id="rentModal<?php echo $property['property_id']; ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Request to Rent</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="property_id" value="<?php echo $property['property_id']; ?>">
                                <h5><?php echo htmlspecialchars($property['property_name']); ?></h5>
                                <p><strong>Monthly Rent:</strong> RM <?php echo number_format($property['rental_price'], 2); ?></p>
                                <p><strong>Property Type:</strong> <?php echo ucfirst($property['property_type']); ?></p>
                                <p><strong>Address:</strong> <?php echo htmlspecialchars($property['address']); ?></p>
                                <hr>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    After submitting, admin will review your request. You will be notified once approved.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" name="request_rent" class="btn btn-primary">Submit Request</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-building fa-3x mb-3"></i>
            <h4>No properties available for rent at the moment.</h4>
            <p>Please check back later or contact the management for assistance.</p>
        </div>
    <?php endif; ?>
</div>

<style>
.property-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border-radius: 12px;
    overflow: hidden;
}

.property-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.price-tag h4 {
    margin-bottom: 0;
}
</style>

<?php include 'includes/tenant-footer.php'; ?>