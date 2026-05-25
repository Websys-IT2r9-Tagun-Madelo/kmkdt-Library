<?php
if (session_status() === PHP_SESSION_NONE) {
    if (isset($_COOKIE['KMKDT_ADMIN_SESSION'])) {
        session_name('KMKDT_ADMIN_SESSION');
    } elseif (isset($_COOKIE['KMKDT_USER_SESSION'])) {
        session_name('KMKDT_USER_SESSION');
    }
    
    session_set_cookie_params(0, '/');
    session_start();
}

// Redirect logic
if (isset($_SESSION['authUser'])) {
    $dashboard = ($_SESSION['authUser']['userRole'] === 'admin') ? 'admin/index' : 'user/index';
    header("Location: /kmkdt-Library/public/" . $dashboard);
    exit();
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>kmkdt Library | Login</title>

  <link rel="icon" type="image/svg+xml"
    href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%2397ee5b'><path d='M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z'/></svg>">

  <!-- Core Libraries -->
  <link rel="stylesheet" href="/kmkdt-Library/public/assets/libs/owl.carousel/dist/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="/kmkdt-Library/public/assets/libs/aos-master/dist/aos.css">
  <link rel="stylesheet" href="/kmkdt-Library/public/assets/css/styles.css" />

  <!-- Bootstrap Icons CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body>

  <div class="page-wrapper overflow-hidden">
    <section class="bg-light-gray border-top border-4 d-flex align-items-center justify-content-center min-vh-100"
      style="border-top-color: #57cb57 !important;">

      <div class="container py-3">
        <div class="sign-in card mx-auto shadow-lg" style="max-width: 500px;">
          <div class="card-body py-8 px-lg-5">

            <a href="index"
              class="mb-2 hstack justify-content-center text-decoration-none gap-3"
              style="font-size: 4.5rem; letter-spacing: -2px;">

              <span class="fw-light"
                style="color: #2A3547; text-transform: lowercase;">
                kmkdt
              </span>

              <span
                style="font-weight: 900; color: #57cb57; text-transform: lowercase;">
                library
              </span>
            </a>

            <div class="position-relative hstack justify-content-center mt-4 mb-6">
              <hr class="w-100 d-block">

              <p class="mb-0 fs-3 bg-white px-3 position-absolute top-50 start-50 translate-middle"
                style="z-index: 5; white-space: nowrap; font-size: 1.2rem !important; font-weight: 500;">

                Welcome Back, Reader!
              </p>
            </div>

            <form class="d-flex flex-column gap-3 needs-validation"
              action="/kmkdt-Library/app/controller/loginController.php"
              method="POST"
              autocomplete="off"
              novalidate>

              <div>
                <input type="text"
                  name="username"
                  class="form-control border-bottom"
                  id="exampleInputUsername"
                  placeholder="Username"
                  required
                  autocomplete="off"
                  autocorrect="off"
                  autocapitalize="none"
                  spellcheck="false">

                <div class="invalid-feedback">
                  Please enter a valid username.
                </div>
              </div>

              <div>
                <input type="password"
                  name="password"
                  class="form-control border-bottom"
                  id="inputPassword"
                  placeholder="Password"
                  required
                  autocomplete="new-password"
                  autocorrect="off"
                  autocapitalize="none"
                  spellcheck="false">

                <div class="invalid-feedback">
                  Please enter a password.
                </div>
              </div>

              <button type="submit"
                name="loginbutton"
                class="btn btn-dark w-100 d-flex align-items-center justify-content-between py-3 fw-bold my-7 fs-4 px-4">

                <span style="width: 24px;"></span>

                <span class="flex-grow-1 text-center">
                  Login
                </span>

                <span style="width: 24px;"></span>
              </button>
            </form>

            <a class="text-center mb-1 d-block text-dark fw-medium"
              href="javascript:void(0)"
              data-bs-toggle="modal"
              data-bs-target="#forgotPasswordModal">

              Forget Password?
            </a>

            <!-- Forgot Password Modal -->
            <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">

                  <div class="modal-header border-0" style="background-color: #57cb57;">
                    <h5 class="modal-title fw-bold text-dark">
                      Reset Password
                    </h5>

                    <button type="button"
                      class="btn-close"
                      data-bs-dismiss="modal"
                      aria-label="Close">
                    </button>
                  </div>

                  <!-- Form -->
                  <form action="/kmkdt-Library/app/controller/loginController.php" method="POST">

                    <div class="modal-body py-4">

                      <p class="text-muted mb-4">
                        Enter your registered email address to generate a reset link.
                      </p>

                      <div class="mb-3">
                        <input type="email"
                          name="emailAddress"
                          class="form-control border-bottom"
                          placeholder="Enter Email Address"
                          required>
                      </div>
                    </div>

                    <div class="modal-footer border-0">

                      <button type="button"
                        class="btn btn-light px-4"
                        data-bs-dismiss="modal">

                        Cancel
                      </button>

                      <button type="submit"
                        name="forgotPasswordButton"
                        class="btn btn-dark px-4">

                        Send Reset Link
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <p class="mb-0 fw-medium text-center">
              Not a member yet?
              <a class="text-dark" href="signUp">
                Sign Up
              </a>
            </p>

          </div>
        </div>
      </div>
    </section>
  </div>

  <div class="get-template hstack gap-2">
    <button
      class="btn bg-primary p-2 round-52 rounded-circle hstack justify-content-center flex-shrink-0"
      id="scrollToTopBtn">

      <iconify-icon icon="lucide:arrow-up" class="fs-7 text-dark"></iconify-icon>
    </button>
  </div>

  <script src="/kmkdt-Library/public/assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="/kmkdt-Library/public/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <?php
  if (isset($_SESSION['message']) && !empty($_SESSION['code'])) {
  ?>
    <script>
      const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,

        didOpen: (toast) => {
          toast.onmouseenter = Swal.stopTimer;
          toast.onmouseleave = Swal.resumeTimer;
        }
      });

      Toast.fire({
        icon: "<?php echo $_SESSION['code']; ?>",
        title: "<?php echo $_SESSION['message']; ?>"
      });
    </script>
  <?php
    unset($_SESSION['message']);
    unset($_SESSION['code']);
  }
  ?>

</body>

</html>