<?php
include '../databaseconnection.php';

$id = intval($_GET['id']);

$result = mysqli_query($conn, "
    SELECT m.*, u.full_name, p.property_name
    FROM maintenance_requests m
    JOIN users u ON m.tenant_id = u.user_id
    JOIN properties p ON m.property_id = p.property_id
    WHERE m.request_id = $id
");

$row = mysqli_fetch_assoc($result);

if(!$row){
    die("Request not found");
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<title>Maintenance Detail</title>
</head>

<body class="container mt-4">

<div class="card">

<div class="card-header bg-primary text-white">
    Maintenance Request Detail
</div>

<div class="card-body">

<p><b>Tenant:</b> <?= $row['full_name'] ?></p>
<p><b>Property:</b> <?= $row['property_name'] ?></p>
<p><b>Issue:</b> <?= $row['issue_title'] ?></p>
<p><b>Description:</b> <?= $row['issue_description'] ?></p>

<p><b>Priority:</b>
<span class="badge bg-warning"><?= $row['priority_level'] ?></span>
</p>

<p><b>Status:</b>
<span class="badge bg-info"><?= $row['request_status'] ?></span>
</p>

<?php if(!empty($row['issue_image'])) { ?>
<img src="../<?= $row['issue_image'] ?>" width="300" class="img-thumbnail">
<?php } ?>

<hr>

<!-- UPDATE FORM -->
<form method="POST" action="updatemaintenance.php">

<input type="hidden" name="id" value="<?= $row['request_id'] ?>">

<label>Status</label>
<select name="status" class="form-control" required>
    <option value="pending">Pending</option>
    <option value="in_progress">In Progress</option>
    <option value="completed">Completed</option>
</select>

<br>

<label>Admin Remark</label>
<textarea name="remark" class="form-control"></textarea>

<br>

<button class="btn btn-success">Update</button>
<a href="maintenancerequest.php" class="btn btn-secondary">Back</a>

</form>

</div>
</div>

</body>
</html>