<?php
session_start();
// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
include '..\databaseconnection.php';

// Fetch logged-in user details
$userId = $_SESSION['user_id'];
$email = $_SESSION['email'];
$userName = $_SESSION['user_name'];

// Mark all admin notifications as read when page is opened
$updateRead = mysqli_query(
    $conn,
    "UPDATE users SET is_read = 1 WHERE role = 'admin' AND is_read = 0"
);

// Total Tenants
$tenantQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM users WHERE role='tenant'"
);
$totalTenants = mysqli_fetch_assoc($tenantQuery)['total'];

//Display for tenant list
$tenantListQuery = mysqli_query(
    $conn,
    "SELECT full_name, email, pictures, status
     FROM users
     WHERE role='tenant'
     ORDER BY user_id ASC
     LIMIT 5"
);

//For new tenants notification badge
$newTenantQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total 
     FROM users 
     WHERE role='tenant' AND is_read = 0"
);
$newTenants = mysqli_fetch_assoc($newTenantQuery)['total']; 

//Display for admin
$adminListQuery = mysqli_query(
    $conn,
    "SELECT full_name, email, pictures, status
     FROM users
     WHERE role='admin'
"
);

$adminQuery = mysqli_query(
    $conn,
    "SELECT full_name, email, pictures, status
     FROM users
     WHERE user_id = $userId"
);
$admin = mysqli_fetch_assoc($adminQuery);

//For new admin notification badge
$newAdminQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total 
     FROM users 
     WHERE role='admin' AND is_read = 0"
);
$newAdmins = mysqli_fetch_assoc($newAdminQuery)['total'];

// Total Properties
$propertyQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM properties"
);
$totalProperties = mysqli_fetch_assoc($propertyQuery)['total'];

// New Payments
$paymentQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM payments WHERE payment_status = 'pending'"
);
$newPayments = mysqli_fetch_assoc($paymentQuery)['total'];


// Total Maintenance Requests
$maintenanceQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM maintenance_requests"
);
$totalMaintenance = mysqli_fetch_assoc($maintenanceQuery)['total'];

//For new maintenance request notification badge
$newMaintenanceQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total 
     FROM maintenance_requests 
     WHERE request_status = 'pending'"
);
$newMaintenance = mysqli_fetch_assoc($newMaintenanceQuery)['total'];

