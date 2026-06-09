<?php include 'includes/tenant-header.php'; ?>

<?php
// Get tenant's properties
$propertyQuery = mysqli_query($conn, "
    SELECT p.property_id, p.property_name, p.address
    FROM properties p
    JOIN leases l ON p.property_id = l.property_id
    WHERE l.tenant_id = $userId AND l.lease_status = 'active'
");

$message = '';
$messageType = '';

// Handle maintenance request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_request'])) {
    $propertyId = mysqli_real_escape_string($conn, $_POST['property_id']);
    $issueTitle = mysqli_real_escape_string($conn, $_POST['issue_title']);
    $issueDescription = mysqli_real_escape_string($conn, $_POST['issue_description']);
    $priorityLevel = mysqli_real_escape_string($conn, $_POST['priority_level']);
    
    $issueImage = null;
    if (isset($_FILES['issue_image']) && $_FILES['issue_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['issue_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $issueImage = 'uploads/maintenance/' . time() . '_' . basename($filename);
            $uploadPath = '../Admin/' . $issueImage;
            
            if (!file_exists('../Admin/uploads/maintenance')) {
                mkdir('../Admin/uploads/maintenance', 0777, true);
            }
            
            move_uploaded_file($_FILES['issue_image']['tmp_name'], $uploadPath);
        }
    }
    
    $insertQuery = "INSERT INTO maintenance_requests (tenant_id, property_id, issue_title, issue_description, issue_image, priority_level, request_status) 
                    VALUES ($userId, $propertyId, '$issueTitle', '$issueDescription', '$issueImage', '$priorityLevel', 'pending')";
    
    if (mysqli_query($conn, $insertQuery)) {
        $message = "Maintenance request submitted successfully! Admin will review your request.";
        $messageType = "success";
    } else {
        $message = "Error submitting request: " . mysqli_error($conn);
        $messageType = "danger";
    }
}
?>

<div class="container-fluid">
    <h2 class="mb-4">Submit Maintenance Request</h2>
    
    <?php if($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-tools"></i> Request Form</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Select Property <span class="text-danger">*</span></label>
                            <select name="property_id" class="form-select" required>
                                <option value="">Choose your rented property</option>
                                <?php while($prop = mysqli_fetch_assoc($propertyQuery)): ?>
                                    <option value="<?php echo $prop['property_id']; ?>">
                                        <?php echo htmlspecialchars($prop['property_name']); ?> - <?php echo htmlspecialchars($prop['address']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Issue Title <span class="text-danger">*</span></label>
                            <input type="text" name="issue_title" class="form-control" placeholder="e.g., Air conditioner not working, Water leakage" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Detailed Description <span class="text-danger">*</span></label>
                            <textarea name="issue_description" class="form-control" rows="4" placeholder="Please describe the issue in detail..." required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Priority Level <span class="text-danger">*</span></label>
                            <select name="priority_level" class="form-select" required>
                                <option value="low">Low - Can wait, not urgent</option>
                                <option value="medium">Medium - Needs attention soon</option>
                                <option value="high">High - Urgent, needs immediate attention</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Upload Photo (Optional)</label>
                            <input type="file" name="issue_image" class="form-control" accept=".jpg,.jpeg,.png">
                            <small class="text-muted">Upload a photo to help us understand the issue better.</small>
                        </div>
                        
                        <button type="submit" name="submit_request" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Submit Request
                        </button>
                        <a href="maintenance-status.php" class="btn btn-secondary">View My Requests</a>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Guidelines</h5>
                </div>
                <div class="card-body">
                    <ul>
                        <li>Please provide clear description of the issue.</li>
                        <li>Uploading a photo helps us respond faster.</li>
                        <li>Emergency issues: Call us directly at +60-125845236.</li>
                        <li>You can track your request status in "Request Status" page.</li>
                        <li>We aim to respond within 24-48 hours.</li>
                    </ul>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> Response Time</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="text-primary">24-48</h2>
                        <p>Hours average response time</p>
                        <hr>
                        <p><i class="fas fa-phone"></i> Emergency: <strong>+60-1155485623</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/tenant-footer.php'; ?>