<?php
session_start();
include '../databaseconnection.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$adminId = $_SESSION['user_id'];
$adminQuery = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $adminId");
$admin = mysqli_fetch_assoc($adminQuery);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullName = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    // Handle image upload
    if (!empty($_FILES['profile_image']['name'])) {

    $imgName = time() . '_' . basename($_FILES['profile_image']['name']);

    // NEW PATH (as requested)
    $targetDir = "assets/img/";

    // create folder if not exists
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $targetFile = $targetDir . $imgName;

    move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFile);

    // store relative path in DB
    $profileImagePath = $targetFile;

    } else {
        $profileImagePath = $admin['pictures'];
    }

    // Update admin profile
    $stmt = $conn->prepare("UPDATE users SET full_name=?, phone_number=?, pictures=? WHERE user_id=?");
    $stmt->bind_param("sssi", $fullName, $phone, $profileImagePath, $adminId);
    $stmt->execute();

    header("Location: profile.php?success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css">
</head>
<body>
<div class="wrapper">

    <div class="main-panel">
        <div class="container">
            <div class="page-inner">
                <div class="row mt-4">
                    <div class="col-md-6 offset-md-3">
                        <div class="card card-round">
                            <div class="card-header">
                                <h4 class="card-title">Manage Profile</h4>
                            </div>
                            <div class="card-body">
                                <?php if(isset($_GET['success'])): ?>
                                    <div class="alert alert-success">Profile updated successfully!</div>
                                <?php endif; ?>
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="form-group text-center">
                                        <img src="<?php echo htmlspecialchars($admin['pictures']); ?>" 
                                             class="rounded-circle mb-3" 
                                             alt="Profile Image" width="120" height="120">
                                        <input type="file" name="profile_image" class="form-control mt-2">
                                    </div>
                                    <div class="form-group">
                                        <label>Full Name</label>
                                        <input type="text" name="full_name" class="form-control" 
                                               value="<?php echo htmlspecialchars($admin['full_name']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($admin['phone_number']); ?>" required>
                                    </div>
                                    <div class="d-flex justify-content-between mt-3">

                                    <!-- Back Button (Left Side) -->
                                    <a href="admindashboard.php" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </a>

                                    <!-- Update Button (Right Side) -->
                                    <button type="submit" class="btn btn-primary">
                                        Update Profile
                                    </button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="assets/js/core/jquery-3.7.1.min.js"></script>
<script src="assets/js/core/bootstrap.min.js"></script>
<script src="assets/js/kaiadmin.min.js"></script>
</body>
</html>