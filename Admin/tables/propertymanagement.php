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
    "SELECT COUNT(*) AS total FROM payments WHERE payment_date >= CURDATE() - INTERVAL 7 DAY"
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
     WHERE is_read = 0"
);
$newMaintenance = mysqli_fetch_assoc($newMaintenanceQuery)['total'];
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Property Management</title>
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
    .is-invalid {
      border: 2px solid #dc3545 !important;
    }

    .error-text {
      color: #dc3545;
      font-size: 12px;
      display: block;
      margin-top: 4px;
      min-height: 18px;
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

              <li class="nav-item active">
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
                  <p>Lease and Payments</p>
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
             
              <li class="nav-item">
                <a data-bs-toggle="collapse" href="#report">
                  <i class="fas fa-file"></i>
                  <p>Report</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="report">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="#">
                        <span class="sub-item">Rental Income</span>
                      </a>
                    </li>
                    <li>
                      <a href="#">
                        <span class="sub-item">Occupancy Rate</span>
                      </a>
                    </li>
                    <li>
                      <a href="#">
                        <span class="sub-item">Maintenance Activities</span>
                      </a>
                    </li>
                  </ul>
                </div>
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
              <h3 class="fw-bold mb-3">Property Management Table</h3>
              <ul class="breadcrumbs mb-3">
              </ul>
            </div>


            <?php if (isset($_GET['error']) && $_GET['error'] == 'duplicate_name') { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong>Error!</strong> Property name already exists.
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php } ?>

            <!-- Table for Property Management -->
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <div class="d-flex align-items-center">
                      <h4 class="card-title">Add New Property</h4>
                      <button class="btn btn-primary btn-round ms-auto" data-bs-toggle="modal" data-bs-target="#addRowModal">
                        <i class="fa fa-plus"></i>
                        Add New Property
                      </button>
                    </div>
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
                            <h5 class="modal-title">
                              <span class="fw-mediumbold"> New</span>
                              <span class="fw-light"> Property </span>
                            </h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <form id="addPropertyForm" action="addproperties.php" method="POST" enctype="multipart/form-data">
                          <div class="modal-body">
                            <p class="small"> Create a new property using this form, make sure you fill them all
                            </p>
                            
                              <div class="row">
                                <div class="col-sm-12">
                                  <div class="form-group form-group-default">
                                    <label style="font-weight: bold; color: #333; size: 16px;">Property Name<span class="text-danger"> *</span></label>
                                    <input id="propertyName"name="name" type="text" class="form-control" placeholder="fill name" required/>
                                    <small id="nameError" class="error-text"></small>
                                  </div>
                                </div>
                                <div class="col-md-6 pe-0">
                                  <div class="form-group form-group-default">
                                    <label style="font-weight: bold; color: #333; size: 16px;">Property Type<span class="text-danger"> *</span></label>
                                    <select id="propertyType"name="type" class="form-control" required>
                                    <option value="">Select Property Type</option>
                                    <option value="residential">Residential</option>
                                    <option value="commercial">Commercial</option>
                                    </select>
                                    <small id="typeError" class="error-text"></small>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group form-group-default">
                                    <label style="font-weight: bold; color: #333; size: 16px;">Address<span class="text-danger"> *</span></label>
                                    <input id="propertyAddress"name="address" type="text" class="form-control" placeholder="fill address" required/>
                                    <small id="addressError" class="error-text"></small>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group form-group-default">
                                    <label style="font-weight: bold; color: #333; size: 16px;">Price<span class="text-danger"> *</span></label>
                                    <input id="propertyPrice"name="price" type="number" class="form-control" min="0.01" step="0.01"placeholder="fill price" required/>
                                    <small id="priceError" class="error-text"></small>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group form-group-default">
                                    <label style="font-weight: bold; color: #333; size: 16px;">Number of Rooms<span class="text-danger"> *</span></label>
                                    <input id="propertyRooms"name="rooms" type="number" class="form-control" min="1" step="1" placeholder="fill number of rooms" required/>
                                    <small id="roomsError" class="error-text"></small>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group form-group-default">
                                    <label style="font-weight: bold; color: #333; size: 16px;">Property Description<span class="text-danger"> *</span></label>
                                    <input id="propertyDescription"name="description" type="text" class="form-control" placeholder="fill description" required>
                                    <small id="descriptionError" class="error-text"></small>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group form-group-default">
                                    <label style="font-weight: bold; color: #333; size: 16px;">Occupancy<span class="text-danger"> *</span></label>
                                    <select id="propertyOccupancy" name="occupancy" class="form-control" required>
                                    <option value="">Select Occupancy Status</option>
                                    <option value="available">Available</option>
                                    <option value="rented">Rented</option>
                                    </select>
                                    <small id="occupancyError" class="error-text"></small>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group form-group-default">
                                    <label style="font-weight: bold; color: #333; size: 16px;">Image<span class="text-danger"> *</span></label>
                                    <input id="propertyImage" name="Image" type="file" class="form-control" accept=".jpg,.jpeg,.png,.webp" placeholder="fill image" required>
                                    <small id="imageError" class="error-text"></small>
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer border-0">
                               <button type="submit" name="add_property" class="btn btn-primary">Add</button>
                               <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div class="table-responsive">
                      <table
                        id="add-row"
                        class="display table table-striped table-hover"
                      >
                        <thead>
                          <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Address</th>
                            <th>Rental Price</th>
                            <th>Number of Rooms</th>
                            <th>Activation</th>
                            <th style="width: 10%">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                        $propertyQuery = mysqli_query($conn, "SELECT * FROM properties ORDER BY property_id DESC");

                        while ($property = mysqli_fetch_assoc($propertyQuery)) {
                        ?>
                        <tr>
                          <td><?= htmlspecialchars($property['property_name']); ?></td>
                          <td><?= htmlspecialchars($property['property_type']); ?></td>
                          <td><?= htmlspecialchars($property['address']); ?></td>
                          <td>RM <?= number_format($property['rental_price'], 2); ?></td>
                          <td><?= htmlspecialchars($property['number_of_rooms']); ?></td>

                        <!-- Activation Toggle -->
                        <td>
                          <form action="toggleactivation.php" method="POST">
                          <input type="hidden" name="property_id" value="<?= $property['property_id']; ?>">

                        <?php if ($property['activation'] == 'active') { ?>
                          <button type="submit" class="btn btn-success btn-sm">Active</button>
                        <?php } else { ?>
                          <button type="submit" class="btn btn-secondary btn-sm">Inactive</button>
                        <?php } ?>
                      </form>
                    </td>

                      <!-- Action Icons -->
                      <td>
                        <div class="d-flex justify-content-center gap-3">

                      <!-- Edit -->
                      <a href="updateproperty.php?id=<?= $property['property_id']; ?>" class="text-center text-primary text-decoration-none">
                        <i class="fa fa-edit fa-lg"></i><br>
                        <small class = "text-dark">Edit</small>
                      </a>

                      <!-- View -->
                      <a href="viewproperty.php?id=<?= $property['property_id']; ?>" class="text-center text-primary text-decoration-none">
                        <i class="fa fa-eye fa-lg"></i><br>
                        <small class = "text-dark">View</small>
                      </a>
                  </div>
                </td>
              </tr>
            <?php
            }
          ?>
        </tbody>
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
