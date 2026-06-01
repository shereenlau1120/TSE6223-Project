<?php
session_start();
include 'databaseconnection.php';

$alert = null;

if (isset($_SESSION['alert'])) {
    $alert = $_SESSION['alert'];
    unset($_SESSION['alert']); // important: show once only
}

// For SignUp checking
if (isset($_POST['signup'])) {

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['alert'] = ['message' => 'Invalid email format!', 'type' => 'error'];
        header("Location: login.php");
        exit();
    }

    if (!preg_match('/^[0-9]{11,12}$/', $phone)) {
        $_SESSION['alert'] = ['message' => 'Phone must be 11–12 digits!', 'type' => 'error'];
        header("Location: login.php");
        exit();
    }

    if (!preg_match('/^[a-zA-Z\s]+$/', $fullname)) {
        $_SESSION['alert'] = ['message' => 'Name must contain letters only!', 'type' => 'error'];
        header("Location: login.php");
        exit();
    }

    if ($_POST['password'] !== $_POST['confirm_password']) {
        $_SESSION['alert'] = ['message' => 'Passwords do not match!', 'type' => 'error'];
        header("Location: login.php");
        exit();
    }

    $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (full_name, pictures, email, phone_number, password, role)
                            VALUES (?, 'assets/img/profileimej.jpg', ?, ?, ?, 'tenant')");

    $stmt->bind_param("ssss", $fullname, $email, $phone, $hashed);

    if ($stmt->execute()) {
        $_SESSION['alert'] = ['message' => 'Signup successful!', 'type' => 'success'];
    } else {
        $_SESSION['alert'] = ['message' => 'Signup failed!', 'type' => 'error'];
    }

    header("Location: login.php");
    exit();
}

//For login checking
if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'admin') {
            header("Location: Admin/admindashboard.php");
            exit();
        } elseif ($user['role'] == 'tenant') {
            header("Location: User\html\index.html");
            exit();
        } else {
            header("Location: index.php");
            exit();
        }

    } else {
        $_SESSION['alert'] = ['message' => 'Invalid email or password!', 'type' => 'error'];

        header("Location: login.php"); 
        exit(); 
    }
}
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="fonts/icomoon/style.css" />
    <link rel="stylesheet" href="fonts/flaticon/font/flaticon.css" />

    <link rel="stylesheet" href="css/tiny-slider.css" />
    <link rel="stylesheet" href="css/aos.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/signupform.css" />

    <title>
      Login/Signup
    </title>
    <style>
    .password-wrapper {
    position: relative;
    width: 100%;
    }

    .password-wrapper input {
    width: 100%;
    height: 50px;
    padding-right: 45px !important;
    box-sizing: border-box;
    }

    .password-wrapper .toggle-password {
    position: absolute;
    right: 15px;
    top: 25px; /* center of 50px input */
    transform: translateY(-50%);
    cursor: pointer;
    color: #777;
    font-size: 18px;
    z-index: 999;
    }

    .password-wrapper .toggle-password:hover {
    color: #1a75ff;
    }
</style>
  </head>
  <body>
    <!-- Dark background overlay -->
<div id="alertOverlay" class="alert-overlay"></div>

<!-- Alert Box -->
<div id="alertBox" class="alert-box">
  <div id="alertMessage"></div>
  <button onclick="closeAlert()" class="alert-btn">OK</button>
