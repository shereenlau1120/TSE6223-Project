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

// Total Rental Income (Paid only)
$incomeQuery = mysqli_query(
    $conn,
    "SELECT SUM(payment_amount) AS total
     FROM payments
     WHERE payment_status='paid'"
);

// New Payments
$paymentQuery = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM payments WHERE payment_status = 'pending'"
);
$newPayments = mysqli_fetch_assoc($paymentQuery)['total'];

$totalIncome = mysqli_fetch_assoc($incomeQuery)['total'];

if ($totalIncome == null) {
    $totalIncome = 0;
}

//Monthly Income for Chart
$monthlyIncomeQuery = mysqli_query(
    $conn,
    "SELECT
        DATE_FORMAT(payment_date, '%Y-%m') AS month,
        SUM(payment_amount) AS total_income
     FROM payments
     WHERE payment_status = 'paid'
     GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
     ORDER BY month ASC"
);

$months = [];
$incomeData = [];

while($row = mysqli_fetch_assoc($monthlyIncomeQuery))
{
    $months[] = $row['month'];
    $incomeData[] = $row['total_income'];
}

//For the transaction history section
$transactionQuery = mysqli_query(
    $conn,
    "SELECT payment_id,
            payment_date,
            payment_amount,
            payment_status
     FROM payments
     ORDER BY payment_date DESC
     LIMIT 10"
);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Admin Dashboard</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link
      rel="icon"
      href="assets/img/kaiadmin/favicon.ico"
      type="image/x-icon"
    />

    <!-- Fonts and icons -->
    <script src="assets/js/plugin/webfont/webfont.min.js"></script>
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
          urls: ["assets/css/fonts.min.css"],
        },
        active: function () {
          sessionStorage.fonts = true;
        },
      });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/plugins.min.css" />
    <link rel="stylesheet" href="assets/css/kaiadmin.min.css" />
    
  </head>
  <body>
    <div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="admindashboard.php" class="logo">
              <img
                src="assets/img/kaiadmin/logo_light.svg"
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
              <li class="nav-item active">
                <a data-bs-toggle="collapse" href="admindashboard.php" class="collapsed" aria-expanded="false">
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
                <a href="tables\propertymanagement.php">
                   <i class="fas fa-building"></i>
                  <p>Property</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="tables/tenantmanagement.php">
                   <i class="fas fa-user-friends"></i>
                  <p>Tenants</p>
                  <span class="badge badge-success"><?php echo $newTenants; ?></span>
                </a>
              </li>

              <li class="nav-item">
                <a href="tables/adminmanagement.php">
                   <i class="fas fa-user-shield"></i>
                  <p>Admin</p>
                  <span class="badge badge-success"><?php echo $newAdmins; ?></span>
                </a>
              </li>

              <li class="nav-item">
                <a href="tables/lease.php">
                  <i class="fas fa-chalkboard-teacher"></i>
                  <p>Lease</p>
                </a>
              </li>

              <li class="nav-item">
                <a href="tables/payments.php">
                  <i class="fas fa-wallet"></i>
                  <p>Payments</p>
                  <span class="badge badge-success"><?php echo $newPayments; ?></span>
                </a>
              </li>

              <li class="nav-item">
                <a href="tables/maintenancerequest.php">
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
                        src="<?php echo htmlspecialchars($admin['pictures']); ?>"
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
                            <img src="<?php echo htmlspecialchars($admin['pictures']); ?>" alt="profile image" class="avatar-img rounded"/>
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
            <div
              class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4"
            >
              <div>
                <h3 class="fw-bold mb-3">Dashboard</h3>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div
                          class="icon-big text-center icon-primary bubble-shadow-small"
                        >
                          <i class="fas fa-users"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Tenants</p>
                          <h4 class="card-title"><?php echo $totalTenants; ?></h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div
                          class="icon-big text-center icon-info bubble-shadow-small"
                        >
                          <i class="fas fa-building"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Properties</p>
                          <h4 class="card-title"><?php echo $totalProperties; ?></h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div
                          class="icon-big text-center icon-success bubble-shadow-small"
                        >
                          <i class="icon-credit-card"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Payments</p>
                          <h4 class="card-title">RM <?php echo number_format($totalIncome, 2); ?></h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div
                          class="icon-big text-center icon-secondary bubble-shadow-small"
                        >
                          <i class="icon-wrench"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Maintenance</p>
                          <h4 class="card-title"><?php echo $totalMaintenance; ?></h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Report Section -->
            <div class="row">
              <div class="col-md-12">
                <div class="card card-round" id="incomeReport">
                  <div class="card-header">
                    <div class="card-head-row">
                      <div class="card-title">Income Report</div>
                      <div class="card-tools">
                        <a href="export_income.php" class="btn btn-label-success btn-round btn-sm me-2">
                          <span class="btn-label">
                            <i class="fas fa-file-export"></i>
                          </span>
                          Export
                        </a>
                        <button onclick="printIncomeReport()" class="btn btn-label-info btn-round btn-sm">
                        <span class="btn-label">
                        <i class="fa fa-print"></i>
                        </span>
                        Print
                      </button>
                    </div>
                  </div>
                </div>
                    <div id="myChartLegend"></div>
                  <div class="card-body">
                    <div class="chart-container" style="height:375px; width: 100%;">
                      <canvas id="statisticsChart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!--Summary of the income report-->
            <div class="table-responsive mt-4">
            <table class="table table-bordered">
            <thead>
              <tr>
                <th>Month</th>
                <th>Total Income (RM)</th>
              </tr>
            </thead>

            <tbody>
            <?php
              mysqli_data_seek($monthlyIncomeQuery, 0);

              while($row = mysqli_fetch_assoc($monthlyIncomeQuery))
              {
            ?>
            <tr>
              <td><?php echo $row['month']; ?></td>
              <td>
                  RM <?php echo number_format($row['total_income'],2); ?>
              </td>
            </tr>
            <?php } ?>
            </tbody>
            </table>
          </div>

            <!-- customer section-->
            <div class="row">
              <div class="col-md-4">
                <div class="card card-round">
                  <div class="card-body">
                    <div class="card-head-row card-tools-still-right">
                      <div class="card-title">Tenants</div>
                      <div class="card-tools">
                        <div class="dropdown">
                          <button
                            class="btn btn-icon btn-clean me-0"
                            type="button"
                            id="dropdownMenuButton"
                            data-bs-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                          >
                            <i class="fas fa-ellipsis-h"></i>
                          </button>
                          <div
                            class="dropdown-menu"
                            aria-labelledby="dropdownMenuButton"
                          >
                            <a class="dropdown-item" href="tables/tenantmanagement.php">View Details</a>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="card-list py-4">
                      <?php while($tenant = mysqli_fetch_assoc($tenantListQuery)) { ?>
                      <div class="item-list">
                      <div class="avatar">
                        <img src="<?php echo $tenant['pictures']; ?>" class="avatar-img rounded-circle">
                      </div>

                      <div class="info-user ms-3">
                        <div class="username">
                          <?php echo htmlspecialchars($tenant['full_name']); ?>
                        </div>

                        <div class="status">
                          <?php echo htmlspecialchars($tenant['email']); ?>
                        </div>
                      </div>
                        <?php if($tenant['status'] == 'active') { ?>
                          <span class="badge badge-success">Active</span>
                        <?php } else { ?>
                          <span class="badge badge-danger">Inactive</span>
                        <?php } ?>
                      </div>
                      <?php } ?>
                        
                    </div>
                  </div>
                </div>
              </div>

              <!-- transaction section -->
              <div class="col-md-8">
                <div class="card card-round">
                  <div class="card-header">
                    <div class="card-head-row card-tools-still-right">
                      <div class="card-title">Transaction History</div>
                      <div class="card-tools">
                        <div class="dropdown">
                          <button class="btn btn-icon btn-clean me-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-h"></i>
                          </button>
                          <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="tables/lease.php">View Details</a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-body p-0">
                    <div class="table-responsive">
                      <!-- Projects table -->
                      <table class="table align-items-center mb-0">
                        <thead class="thead-light">
                          <tr>
                            <th scope="col">Payment Number</th>
                            <th scope="col" class="text-end">Date & Time</th>
                            <th scope="col" class="text-end">Amount</th>
                            <th scope="col" class="text-end">Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          <!--<tr>
                            <th scope="row">
                              Payment from #10231
                            </th>
                            <td class="text-end">Mar 19, 2020, 2.45pm</td>
                            <td class="text-end">RM 250.00</td>
                            <td class="text-end">
                              <span class="badge badge-success">Completed</span>
                            </td>
                          </tr>-->
                          <?php while($payment = mysqli_fetch_assoc($transactionQuery)) { ?>
                          <tr>
                          <th scope="row">
                            #<?php echo $payment['payment_id']; ?>
                          </th>

                          <td class="text-end">
                            <?php echo date('d M Y', strtotime($payment['payment_date'])); ?>
                          </td>

                          <td class="text-end">
                            RM <?php echo number_format($payment['payment_amount'], 2); ?>
                          </td>

                            <td class="text-end">

                            <?php if($payment['payment_status'] == 'paid') { ?>
                              <span class="badge bg-success">Paid</span>

                            <?php } elseif($payment['payment_status'] == 'pending') { ?>
                              <span class="badge bg-warning text-dark">Pending</span>

                            <?php } else { ?>
                              <span class="badge bg-danger">Overdue</span>
                            <?php } ?>
                          </td>
                        </tr>
                      <?php } ?>
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
    <script src="assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Chart JS -->
    <script src="assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Datatables -->
    <script src="assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- jQuery Vector Maps -->
    <script src="assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="assets/js/plugin/jsvectormap/world.js"></script>

    <!-- Sweet Alert -->
    <script src="assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="assets/js/kaiadmin.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    
