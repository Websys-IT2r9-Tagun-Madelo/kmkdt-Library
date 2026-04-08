<?php 
session_start(); 
function old($key) { return $_SESSION['old_input'][$key] ?? ''; } 
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>kmkdt Library | Sign Up</title>
  <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%2397ee5b'><path d='M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z'/></svg>">
  <link rel="stylesheet" href="/kmkdt-Library/public/assets/libs/owl.carousel/dist/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="/kmkdt-Library/public/assets/libs/aos-master/dist/aos.css">
  <link rel="stylesheet" href="/kmkdt-Library/public/assets/css/styles.css" />
</head>
<body>
  <div class="page-wrapper overflow-hidden">
    <section class="bg-light-gray border-top border-4 d-flex align-items-center justify-content-center min-vh-100" style="border-top-color: #97ee5b !important;">
      <div class="container py-3">
        <div class="sign-in card mx-auto shadow-lg">
          <div class="card-body py-8 px-lg-5">
            <a href="index" class="mb-8 hstack justify-content-center text-decoration-none" style="font-size: 4.5rem; letter-spacing: -2px;">
              <span class="fw-light" style="color: #2A3547; text-transform: lowercase;">kmkdt</span>
              <span style="font-weight: 900; color: #97ee5b; margin-left: 10px; text-transform: lowercase;">library</span>
            </a>

            <div class="hstack gap-3">
              <a href="javascript:void(0)" class="btn btn-outline-light bg-white px-3 py-2 fs-4 text-dark w-50 fw-medium hstack gap-2 lh-lg justify-content-center">
                Sign Up <img src="/kmkdt-Library/public/assets/images/svgs/icon-google.svg" alt="google" class="img-fluid">
              </a>
              <a href="javascript:void(0)" class="btn btn-outline-light bg-white px-3 py-2 fs-4 text-dark w-50 fw-medium hstack gap-2 lh-lg justify-content-center">
                Sign Up <img src="/kmkdt-Library/public/assets/images/svgs/icon-github.svg" alt="github" class="img-fluid">
              </a>
            </div>

            <div class="position-relative hstack justify-content-center">
              <hr class="my-8 w-100 d-block">
              <p class="mb-0 fs-3 bg-body px-3 position-absolute top-50 start-50 translate-middle">OR</p>
            </div>

            <form id="signUpForm" class="d-flex flex-column gap-3 needs-validation" 
                  action="/kmkdt-Library/app/controller/loginController.php" 
                  method="POST" 
                  autocomplete="off" 
                  novalidate>
              
              <p class="text-center fs-3 mb-0 mt-3 text-uppercase fw-bold" style="letter-spacing: 1px; color: #2A3547;">Profile Information</p>
              
              <p class="text-muted fs-2 mb-0 mt-2 text-uppercase fw-bold">Full Name</p>  
              <div>
                  <input type="text" name="fullName" class="form-control border-bottom" placeholder="Monkey D. Luffy" value="<?php echo old('fullName'); ?>" required>
                  <div class="invalid-feedback">Please enter your full name.</div>
              </div>
              
              <p class="text-muted fs-2 mb-0 mt-2 text-uppercase fw-bold">Email Address</p>  
              <div>
                <input type="email" name="emailAddress" class="form-control border-bottom" placeholder="PirateKingLuffy@gmail.com" value="<?php echo old('emailAddress'); ?>" required>
                <div class="invalid-feedback">Please enter a valid email address.</div>
              </div>

              <p class="text-center fs-3 mb-0 mt-3 text-uppercase fw-bold" style="letter-spacing: 1px; color: #2A3547;">Account Setup</p>

              <p class="text-muted fs-2 mb-0 mt-2 text-uppercase fw-bold">Username</p>  
              <div>
                  <input type="text" name="username" class="form-control border-bottom" placeholder="Pirateking55" value="<?php echo old('username'); ?>" required>
                  <div class="invalid-feedback">Please enter a username.</div>
              </div>

              <p class="text-muted fs-2 mb-0 mt-2 text-uppercase fw-bold">Password</p>  
              <div class="row g-3">
                <div class="col-md-6">
                  <input type="password" name="password" class="form-control border-bottom" id="inputPassword" placeholder="Set Password" required>
                </div>
                <div class="col-md-6">
                  <input type="password" name="confirmPassword" class="form-control border-bottom" id="confirmPassword" placeholder="Confirm Password" required>
                </div>
                <div id="pwMismatch" class="text-danger fs-2" style="display:none;">Passwords do not match.</div>
              </div>

              <p class="text-center fs-3 mb-0 mt-3 text-uppercase fw-bold" style="letter-spacing: 1px; color: #2A3547;">Address Details</p>

              <p class="text-muted fs-2 mb-0 mt-3 text-uppercase fw-bold">Street</p>
              <div>
                  <input type="text" name="street" class="form-control border-bottom" placeholder="123 Rizal St., Apt 2B" value="<?php echo old('street'); ?>" required>
                  <div class="invalid-feedback">Please enter your street address.</div>
              </div>

              <div class="row g-3">
                  <div class="col-md-6">
                      <p class="text-muted fs-2 mb-0 mt-2 text-uppercase fw-bold">Barangay</p>
                      <input type="text" name="barangay" class="form-control border-bottom" placeholder="Brgy. Obrero" value="<?php echo old('barangay'); ?>" required>
                  </div>
                  <div class="col-md-6">
                      <p class="text-muted fs-2 mb-0 mt-2 text-uppercase fw-bold">City</p>
                      <input type="text" name="city" class="form-control border-bottom" placeholder="Davao City" value="<?php echo old('city'); ?>" required>
                  </div>
              </div>

              <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" name="terms_agreed" id="termsCheck" required>
                <label class="form-check-label fs-4 text-dark" for="termsCheck">
                  I agree and accept the <a class="text-dark fw-bold" href="privacy-policy">Terms & Policy</a>
                </label>
              </div>
          
                <div class="d-flex flex-column align-items-center w-100"> <button type="submit" name="registerbutton" class="btn btn-dark w-100 d-flex align-items-center justify-content-center py-3 fw-bold mt-3 fs-4 gap-0"><span>Sign Up</span><iconify-icon icon="lucide:arrow-right" class="fs-5"></iconify-icon></button></div>
            </form>

            <p class="mt-4 fw-medium text-center">Already have an account? <a class="text-dark" href="login">Log In</a></p>
          </div>
        </div>
      </div>
    </section>
  </div>

  <script src="/kmkdt-Library/public/assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="/kmkdt-Library/public/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    document.getElementById('signUpForm').addEventListener('submit', function(e) {
      const pass = document.getElementById('inputPassword').value;
      const confirm = document.getElementById('confirmPassword').value;
      const mismatch = document.getElementById('pwMismatch');
      
      if (pass !== confirm) {
        e.preventDefault();
        mismatch.style.display = 'block';
        document.getElementById('confirmPassword').classList.add('is-invalid');
      } else {
        mismatch.style.display = 'none';
      }
    });
  </script>

  <?php if (isset($_SESSION['message'])): ?>
    <script>
      Swal.mixin({
        toast: true, position: "top-end", showConfirmButton: false, timer: 3000, timerProgressBar: true
      }).fire({
        icon: "<?php echo $_SESSION['code']; ?>",
        title: "<?php echo $_SESSION['message']; ?>"
      });
    </script>
    <?php unset($_SESSION['message'], $_SESSION['code'], $_SESSION['old_input']); ?>
  <?php endif; ?>
</body>
</html>