</div>
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
              <li> <a href="properties.php">Properties</a></li>
              <li class="active"><a href="login.php">Login/Sign Up</a></li>
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
      style="background-image: url('images\\login page image.png')"
    >
      <div class="container">
        <div class="row justify-content-center align-items-center">
          <div class="col-lg-9 text-center mt-5">
            <h1 class="heading" data-aos="fade-up">Login / Sign Up</h1>
          </div>
        </div>
      </div>
    </div>

    <!-- for the form section -->
     <div class="wrapper">
      <div class="title-text">
        <div class="title login">Login Form</div>
        <div class="title signup">Signup Form</div>
      </div>
      <div class="form-container">
        <div class="slide-controls">
          <input type="radio" name="slide" id="login" checked>
          <input type="radio" name="slide" id="signup">
          <label for="login" class="slide login">Login</label>
          <label for="signup" class="slide signup">Signup</label>
          <div class="slider-tab"></div>
        </div>
        <div class="form-inner">
          <form action="login.php" method="POST" class="login">
            <div class="field">
              <label>Email Address <span class="required">*</span></label>
              <input type="text" name="email" placeholder="Enter your email address" required>
            </div>
            <!--<div class="field">
              <label>Password <span class="required">*</span></label>
              <input type="password" name="password" placeholder="Enter your password" required>
            </div>-->
            <div class="field">
            <label>Password <span class="required">*</span></label>
              <div class="password-wrapper">
              <input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>
              <i class="bi bi-eye toggle-password" onclick="togglePassword('loginPassword', this)"></i>
              </div>
            </div>
            <div class="pass-link"><a href="#">Forgot password?</a></div>
            <div class="field loginbtn">
              <div class="btn-layer"></div>
              <input type="submit" name="login" value="Login">
            </div>
            <div class="signup-link">Haven't an account? <a href="">Signup now</a></div>
          </form>
          <form action="login.php" method="POST" class="signup">
            <div class="field">
            <label>Full Name <span class="required">*</span></label>
            <input type="text" name="fullname" placeholder="Full name, e.g. Ali binti Mohamad" required>
            </div>
            <div class="field">
              <label>Email Address <span class="required">*</span></label>
              <input type="text" name="email" placeholder="Email Address, e.g. aliabu@gmail.com" required>
            </div>
            <div class="field">
              <label>Phone Number <span class="required">*</span></label>
              <input type="tel" name="phone" placeholder="Phone Number, e.g. 012-3456789" required>
            </div>
            <div class="field">
              <label>Password <span class="required">*</span></label>
              <small class="field-hint">Must contain at least 8 characters, including uppercase, lowercase letters and numbers.</small>
              <div class="password-wrapper">
              <input type="password" id="Password1" name="password" placeholder="Password" required>
              <i class="bi bi-eye toggle-password" onclick="togglePassword('Password1', this)"></i>
              </div>
            </div>
            <div class="field">
              <label>Confirm Password <span class="required">*</span></label>
              <div class="password-wrapper">
              <input type="password" id="Password2" name="confirm_password" placeholder="Confirm password" required>
              <i class="bi bi-eye toggle-password" onclick="togglePassword('Password2', this)"></i>
              </div>
            </div>
            <div class="field loginbtn">
              <div class="btn-layer"></div>
              <input type="submit" name="signup" value="Sign Up">
            </div>
          </form>
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
    <script src="js/signupform.js"></script>

    <!-- For the alert box -->

    <script>
    function showAlert(message, type = "info") {
    const box = document.getElementById("alertBox");
    const msg = document.getElementById("alertMessage");
    const overlay = document.getElementById("alertOverlay");

    msg.innerText = message;

    box.className = "alert-box alert-" + type;

    box.style.display = "block";
    overlay.style.display = "block";

    window.shouldRedirect = true;
  }

  function closeAlert() {
    const box = document.getElementById("alertBox");
    const overlay = document.getElementById("alertOverlay");

    box.style.display = "none";
    overlay.style.display = "none";

    if (window.shouldRedirect) {
        window.shouldRedirect = false;
        window.location.href = "login.php";
    }
  }
</script>

<?php if ($alert): ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (!window.alertShown) {
        window.alertShown = true;
        showAlert("<?= addslashes($alert['message']) ?>", "<?= $alert['type'] ?>");
    }
});
</script>
<?php endif; ?>

<script>
function togglePassword(inputId, icon) {

    const input = document.getElementById(inputId);

    if (input.type === "password") {
        input.type = "text";

        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");

    } else {
        input.type = "password";

        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
}
</script>
  </body>
</html>