<script>
var ctx = document.getElementById("statisticsChart").getContext("2d");
Chart.register(ChartDataLabels);
new Chart(ctx, {
    plugins: [ChartDataLabels],
    type: "bar",

            data: { labels: <?php echo json_encode($months); ?>, 
            datasets: [{ label: "Monthly Income", data: <?php echo json_encode($incomeData); ?>, 
            backgroundColor: [ 
            "#4BC0C0", 
            "#36A2EB", 
            "#1E88E5", 
            "#F4D35E", 
            "#FF9800", 
            "#8E044D", 
            "#F4435E", 
            "#5AD17B", 
            "#5C6BC0", 
            "#E056D8", 
            "#26A69A", 
            "#7E57C2" ], 
            
            borderRadius: 10, 
            borderSkipped: false, 
            barThickness: 50, 
            maxBarThickness: 60, 
            barPercentage: 0.8, 
            categoryPercentage: 0.9 
        }] 
    }, 
    
    options: { 
      responsive: true, 
      maintainAspectRatio: false, 
      layout: { 
        padding: { 
          top: 30 } 
        }, 
        
        plugins: { 
          datalabels: { 
            anchor: 'end', 
            align: 'top', 
            color: '#000', 
            font: { 
              weight: 'bold', 
              size: 12 }, 
              
              formatter: function(value) { 
                return 'RM ' + value; } 
              }, 
              
        legend: { 
          display: false 
        }, 
        
        title: { 
          display: true, 
          text: "Monthly Rental Income Report", 
          font: { 
            size: 22, 
            weight: 'bold' 
          } 
        } 
      }, 
      
      scales: { 
        y: { 
          beginAtZero: true, 

          title: { 
          display: true, 
          text: "Income (RM)" 
        }, 
        
          grid: { 
            color: "#E5E5E5" 
          } 
        }, 
        
          x: { 
            title: { 
              display: true, 
              text: "Month" 
            }, 
            
          grid: { 
            display: false 
          } 
        } 
      } 
    } 
  });
</script>

<!-- For printing the income report section -->
 <script>
function printIncomeReport() {

    var reportContent =
        document.getElementById("incomeReport").innerHTML;

    var printWindow = window.open('', '', 'width=1000,height=700');

    printWindow.document.write(`
        <html>
        <head>
            <title>Income Report</title>

            <link rel="stylesheet"
                  href="assets/css/bootstrap.min.css">

            <style>
                body{
                    padding:20px;
                    font-family:Arial,sans-serif;
                }

                .btn,
                .card-tools{
                    display:none;
                }
            </style>
        </head>

        <body>
            ${reportContent}
        </body>

        </html>
    `);

    printWindow.document.close();

    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 500);
}
</script>
  </body>
</html>
