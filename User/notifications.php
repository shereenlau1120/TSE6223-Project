<?php include 'includes/tenant-header.php'; ?>

<?php
// Mark all as read
if (isset($_GET['mark_read'])) {
    mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE user_id = $userId");
    header("Location: notifications.php");
    exit();
}

// Mark single as read
if (isset($_GET['read_id'])) {
    $readId = intval($_GET['read_id']);
    mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE notification_id = $readId AND user_id = $userId");
    header("Location: notifications.php");
    exit();
}

$notifQuery = mysqli_query($conn, "
    SELECT * FROM notifications 
    WHERE user_id = $userId 
    ORDER BY created_at DESC
");
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-bell"></i> Notifications</h2>
        <a href="?mark_read=1" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-check-double"></i> Mark All as Read
        </a>
    </div>
    
    <div class="card">
        <div class="card-body p-0">
            <?php if(mysqli_num_rows($notifQuery) > 0): ?>
                <?php while($notif = mysqli_fetch_assoc($notifQuery)): ?>
                    <div class="notification-item p-3 border-bottom <?php echo $notif['is_read'] ? '' : 'bg-light'; ?>">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">
                                    <?php if(!$notif['is_read']): ?>
                                        <span class="badge bg-primary me-2">New</span>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($notif['notification_title']); ?>
                                </h6>
                                <p class="mb-1"><?php echo htmlspecialchars($notif['notification_message']); ?></p>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> <?php echo date('d M Y, h:i A', strtotime($notif['created_at'])); ?>
                                </small>
                            </div>
                            <?php if(!$notif['is_read']): ?>
                                <a href="?read_id=<?php echo $notif['notification_id']; ?>" class="btn btn-sm btn-link">
                                    Mark as read
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No notifications yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.notification-item:hover {
    background-color: #f8f9fa;
}
</style>

<?php include 'includes/tenant-footer.php'; ?>