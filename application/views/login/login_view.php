<?php
defined('BASEPATH') or exit('No direct script access allowed');
$v = date("His");
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-wide  customizer-hide" dir="ltr" data-theme="theme-default"
  data-assets-path="../assets/" data-template="vertical-menu-template">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>(SW) TOGETAMOVE</title>
  <!-- Favicon -->
  <link rel="manifest" href="<?php echo base_url('manifest.json'); ?>">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="ToGetaMove">
  <link rel="icon" type="" href="../assets/img/favicon/logo_togetamove.png" />
  <link rel="apple-touch-icon" href="../assets/img/favicon/logo_togetamove.png">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet">
  <!-- Icons -->
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  <!-- Core CSS -->
  <link rel="stylesheet" href="../assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../assets/vendor/css/rtl/theme-defaults.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />
  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/typeahead-js/typeahead.css" />
  <!-- Vendor -->
  <link rel="stylesheet" href="../assets/vendor/libs/@form-validation/umd/styles/index.min.css" />
  <!-- Page -->
  <link rel="stylesheet" href="../assets/vendor/css/pages/page-auth.css">
  <!-- Helpers -->
  <script src="../assets/vendor/js/helpers.js"></script>
  <script src="../assets/js/config.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.min.css" rel="stylesheet">
</head>

<body style="background-color: #FFFFFF">

  <style>
    .app-brand {
      background: transparent !important;
    }

    .input-group {
      display: flex;
      align-items: center;
    }

    .input-group .form-control {
      height: calc(2.5rem + 2px);
      border-top-right-radius: 0;
      border-bottom-right-radius: 0;
    }

    .input-group .input-group-text {
      height: calc(2.5rem + 2px);
      border-top-left-radius: 0;
      border-bottom-left-radius: 0;
      background-color: #f8f9fa;
      border: 1px solid #ced4da;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .form-label {
      font-weight: bold;
      color: #495057;
    }
  </style>
  <!-- Section: Design Block -->
  <section class="">
    <!-- Jumbotron -->
    <div class="px-4 py-5 px-md-5 text-lg-start">
      <div class="container"></br></br></br></br>
        <div class="row gx-lg-5 align-items-center">

          <!-- <div class="col-lg-6 mb-5 mb-lg-0">
            <h1 class="my-5 display-3 fw-bold ls-tight">
              eTraveling <br />
              <span class="text-primary">For your business</span>
            </h1>
          </div> -->

          <div class="col-lg-6 mb-5 mb-lg-0">
            <div class="app-brand justify-content-center mb-0 text-center">
              <img src="../assets/img/favicon/logo_animation.gif" style="width: 85%;" />
            </div>
          </div>

          <div class="col-lg-6 mb-5 mb-lg-0">
            <div class="card" style="background-color: #FFFFFF">
              <div class="card-body py-5 px-md-5">
                <!-- 2 column grid layout with text inputs for the first and last names -->
                <div class="row">

                  <!-- Email input -->
                  <div class="mb-3">
                    <label for="defaultFormControlInput" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" placeholder="Employee Code"
                      aria-describedby="defaultFormControlHelp" />
                    <div id="defaultFormControlHelp" class="form-text">We'll never share your details with anyone
                      else.</div>
                  </div>

                  <!-- Password input -->
                  <div class="form-outline mb-4 position-relative">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                      <input type="password" id="password" class="form-control" placeholder="Enter your password" />
                      <span class="input-group-text toggle-password" style="cursor: pointer;">
                        <i class="bx bx-hide"></i>
                      </span>
                    </div>
                  </div>

                  <!-- Checkbox -->
                  <div class="form-check d-flex justify-content-center mb-4">
                    <input class="form-check-input me-2" type="checkbox" value="" id="remember" checked />
                    <label class="form-check-label" for="form2Example33">
                      Remember Me
                    </label>
                  </div>

                  <!-- Submit button -->
                  <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4"
                    id="btn_login">
                    Sign in
                  </button>

                  <!-- Forgot password link -->
                  <div class="text-center">
                    <a href="#" id="forgotPasswordLink" onclick="forgotPassword()">forgot password?</a>
                  </div>


                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Jumbotron -->
  </section>
  <!-- Section: Design Block -->


  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/libs/hammer/hammer.js"></script>
  <!-- <script src="../assets/vendor/libs/i18n/i18n.js"></script> -->
  <script src="../assets/vendor/libs/typeahead-js/typeahead.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>
  <!-- Vendors JS -->
  <script src="../assets/vendor/libs/@form-validation/umd/bundle/popular.min.js"></script>
  <script src="../assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js"></script>
  <script src="../assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js"></script>
  <!-- Main JS -->
  <script src="../assets/js/main.js"></script>
  <!-- Page JS -->
  <script src="../assets/js/pages-auth.js"></script>
  <script src="../assets/js/Login/login_view_js.js?v=<?php echo $v; ?>"></script>
  <script>
    $(document).ready(function () {
      $(".toggle-password").on("click", function () {
        const passwordField = $("#password");
        const icon = $(this).find("i");
        const isPassword = passwordField.attr("type") === "password";
        passwordField.attr("type", isPassword ? "text" : "password");
        icon.toggleClass("bx-hide bx-show");
      });
    });
  </script>
</body>

</html>

<!-- beautify ignore:end -->
