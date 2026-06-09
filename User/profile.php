<?php include 'includes/tenant-header.php'; ?>

<?php
// Get user data
$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $userId");
$user = mysqli_fetch_assoc($userQuery);

$message = '';
$messageType = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullName = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
    
    // Handle image upload
    $profileImage = $user['pictures'];
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $profileImage = 'assets/img/profile/' . time() . '_' . basename($_FILES['profile_image']['name']);
            $uploadPath = '../Admin/' . $profileImage;
            
            if (!file_exists('../Admin/assets/img/profile')) {
                mkdir('../Admin/assets/img/profile', 0777, true);
            }
            
            move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadPath);
        }
    }
    
    $updateQuery = "UPDATE users SET full_name='$fullName', phone_number='$phone', pictures='$profileImage' WHERE user_id=$userId";
    
    if (mysqli_query($conn, $updateQuery)) {
        $_SESSION['user_name'] = $fullName;
        $message = "Profile updated successfully!";
        $messageType = "success";
        // Refresh user data
        $userQuery = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $userId");
        $user = mysqli_fetch_assoc($userQuery);
    } else {
        $message = "Error updating profile: " . mysqli_error($conn);
        $messageType = "danger";
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if (password_verify($currentPassword, $user['password'])) {
        if ($newPassword === $confirmPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE users SET password='$hashedPassword' WHERE user_id=$userId";
            
            if (mysqli_query($conn, $updateQuery)) {
                $message = "Password changed successfully!";
                $messageType = "success";
            } else {
                $message = "Error changing password.";
                $messageType = "danger";
            }
        } else {
            $message = "New passwords do not match!";
            $messageType = "danger";
        }
    } else {
        $message = "Current password is incorrect!";
        $messageType = "danger";
    }
}
?>

<div class="container-fluid">
    <h2 class="mb-4">My Profile</h2>
    
    <?php if($message): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <img src="../Admin/<?php echo htmlspecialchars($user['pictures']); ?>" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4><?php echo htmlspecialchars($user['full_name']); ?></h4>
                    <p class="text-muted"><?php echo ucfirst($user['role']); ?></p>
                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($user['phone_number']); ?></p>
                    <p><i class="fas fa-calendar"></i> Member since: <?php echo date('d M Y', strtotime($user['created_at'])); ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            <small class="text-muted">Email cannot be changed. Contact admin for assistance.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Profile Picture</label>
                            <input type="file" name="profile_image" class="form-control" accept=".jpg,.jpeg,.png">
                        </div>
                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-key"></i> Change Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                            <small class="text-muted">Must be at least 8 characters.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-warning">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/tenant-footer.php'; ?>