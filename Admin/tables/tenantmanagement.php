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
    <title>Tenant Management</title>
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

              <li class="nav-item active">
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
                <a data-bs-toggle="collapse" href="#leases">
                  <i class="fas fa-chalkboard-teacher"></i>
                  <p>Lease</p>
                  <span class="caret"></span>
                </a>
                <div class="collapse" id="leases">
                  <ul class="nav nav-collapse">
                    <li>
                      <a href="leasemanagement.php">
                        <span class="sub-item">Lease Management</span>
                      </a>
                    </li>
                    <li>
                      <a href="leasepayment.php">
                        <span class="sub-item">Lease Payments</span>
                      </a>
                    </li>
                  </ul>
                </div>
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
                        <a class="dropdown-item" href="../logout.php">Logout</a>
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
              <h3 class="fw-bold mb-3">Tenant Management Table</h3>
              <ul class="breadcrumbs mb-3">
              </ul>
            </div>

            <!-- Table for Property Management -->
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <div class="d-flex align-items-center">
                      <h4 class="card-title">Add Row</h4>
                      <button
                        class="btn btn-primary btn-round ms-auto"
                        data-bs-toggle="modal"
                        data-bs-target="#addRowModal"
                      >
                        <i class="fa fa-plus"></i>
                        Add Row
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
                              <span class="fw-light"> Row </span>
                            </h5>
                            <button
                              type="button"
                              class="close"
                              data-dismiss="modal"
                              aria-label="Close"
                            >
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <p class="small">
                              Create a new row using this form, make sure you
                              fill them all
                            </p>
                            <form>
                              <div class="row">
                                <div class="col-sm-12">
                                  <div class="form-group form-group-default">
                                    <label>Name</label>
                                    <input
                                      id="addName"
                                      type="text"
                                      class="form-control"
                                      placeholder="fill name"
                                    />
                                  </div>
                                </div>
                                <div class="col-md-6 pe-0">
                                  <div class="form-group form-group-default">
                                    <label>Position</label>
                                    <input
                                      id="addPosition"
                                      type="text"
                                      class="form-control"
                                      placeholder="fill position"
                                    />
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group form-group-default">
                                    <label>Office</label>
                                    <input
                                      id="addOffice"
                                      type="text"
                                      class="form-control"
                                      placeholder="fill office"
                                    />
                                  </div>
                                </div>
                              </div>
                            </form>
                          </div>
                          <div class="modal-footer border-0">
                            <button
                              type="button"
                              id="addRowButton"
                              class="btn btn-primary"
                            >
                              Add
                            </button>
                            <button
                              type="button"
                              class="btn btn-danger"
                              data-dismiss="modal"
                            >
                              Close
                            </button>
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
                            <th>Position</th>
                            <th>Office</th>
                            <th style="width: 10%">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>Tiger Nixon</td>
                            <td>System Architect</td>
                            <td>Edinburgh</td>
                            <td>
                              <div class="form-button-action">
                                <button
                                  type="button"
                                  data-bs-toggle="tooltip"
                                  title=""
                                  class="btn btn-link btn-primary btn-lg"
                                  data-original-title="Edit Task"
                                >
                                  <i class="fa fa-edit"></i>
                                </button>
                                <button
                                  type="button"
                                  data-bs-toggle="tooltip"
                                  title=""
                                  class="btn btn-link btn-danger"
                                  data-original-title="Remove"
                                >
                                  <i class="fa fa-times"></i>
                                </button>
                              </div>
                            </td>
                          </tr>
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

        $("#addRowButton").click(function () {
          $("#add-row")
            .dataTable()
            .fnAddData([
              $("#addName").val(),
              $("#addPosition").val(),
              $("#addOffice").val(),
              action,
            ]);
          $("#addRowModal").modal("hide");
        });
      });
    </script>
  </body>
</html>
