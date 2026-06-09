<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'tenant') {
    header("Location: ../login.php");
    exit();
}

include '../databaseconnection.php';

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'];

// Get unread notification count
$notifQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM notifications WHERE user_id = $userId AND is_read = 0");
$unreadCount = mysqli_fetch_assoc($notifQuery)['total'];

// Get user profile picture
$userQuery = mysqli_query($conn, "SELECT pictures FROM users WHERE user_id = $userId");
$user = mysqli_fetch_assoc($userQuery);
$profilePic = $user['pictures'] ?? 'assets/img/default-avatar.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Dashboard - Property Rental Management System</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/tenant-style.css">
    
    <!-- Custom JS -->
    <script src="assets/js/tenant-script.js"></script>
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f4f6f9;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 280px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
        }
        
        .sidebar.collapsed {
            width: 80px;
        }
        
        .sidebar .logo {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar .logo h3 {
            margin: 0;
            font-size: 1.3rem;
        }
        
        .sidebar .nav-links {
            list-style: none;
            padding: 20px 0;
            margin: 0;
        }
        
        .sidebar .nav-links li {
            margin: 5px 0;
        }
        
        .sidebar .nav-links a {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s;
            gap: 15px;
        }
        
        .sidebar .nav-links a:hover,
        .sidebar .nav-links li.active a {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        
        .sidebar .nav-links i {
            width: 25px;
            font-size: 1.2rem;
        }
        
        .sidebar .nav-links span {
            flex: 1;
        }
        
        .sidebar.collapsed .nav-links span,
        .sidebar.collapsed .logo h3 span {
            display: none;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            transition: all 0.3s ease;
        }
        
        .main-content.expanded {
            margin-left: 80px;
        }
        
        /* Top Navbar */
        .top-navbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .toggle-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #333;
        }
        
        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-dropdown .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .notification-badge {
            position: relative;
        }
        
        .notification-badge .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            padding: 3px 6px;
            font-size: 10px;
        }
        
        .content-wrapper {
            padding: 30px;
        }
        
        /* Cards */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<div class="sidebar" id="sidebar">
    <div class="logo">
        <h3><i class="fas fa-home"></i> <span>PRMS</span></h3>
    </div>
    <ul class="nav-links">
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'available-properties.php' ? 'active' : ''; ?>">
            <a href="available-properties.php"><i class="fas fa-building"></i><span>Browse Properties</span></a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'my-requests.php' ? 'active' : ''; ?>">
            <a href="my-requests.php"><i class="fas fa-clock"></i><span>My Requests</span></a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'my-rental.php' ? 'active' : ''; ?>">
            <a href="my-rental.php"><i class="fas fa-file-signature"></i><span>My Rental</span></a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'payment-history.php' ? 'active' : ''; ?>">
            <a href="payment-history.php"><i class="fas fa-credit-card"></i><span>Payment History</span></a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'submit-payment.php' ? 'active' : ''; ?>">
            <a href="submit-payment.php"><i class="fas fa-upload"></i><span>Submit Payment</span></a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'maintenance-request.php' ? 'active' : ''; ?>">
            <a href="maintenance-request.php"><i class="fas fa-tools"></i><span>Maintenance</span></a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'maintenance-status.php' ? 'active' : ''; ?>">
            <a href="maintenance-status.php"><i class="fas fa-clipboard-list"></i><span>Request Status</span></a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
            <a href="profile.php"><i class="fas fa-user"></i><span>My Profile</span></a>
        </li>
        <li>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </li>
    </ul>
</div>

<div class="main-content" id="mainContent">
    <div class="top-navbar">
        <button class="toggle-btn" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="user-dropdown">
            <a href="notifications.php" class="notification-badge" style="color: #333;">
                <i class="fas fa-bell fa-lg"></i>
                <?php if($unreadCount > 0): ?>
                    <span class="badge"><?php echo $unreadCount; ?></span>
                <?php endif; ?>
            </a>
            <a href="profile.php" style="text-decoration: none; color: #333; display: flex; align-items: center; gap: 10px;">
                <img src="../Admin/<?php echo htmlspecialchars($profilePic); ?>" class="avatar" alt="Profile">
                <span><?php echo htmlspecialchars($userName); ?></span>
            </a>
        </div>
    </div>
    
    <div class="content-wrapper">