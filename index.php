<!-- /*
* Template Name: Property
* Template Author: Untree.co
* Template URI: https://untree.co/
* License: https://creativecommons.org/licenses/by/3.0/
*/ -->

<?php
session_start();
include 'databaseconnection.php';

// Fetch properties for homepage slider
$propertyQuery = mysqli_query(
    $conn,
    "SELECT *
     FROM properties
     WHERE activation='active'
     ORDER BY property_id DESC
     LIMIT 10"
);
?>
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
      Property Rental Management System
    </title>

    <style>
      .property-slider .property-item {
      height: auto !important;
      min-height: 400px; /* optional, adjust as needed */
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      }
      .property-item .img {
          height: auto !important;
          max-height: 300px; /* optional */
      }
      .property-item img {
          width: 100%;
          height: auto;
      }

      .person {
    height: 120px;              /* fixed card height */
    display: flex;
    flex-direction: column;
    border-radius: 10px;
    background: #eaf2f8;
}

.person-contents {
    flex: 1;
    padding: 15px;
    display: flex;
    flex-direction: column;
}

.person-contents p {
    flex: 1;                   /* pushes social icons to bottom */
    overflow: hidden;
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
            <a href="index.html" class="logo m-0 float-start">Property Rental Management System</a>

            <ul class="js-clone-nav d-none d-lg-inline-block text-start site-menu float-end">
              <li class="active"><a href="index.php">Home</a></li>
              <li> <a href="properties.php">Properties</a></li>
              <li><a href="about.html">About Us</a></li>
              <li><a href="contact.html">Contact Us</a></li>
              <li><a href="login.php">Login/Sign Up</a></li>
            </ul>

            <a href="#" class="burger light me-auto float-end mt-1 site-menu-toggle js-menu-toggle d-inline-block d-lg-none" data-toggle="collapse" data-target="#main-navbar">
              <span></span>
            </a>
          </div>
        </div>
      </div>
    </nav>

    <div class="hero">
      <div class="hero-slide">
        <div
          class="img overlay"
          style="background-image: url('images/hero_bg_3.jpg')"
        ></div>
        <div
          class="img overlay"
          style="background-image: url('images/hero_bg_2.jpg')"
        ></div>
        <div
          class="img overlay"
          style="background-image: url('images/hero_bg_1.jpg')"
        ></div>
      </div>

      <div class="container">
        <div class="row justify-content-center align-items-center">
          <div class="col-lg-9 text-center">
            <h1 class="heading" data-aos="fade-up">
              Easiest way to find your dream home
            </h1>
            <form
              action="#"
              class="narrow-w form-search d-flex align-items-stretch mb-3"
              data-aos="fade-up"
              data-aos-delay="200"
            >
            </form>
          </div>
        </div>
      </div>
    </div>

    <div class="section">
      <div class="container">
        <div class="row mb-5 align-items-center">
          <div class="col-lg-6">
            <h2 class="font-weight-bold text-primary heading">
              Popular Properties
            </h2>
          </div>
          <div class="col-lg-6 text-lg-end">
            <p>
              <a
                href="properties.php"
                target="_blank"
                class="btn btn-primary text-white py-3 px-4"
                >View all properties</a
              >
            </p>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="property-slider-wrap">
              <div class="property-slider">
                <?php while($property = mysqli_fetch_assoc($propertyQuery)) { ?>
                <div class="property-item">
                  <a href="propertiesitem.php?id=<?php echo $property['property_id']; ?>" class="img">
                    <img src="Admin/<?php echo htmlspecialchars($property['property_image']); ?>" style="width:100%;height:250px;">-
                  </a>

                  <div class="property-content">
                    <div class="price mb-2">
                      <span>RM <?php echo number_format($property['rental_price'], 2); ?></span>
                    </div>

                    <div>
                      <span class="d-block mb-2 text-black-50">
                        <?php echo htmlspecialchars($property['address']); ?>
                      </span>
                      <span class="city d-block mb-3"><?php echo ucfirst($property['property_type']); ?></span>

                      <div class="specs d-flex mb-4">
                        <span class="d-block d-flex align-items-center me-3">
                          <span class="icon-bed me-2"></span>
                          <span class="caption"><?php echo $property['number_of_rooms'] ?? 0; ?> rooms</span>
                        </span>
                        <span class="d-block d-flex align-items-center">
                          <span class="icon-bath me-2"></span>
                          <span class="caption"><?php echo ($property['occupancy_status']=='available') ? 'Available' : 'Rented'; ?></span>
                        </span>
                      </div>

                      <a href="properties.php" class="btn btn-primary py-2 px-3">
                        See details
                      </a>
                    </div>
                  </div>
                </div>
              <?php } ?>
              </div>

              <div
                id="property-nav"
                class="controls"
                tabindex="0"
                aria-label="Carousel Navigation"
              >
                <span
                  class="prev"
                  data-controls="prev"
                  aria-controls="property"
                  tabindex="-1"
                  >Prev</span
                >
                <span
                  class="next"
                  data-controls="next"
                  aria-controls="property"
                  tabindex="-1"
                  >Next</span
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <section class="features-1">
      <div class="container">
        <div class="row">
          <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
            <div class="box-feature">
              <span class="flaticon-house"></span>
              <h3 class="mb-3">Our Properties</h3>
              <p>
                Browse verified rental homes across Melacca City.
              </p>
            </div>
          </div>
          <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="500">
            <div class="box-feature">
              <span class="flaticon-building"></span>
              <h3 class="mb-3">Property for Rent</h3>
              <p>
                Affordable rooms, condos, and apartments available now.
              </p>
            </div>
          </div>
          <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
            <div class="box-feature">
              <span class="flaticon-house-3"></span>
              <h3 class="mb-3">Trusted Agents</h3>
              <p>
                All listings are managed by verified property agents.
              </p>
            </div>
          </div>
          <div class="col-6 col-lg-3" data-aos="fade-up" data-aos-delay="600">
            <div class="box-feature">
              <span class="flaticon-house-1"></span>
              <h3 class="mb-3">Easy Booking</h3>
              <p>
                Book your property in just a few clicks.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <div class="section section-5 bg-light">
      <div class="container">
        <div class="row justify-content-center text-center mb-5">
          <div class="col-lg-6 mb-1">
            <h2 class="font-weight-bold heading text-primary mb-4">
              Our Teams
            </h2>
            <p class="text-black-50">
              Experienced in helping tenants find affordable and quality homes.
            </p>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
            <div class="h-100 person">
              <img
                src="images/teamlead.png"
                alt="Image"
                class="img-fluid"
              />

              <div class="person-contents">
                <h2 class="mb-0"><a href="#">Lau Shereen</a></h2>
                <span class="meta d-block mb-3">Team Leader</span>
                <p>
                  Student ID: 252UT254KV 
                </p>

                <ul class="social list-unstyled list-inline dark-hover">
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-twitter"></span></a>
                  </li>
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-facebook"></span></a>
                  </li>
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-linkedin"></span></a>
                  </li>
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-instagram"></span></a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
            <div class="h-100 person">
              <img
                src="images/mem3.png"
                alt="Image"
                class="img-fluid"
              />

              <div class="person-contents">
                <h2 class="mb-0"><a href="#">Eileen Chin </a></h2>
                <span class="meta d-block mb-3">Team Member 1</span>
                <p>
                  Student ID: 242UT2444B 
                </p>

                <ul class="social list-unstyled list-inline dark-hover">
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-twitter"></span></a>
                  </li>
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-facebook"></span></a>
                  </li>
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-linkedin"></span></a>
                  </li>
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-instagram"></span></a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-sm-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
            <div class="h-100 person">
              <img
                src="images/mem2.png"
                alt="Image"
                class="img-fluid"
              />

              <div class="person-contents">
                <h2 class="mb-0"><a href="#">Mishalini A/P S.Rajajee </a></h2>
                <span class="meta d-block mb-3">Team Member 2</span>
                <p>
                  Student ID: 1211104941 
                </p>

                <ul class="social list-unstyled list-inline dark-hover">
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-twitter"></span></a>
                  </li>
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-facebook"></span></a>
                  </li>
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-linkedin"></span></a>
                  </li>
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-instagram"></span></a>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-sm-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
            <div class="h-100 person">
              <img
                src="images/teammem.png"
                alt="Image"
                class="img-fluid"
              />

              <div class="person-contents">
                <h2 class="mb-0"><a href="#">Yong Mei Yan</a></h2>
                <span class="meta d-block mb-3">Team Member 3</span>
                <p>
                  Student ID: 253UT256JJ
                </p>

                <ul class="social list-unstyled list-inline dark-hover">
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-twitter"></span></a>
                  </li>
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-facebook"></span></a>
                  </li>
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-linkedin"></span></a>
                  </li>
                  <li class="list-inline-item">
                    <a href="#"><span class="icon-instagram"></span></a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Continue for the bottom section -->
    <div class="section">
      <div class="container">
        <div class="row">
          <div
            class="col-lg-4 mb-5 mb-lg-0"
            data-aos="fade-up"
            data-aos-delay="100"
          >
            <div class="contact-info">
              <div class="address mt-2">
                <i class="icon-room"></i>
                <h4 class="mb-2">Location:</h4>
                <p>
                  Jalan Ayer Keroh Lama, 75450 Bukit Beruang,<br />
                  Melaka
                </p>
              </div>

              <div class="open-hours mt-4">
                <i class="icon-clock-o"></i>
                <h4 class="mb-2">Open Hours:</h4>
                <p>
                  Monday-Sunday:<br />
                  9:00 AM - 5:00 PM
                </p>
              </div>

              <div class="email mt-4">
                <i class="icon-envelope"></i>
                <h4 class="mb-2">Email:</h4>
                <p>admin@gmail.com</p>
              </div>

              <div class="phone mt-4">
                <i class="icon-phone"></i>
                <h4 class="mb-2">Call:</h4>
                <p>+60-125845236</p>
                <p>+60-1155485623</p>
              </div>
            </div>
          </div>
          <div class="col-lg-8" data-aos="fade-up" data-aos-delay="200">
            <!--Embedded the google maps-->
            <div class="map-container">
              <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.724484534347!2d102.219793!3d2.190656!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d1a5e0c8c9d5b7%3A0x4c6b5e5e5e5e5e5e!2sJalan%20Ayer%20Keroh%20Lama%2C%2075450%20Bukit%20Beruang%2C%20Melaka!5e0!3m2!1sen!2smy!4v1634567890123!5m2!1sen!2smy"
                width="100%"
                height="450"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
              ></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.untree_co-section -->

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
                <li><a href="about.html">About us</a></li>
                <li><a href="services.html">Services</a></li>
                <li><a href="terms.html">Terms</a></li>
                <li><a href="privacy.html">Privacy</a></li>
                <li><a href="faq.html">FAQ</a></li>
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