/* RENTAL INCOME */
$income = mysqli_query($conn, "
    SELECT DATE_FORMAT(payment_date, '%Y-%m') AS month,
           SUM(payment_amount) AS total
    FROM payments
    WHERE payment_status='paid'
    GROUP BY month
");

/* OCCUPANCY */
$occupancy = mysqli_query($conn, "
    SELECT occupancy_status, COUNT(*) AS total
    FROM properties
    GROUP BY occupancy_status
");

/* MAINTENANCE */
$maintenance = mysqli_query($conn, "
    SELECT request_status, COUNT(*) AS total
    FROM maintenance_requests
    GROUP BY request_status
");
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Reports</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link
      rel="icon"
      href="../assets/img/kaiadmin/favicon.ico"
      type="image/x-icon"
    />

    <!-- Fonts and icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["../assets/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/plugins.min.css" />
    <link rel="stylesheet" href="../assets/css/kaiadmin.min.css" />

    <style>
    /*body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 40px; color: #333; }*/
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
    .header img { height: 60px; }
    .header h1 { font-size: 28px; margin: 0; color: #2c3e50; }
    h2.section { background-color: #2c3e50; color: #fff; padding: 10px 15px; margin-top: 40px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
    th { background-color: #34495e; color: #fff; }
    tr:nth-child(even) { background-color: #f7f7f7; }
    tfoot { font-weight: bold; background-color: #ecf0f1; }
    button { padding: 10px 25px; margin-bottom: 20px; background-color: #2c3e50; color: #fff; border: none; cursor: pointer; border-radius: 4px; }
    button:hover { background-color: #34495e; }
    @media print {
    button { display: none; }
    body { margin: 0; }
    h2.section { page-break-before: always; }
    }
    </style>

  </head>
  <body>
    <div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="../admindashboard.php" class="logo">
              <img
                src="../assets/img/kaiadmin/logo_light.svg"
                alt="navbar brand"
                class="navbar-brand"
                height="20"
              />
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>
          <!-- End Logo Header -->
        </div>

        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">
              <li class="nav-item">
                <a href="..\admindashboard.php" class="collapsed" aria-expanded="false">
                  <i class="fas fa-home"></i>
                  <p>Home</p>
                </a>
                <div class="collapse" id="dashboard">
                </div>
              </li>
              <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-ellipsis-h"></i>
                </span>
                <h4 class="text-section">Components</h4>
              </li>    

              <li class="nav-item">
                <a href="propertymanagement.php">
                   <i class="fas fa-building"></i>
                  <p>Property</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="tenantmanagement.php">
                   <i class="fas fa-user-friends"></i>
                  <p>Tenants</p>
                  <span class="badge badge-success"><?php echo $newTenants; ?></span>
                </a>
              </li>

              <li class="nav-item">
                <a href="adminmanagement.php">
                   <i class="fas fa-user-shield"></i>
                  <p>Admin</p>
                  <span class="badge badge-success"><?php echo $newAdmins; ?></span>
                </a>
              </li>

              <li class="nav-item">
                <a href="lease.php">
                  <i class="fas fa-chalkboard-teacher"></i>
                  <p>Lease</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="payments.php">
                  <i class="fas fa-wallet"></i>
                  <p>Payments</p>
                  <span class="badge badge-success"><?php echo $newPayments; ?></span>
                </a>
              </li>

              <li class="nav-item">
                <a href="maintenancerequest.php">
                   <i class="fas fa-wrench"></i>
                  <p>Maintenance</p>
                  <span class="badge badge-success"><?php echo $newMaintenance; ?></span>
                </a>
              </li>
             
              <li class="nav-item active">
                <a href="reports.php">
                  <i class="fas fa-file"></i>
                  <p>Report</p>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->

      <!-- Sidebar Toggle Button -->
      <div class="main-panel">
        <div class="main-header">
          <div class="main-header-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
              <a href="index.html" class="logo">
                <img src="assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20"/>
              </a>
              <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                  <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                  <i class="gg-menu-left"></i>
                </button>
              </div>
              <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
              </button>
            </div>
            <!-- End Logo Header -->
          </div>

          <!-- Top Navigation Bar -->
          <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
            <div class="container-fluid">
              <nav
                class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
              </nav>

              <!-- For Top Bar Logout Section -->
              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                
                <li class="nav-item topbar-user dropdown hidden-caret">
                  <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                    <div class="avatar-sm">
                      <img
                        src="../<?php echo htmlspecialchars($admin['pictures']); ?>"
                        alt="..."
                        class="avatar-img rounded-circle"
                      />
                    </div>
                    <span class="profile-username">
                      <span class="op-7">Hi,</span>
                      <span class="fw-bold"><?php echo $admin['full_name']; ?></span>
                    </span>
                  </a>
                  <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                      <li>
                        <div class="user-box">
                          <div class="avatar-lg">
                            <img src="../<?php echo htmlspecialchars($admin['pictures']); ?>" alt="profile image" class="avatar-img rounded"/>
                          </div>

                          <div class="u-text">
                          <h4><?php echo htmlspecialchars($admin['full_name']); ?></h4>
                          <p class="text-muted"><?php echo htmlspecialchars($admin['email']); ?></p>
                          <a href="profile.php" class="btn btn-xs btn-secondary btn-sm">View Profile</a>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/TSE6223-Project-1/logout.php">Logout</a>
                      </li>
                    </div>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>
          <!-- End Navbar -->
        </div>

        <div class="container">
          <div class="page-inner">
            <div class="page-header">
              <h3 class="fw-bold mb-3">Reports</h3>
              <ul class="breadcrumbs mb-3">
              </ul>
            </div>

            <!-- Table for Admin Management -->
            <div class="row">
              
            <div class="col-md-12">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title"></div>
                  </div>
                  <div class="card-body">
                    <!-- Modal -->
                    <div
                      class="modal fade"
                      id="addRowModal"
                      tabindex="-1"
                      role="dialog"
                      aria-hidden="true"
                    >
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header border-0">
                          </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="table-responsive">
                    <div class="header">
                    <img src="../assets/img/kaiadmin/logo_light.svg" alt="Logo">
                    <h1>Property Rental System Report</h1>
                </div>

                <button onclick="window.print()">Print / Save as PDF</button>

                <h2 class="section">Rental Income</h2>
                <table>
                <tr><th>Month</th><th>Total Income (RM)</th></tr>
                <?php $income_total = 0; ?>
                <?php while($row = mysqli_fetch_assoc($income)) { 
                    $income_total += $row['total'];
                ?>
                <tr>
                <td><?= $row['month'] ?></td>
                <td><?= number_format($row['total'],2) ?></td>
                </tr>
                <?php } ?>
                <tfoot>
                <tr><td>Total</td><td><?= number_format($income_total,2) ?></td></tr>
                </tfoot>
                </table>

                <h2 class="section">Occupancy Status</h2>
                <table>
                <tr><th>Status</th><th>Total Properties</th></tr>
                <?php while($row = mysqli_fetch_assoc($occupancy)) { ?>
                <tr>
                <td><?= $row['occupancy_status'] ?></td>
                <td><?= $row['total'] ?></td>
                </tr>
                <?php } ?>
                </table>

                <h2 class="section">Maintenance Requests</h2>
                <table>
                <tr><th>Status</th><th>Total Requests</th></tr>
                <?php while($row = mysqli_fetch_assoc($maintenance)) { ?>
                <tr>
                <td><?= ucfirst($row['request_status']) ?></td>
                <td><?= $row['total'] ?></td>
                </tr>
                <?php } ?>
                </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--   Core JS Files   -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script>
      $(document).ready(function () {
        $("#basic-datatables").DataTable({});

        $("#multi-filter-select").DataTable({
          pageLength: 5,
          initComplete: function () {
            this.api()
              .columns()
              .every(function () {
                var column = this;
                var select = $(
                  '<select class="form-select"><option value=""></option></select>'
                )
                  .appendTo($(column.footer()).empty())
                  .on("change", function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                    column
                      .search(val ? "^" + val + "$" : "", true, false)
                      .draw();
                  });

                column
                  .data()
                  .unique()
                  .sort()
                  .each(function (d, j) {
                    select.append(
                      '<option value="' + d + '">' + d + "</option>"
                    );
                  });
              });
          },
        });

        // Add Row
        $("#add-row").DataTable({
          pageLength: 5,
        });
        var action =
          '<td> <div class="form-button-action"> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';
      });
    </script>

    <script>
  document.getElementById("addPropertyForm").addEventListener("submit", function(e){

    let valid = true;

    document.querySelectorAll(".error-text").forEach(error => {
    error.textContent = "";
    });

    document.querySelectorAll(".form-control").forEach(field => {
    field.classList.remove("is-invalid");
    });

    const name = document.getElementById("propertyName");
    const address = document.getElementById("propertyAddress");
    const price = document.getElementById("propertyPrice");
    const rooms = document.getElementById("propertyRooms");
    const description = document.getElementById("propertyDescription");
    const image = document.getElementById("propertyImage");

    // Reset borders
    document.querySelectorAll(".form-control").forEach(field => {
        field.addEventListener("input", function(){
        this.classList.remove("is-invalid");
    });

    field.addEventListener("change", function(){
        this.classList.remove("is-invalid");
    });
});

    // Property Name
    if(name.value.trim() === ""){
    name.classList.add("is-invalid");
    document.getElementById("nameError").textContent =
        "Property name is required.";
    valid = false;
    }
    else if(!/^[A-Za-z\s]+$/.test(name.value.trim())){
    name.classList.add("is-invalid");
    document.getElementById("nameError").textContent = "Only letters and spaces are allowed.";
    valid = false;
    }

    // Address
    if(address.value.trim().length < 5){
        address.classList.add("is-invalid");
        document.getElementById("addressError").textContent =
            "Address must be at least 5 characters long.";
        valid = false;
    }

    // Price (float > 0)
    if(price.value.trim() === ""){
    price.classList.add("is-invalid");
    document.getElementById("priceError").textContent =
        "Rental price is required.";
    valid = false;
    }
    else if(isNaN(price.value) || parseFloat(price.value) <= 0){
    price.classList.add("is-invalid");
    document.getElementById("priceError").textContent =
        "Price must be greater than 0.";
    valid = false;
    }

    // Number of Rooms (integer > 0)
    if(rooms.value.trim() === ""){
    rooms.classList.add("is-invalid");
    document.getElementById("roomsError").textContent =
        "Number of rooms is required.";
    valid = false;
    }
    else if(!Number.isInteger(Number(rooms.value)) || Number(rooms.value) <= 0){
    rooms.classList.add("is-invalid");
    document.getElementById("roomsError").textContent =
        "Rooms must be a positive whole number.";
    valid = false;
    }

    // Description
    if(description.value.trim().length < 10){
        description.classList.add("is-invalid");
        document.getElementById("descriptionError").textContent =
            "Description must be at least 10 characters long.";
        valid = false;
    }

    // Image Required + Format Validation
    if(image.files.length === 0){
    image.classList.add("is-invalid");
    document.getElementById("imageError").textContent =
        "Please upload a property image.";
    valid = false;
    }
    else {
    const allowedExtensions = ["jpg","jpeg","png","gif","webp"];

    const fileName = image.files[0].name.toLowerCase();
    const extension = fileName.split(".").pop();

    if(!allowedExtensions.includes(extension)){
        image.classList.add("is-invalid");
        document.getElementById("imageError").textContent =
            "Only JPG, JPEG, PNG, GIF and WEBP files are allowed.";
        valid = false;
    }
   }

    if(!valid){
        e.preventDefault();
    }
  });

  // Remove error when corrected
document.querySelectorAll(".form-control").forEach(field => {
    field.addEventListener("input", () => {
        field.classList.remove("is-invalid");
        const errorId = field.id.replace("property","").toLowerCase() + "Error";
        const errorElement = document.getElementById(errorId);
        if(errorElement) errorElement.textContent = "";
    });

    field.addEventListener("change", () => {
        field.classList.remove("is-invalid");
        const errorId = field.id.replace("property","").toLowerCase() + "Error";
        const errorElement = document.getElementById(errorId);
        if(errorElement) errorElement.textContent = "";
    });
});
  </script>
  </body>
</html>
