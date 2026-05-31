<?php
session_start();
include '../databaseconnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);

// fetch tenant
$result = mysqli_query($conn, "SELECT * FROM users WHERE user_id = $id");
$tenant = mysqli_fetch_assoc($result);

if (!$tenant) {
    die("Tenant not found.");
}

/* =========================
   AJAX duplicate email check
   ========================= */
if (isset($_POST['action']) && $_POST['action'] === 'check_email') {

    $email = trim($_POST['email']);
    $current_id = intval($_POST['current_id']);

    $stmt = $conn->prepare("
        SELECT user_id 
        FROM users 
        WHERE email = ? AND user_id != ?
    ");
    $stmt->bind_param("si", $email, $current_id);
    $stmt->execute();
    $stmt->store_result();

    echo json_encode([
        "exists" => $stmt->num_rows > 0
    ]);

    exit();
}

/* =========================
   UPDATE TENANT
   ========================= */
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['action'])) {

    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone_number'];

    $imagePath = $tenant['pictures'];

    // duplicate email check
    $check = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
    $check->bind_param("si", $email, $id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $errorMessage = "This email already exists.";
    } else {

        // IMAGE UPLOAD (same style as property)
        if (!empty($_FILES['Image']['name'])) {

            $folder = "../assets/img/";
            $fileName = time() . "_" . basename($_FILES["Image"]["name"]);
            $targetFile = $folder . $fileName;

            if (move_uploaded_file($_FILES["Image"]["tmp_name"], $targetFile)) {
                $imagePath = "assets/img/" . $fileName;
            }
        }

        $stmt = $conn->prepare("
            UPDATE users
            SET full_name=?, email=?, phone_number=?, pictures=?
            WHERE user_id=?
        ");

        $stmt->bind_param(
            "ssssi",
            $name,
            $email,
            $phone,
            $imagePath,
            $id
        );

        if ($stmt->execute()) {
            header("Location: tenantmanagement.php");
            exit();
        } else {
            $errorMessage = "Update failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Update Tenant</title>

<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<link rel="stylesheet" href="../assets/css/kaiadmin.min.css">

<style>
.is-invalid { border: 2px solid #dc3545 !important; }
.error-text { color: #dc3545; font-size: 12px; margin-top: 3px; }
.preview-image { max-width: 250px; border-radius: 10px; }
</style>
</head>

<body>

<div class="container mt-4">

<div class="card">

<!-- HEADER -->
<div class="card-header bg-primary text-white">
    <h4 class="mb-0">Update Tenant</h4>
</div>

<div class="card-body">

<?php if (!empty($errorMessage)) { ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Error!</strong> <?= htmlspecialchars($errorMessage) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php } ?>

<form id="tenantForm" method="POST" enctype="multipart/form-data">

<input type="hidden" id="currentId" value="<?= $id ?>">

<!-- NAME -->
<div class="mb-3">
    <label class="form-label">Full Name</label>
    <input type="text" id="name" name="full_name"
           class="form-control"
           value="<?= htmlspecialchars($tenant['full_name']) ?>">
    <div id="nameError" class="error-text"></div>
</div>

<!-- EMAIL -->
<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" id="email" name="email"
           class="form-control"
           value="<?= htmlspecialchars($tenant['email']) ?>">
    <div id="emailError" class="error-text"></div>
</div>

<!-- PHONE -->
<div class="mb-3">
    <label class="form-label">Phone Number</label>
    <input type="text" id="phone" name="phone_number"
           class="form-control"
           value="<?= htmlspecialchars($tenant['phone_number']) ?>">
    <div id="phoneError" class="error-text"></div>
</div>

<!-- CURRENT IMAGE -->
<div class="mb-3">
    <label class="form-label">Current Image</label><br>
    <?php if (!empty($tenant['pictures'])) { ?>
        <img src="../<?= htmlspecialchars($tenant['pictures']) ?>" class="preview-image">
    <?php } ?>
</div>

<!-- NEW IMAGE -->
<div class="mb-3">
    <label class="form-label">Replace Image (Optional)</label>
    <input type="file" name="Image" id="image" class="form-control">
    <div id="imageError" class="error-text"></div>
</div>

<!-- BUTTONS -->
<div class="d-flex justify-content-between">
    <a href="tenantmanagement.php" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-success">Update Tenant</button>
</div>
</form>

</div>
</div>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
<!--<script>
document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("tenantForm");

    const name = document.getElementById("name");
    const email = document.getElementById("email");
    const phone = document.getElementById("phone");
    const currentId = document.getElementById("currentId").value;

    let emailValid = true;
    let timeout;

    // =========================
    // AJAX EMAIL CHECK
    // =========================
    email.addEventListener("input", function () {

        clearTimeout(timeout);

        timeout = setTimeout(() => {

            fetch("updatetenant.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: `action=check_email&email=${encodeURIComponent(email.value)}&current_id=${currentId}`
            })
            .then(res => res.json())
            .then(data => {

                if (data.exists) {
                    emailValid = false;
                    email.classList.add("is-invalid");
                    document.getElementById("emailError").textContent = "Email already exists.";
                } else {
                    emailValid = true;
                    email.classList.remove("is-invalid");
                    document.getElementById("emailError").textContent = "";
                }
            });

        }, 300);
    });

    // =========================
    // FORM VALIDATION
    // =========================
    form.addEventListener("submit", function (e) {

        let valid = true;

        document.querySelectorAll(".error-text").forEach(el => el.textContent = "");

        // NAME (letters only)
        if(!/^[A-Za-z\s]+$/.test(name.value.trim())){
            name.classList.add("is-invalid");
            document.getElementById("nameError").textContent = "Only letters and spaces are allowed.";
            valid = false;
        }

        form.addEventListener("submit", function (e) {

        let valid = true;

        document.querySelectorAll(".error-text").forEach(el => el.textContent = "");

        // NAME (letters only)
        if(!/^[A-Za-z\s]+$/.test(name.value.trim())){
            name.classList.add("is-invalid");
            document.getElementById("nameError").textContent = "Only letters and spaces are allowed.";
            valid = false;
        }

        // PHONE NUMBER (11-12 digits)
        const phonePattern = /^[0-9]{10,12}$/; // accepts 10 to 12 digits
        if(!phonePattern.test(phone.value.trim())){
            valid = false;
            phone.classList.add("is-invalid");
            document.getElementById("phoneError").textContent = "Phone number must be 10-12 digits only.";
        }

        if (!emailValid) {
            valid = false;
            email.classList.add("is-invalid");
            document.getElementById("emailError").textContent = "Email already exists.";
        }

        if (!valid) {
            e.preventDefault();
        }
    });
});
});
</script>-->
<script>
document.getElementById("tenantForm").addEventListener("submit", function(e){

    let valid = true;

    const name = document.getElementById("name");
    const phone = document.getElementById("phone");
    const email = document.getElementById("email");

    document.querySelectorAll(".error-text").forEach(el => el.textContent = "");

    // NAME
    if(!/^[A-Za-z\s]+$/.test(name.value.trim())){
        name.classList.add("is-invalid");
        document.getElementById("nameError").textContent = "Only letters and spaces are allowed.";
        valid = false;
    }

    // PHONE (10–12 digits)
    const phonePattern = /^[0-9]{10,12}$/;
    if(!phonePattern.test(phone.value.trim())){
        phone.classList.add("is-invalid");
        document.getElementById("phoneError").textContent = "Phone number must be 10–12 digits.";
        valid = false;
    }

    // EMAIL duplicate check (only if you have emailValid from AJAX)
    if (typeof emailValid !== "undefined" && !emailValid) {
        email.classList.add("is-invalid");
        document.getElementById("emailError").textContent = "Email already exists.";
        valid = false;
    }

    if (!valid) {
        e.preventDefault();
    }
});
</script>
</body>
</html>