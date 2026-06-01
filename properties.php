<?php
session_start();
include 'databaseconnection.php';

$propertyQuery = mysqli_query(
    $conn,
    "SELECT * 
     FROM properties 
     WHERE activation='active'
     ORDER BY property_id DESC"
);
?>


<!-- /*
* Template Name: Property
* Template Author: Untree.co
* Template URI: https://untree.co/
* License: https://creativecommons.org/licenses/by/3.0/
*/ -->
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="author" content="Untree.co" />
    <link rel="shortcut icon" href="favicon.png" />

    <meta name="description" content="" />
    <meta name="keywords" content="bootstrap, bootstrap5" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="fonts/icomoon/style.css" />
    <link rel="stylesheet" href="fonts/flaticon/font/flaticon.css" />

    <link rel="stylesheet" href="css/tiny-slider.css" />
    <link rel="stylesheet" href="css/aos.css" />
    <link rel="stylesheet" href="css/style.css" />

    <title>
      Property &mdash; Free Bootstrap 5 Website Template by Untree.co
    </title>
    <style>
    .btn-secondary.disabled, .btn-secondary:disabled {
    background-color: #555 !important; /* dark grey */
    color: #fff !important;
    cursor: not-allowed;
    pointer-events: none; /* makes it unclickable */
    }
    </style>
  </head>
  <body>
    <div class="site-mobile-menu site-navbar-target">
      <div class="site-mobile-menu-header">
        <div class="site-mobile-menu-close">
          <span class="icofont-close js-menu-toggle"></span>
        </div>
      </div>
      <div class="site-mobile-menu-body"></div>
    </div>

    <nav class="site-nav">
      <div class="container">
        <div class="menu-bg-wrap">
          <div class="site-navigation">
            <a href="index.html" class="logo m-0 float-start">Property</a>

            <ul
              class="js-clone-nav d-none d-lg-inline-block text-start site-menu float-end"
            >
              <li><a href="index.php">Home</a></li>
              <li class="active"> <a href="properties.php">Properties</a></li>
              <li><a href="login.php">Login/Sign Up</a></li>
            </ul>

            <a
              href="#"
              class="burger light me-auto float-end mt-1 site-menu-toggle js-menu-toggle d-inline-block d-lg-none"
              data-toggle="collapse"
              data-target="#main-navbar"
            >
              <span></span>
            </a>
          </div>
        </div>
      </div>
    </nav>

    <div
      class="hero page-inner overlay"
      style="background-image: url('images/hero_bg_1.jpg')"
    >
      <div class="container">
        <div class="row justify-content-center align-items-center">
          <div class="col-lg-9 text-center mt-5">
            <h1 class="heading" data-aos="fade-up">Properties</h1>

            <nav
              aria-label="breadcrumb"
              data-aos="fade-up"
              data-aos-delay="200"
            >
              <ol class="breadcrumb text-center justify-content-center">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active text-white-50" aria-current="page" style="color: white !important">
                  Properties
                </li>
              </ol>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Properties Section -->
    <div class="section section-properties">
        <div class="container">
          <div class="row">
            <?php while($property = mysqli_fetch_assoc($propertyQuery)) { 
                $isRented = ($property['occupancy_status'] === 'rented');
                $btnClass = $isRented ? 'btn-secondary disabled' : 'btn-primary';
            ?>
            <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4 mb-4">
              <div class="property-item mb-30">

                <a href="<?php echo $isRented ? '#' : 'login.php'; ?>">
                  <img src="Admin/<?php echo htmlspecialchars($property['property_image']); ?>" 
                      class="img-fluid"
                      style="height:250px; width:100%; object-fit:cover;"
                      alt="<?php echo htmlspecialchars($property['property_name']); ?>">
                </a>

                <div class="property-content">
                  <div class="price mb-2">
                    <span>RM <?php echo number_format($property['rental_price'], 2); ?></span>
                  </div>

                  <span class="d-block mb-2 text-black-50">
                    <?php echo htmlspecialchars($property['address']); ?>
                  </span>

                  <span class="city d-block mb-3">
                    <?php echo ucfirst($property['property_type']); ?>
                  </span>

                  <div class="specs d-flex mb-4">
                    <span class="d-flex align-items-center me-3">
                      <span class="icon-bed me-2"></span>
                      <span class="caption"><?php echo $property['number_of_rooms'] ?? 0; ?> rooms</span>
                    </span>

                    <span class="d-flex align-items-center">
                      <span class="icon-bath me-2"></span>
                      <span class="caption">
                        <?php echo ucfirst($property['occupancy_status']); ?>
                      </span>
                    </span>
                  </div>

                  <a href="<?php echo $isRented ? '#' : 'login.php'; ?>" 
                    class="btn py-2 px-3 <?php echo $btnClass; ?>">
                    <?php echo $isRented ? 'Rented' : 'Book Now'; ?>
                  </a>
                </div>
              </div>
            </div>
            <?php } ?>
          </div>

          <!-- Centered Pagination -->
          <div class="row py-5">
            <div class="col-12 d-flex justify-content-center">
              <div class="custom-pagination">
                <?php 
                  // Dynamic pagination: count total records
                  $totalProperties = mysqli_num_rows($propertyQuery);
                  $propertiesPerPage = 9; // Adjust how many per page
                  $totalPages = ceil($totalProperties / $propertiesPerPage);

                  for($i = 1; $i <= $totalPages; $i++) {
                    echo '<a href="properties.php?page='.$i.'"'.($i==1?' class="active"':'').'>'.$i.'</a>';
                  }
                ?>
              </div>
            </div>
          </div>

        </div>
      </div>

    <!-- Continue for the bottom section -->
    <div class="site-footer">
    <div class="container">
        <div class="row">
          <div class="col-lg-4">
            <div class="widget">
              <h3>Contact</h3>
              <address>Ayer Keroh, Melaka</address>
              <ul class="list-unstyled links">
                <li>
                  <span class="icon-phone"></span>
                  <a></a>
                  <a href="tel://125845236">+60-125845236</a>
                </li>
                <li>
                  <span class="icon-phone"></span>
                  <a></a>
                  <a href="tel://1155485623">+60-1155485623</a>
                </li>
                <li>
                  <span class="icon-envelope"></span>
                  <a></a>
                  <a href="mailto:adminl@gmail.com">admin@gmail.com</a>
                </li>
              </ul>
            </div>
            <!-- /.widget -->
          </div>
          <!-- /.col-lg-4 -->
          <div class="col-lg-4">
            <div class="widget">
              <h3>Sources</h3>
              <ul class="list-unstyled float-start links">
                <li><a href="properties.php">Property</a></li>
              </ul>
            </div>
            <!-- /.widget -->
          </div>
          <!-- /.col-lg-4 -->
          <div class="col-lg-4">
            <div class="widget">
              <h3>Links</h3>
              <ul class="list-unstyled links">
              </ul>

              <ul class="list-unstyled social">
                <li>
                  <a><span class="icon-instagram"></span></a>
                </li>
                <li>
                  <a><span class="icon-twitter"></span></a>
                </li>
                <li>
                  <a><span class="icon-facebook"></span></a>
                </li>
                <li>
                  <a><span class="icon-linkedin"></span></a>
                </li>
              </ul>
            </div>
            <!-- /.widget -->
          </div>
          <!-- /.col-lg-4 -->
        </div>
        <!-- /.row -->

        <div class="row mt-5">
          <div class="col-12 text-center">
            <!-- 
              **==========
              NOTE: 
              Please don't remove this copyright link unless you buy the license here https://untree.co/license/  
              **==========
            -->

            <p>
              Copyright &copy;
              <script>
                document.write(new Date().getFullYear());
              </script>
              . All Rights Reserved. &mdash; Designed with love by
              <a href="https://untree.co">Untree.co</a>
              <!-- License information: https://untree.co/license/ -->
            </p>
            <div>
             TSE6223 Software Engineering Fundamentals - Group Project
            </div>
            <div>
              Property Rental Management System
            </div>
          </div>
        </div>
      </div>
      <!-- /.container -->
    </div>
    <!-- /.site-footer -->

    <!-- Preloader -->
    <div id="overlayer"></div>
    <div class="loader">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/tiny-slider.js"></script>
    <script src="js/aos.js"></script>
    <script src="js/navbar.js"></script>
    <script src="js/counter.js"></script>
    <script src="js/custom.js"></script>
  </body>
</html>
