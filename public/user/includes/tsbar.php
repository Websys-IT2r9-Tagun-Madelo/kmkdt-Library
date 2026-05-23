<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('KMKDT_USER_SESSION');
    session_set_cookie_params(0, '/'); 
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']); 


$eBookHref = "eBook"; 
if (isset($_SESSION['current_reading_id'])) {
    $eBookHref = "eBook?id=" . $_SESSION['current_reading_id'];
}
?>

<header class="header border-4 border-primary border-top position-fixed start-0 top-0 w-100">
  <div class="container">
    <div class="header-wrapper d-flex align-items-center justify-content-between">
      <div class="logo">
        <a></a>
      </div>

    <!-- notification bell -->
        <div class="d-flex align-items-center gap-3 pe-3">

            <div class="dropdown">
                <a href="#" class="position-relative d-flex align-items-center justify-content-center header-notification-link" 
                  id="userNotiDropdown" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                    
                    <i class="bi bi-bell-fill bell-outline-stroke"></i>
                    
                    <span id="userNotiBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none">
                        0
                    </span>
                </a>
                
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-3 p-0" aria-labelledby="userNotiDropdown" id="userNotiMenu">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center dropdown-header-bg">
                        <h6 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-lightning-charge-fill text-lime-accent"></i> Activity Updates
                        </h6>
                        <span class="badge text-uppercase px-2 py-1 rounded-pill live-feed-badge">
                            Live 
                        </span>
                    </div>
                    
                    <div id="userNotiContainer" class="custom-noti-scrollbar">
                        <div class="text-center py-5 text-muted small">
                            <i class="bi bi-bell-fill d-block text-secondary opacity-50 mb-2 empty-bell-icon"></i>
                            <span class="fw-medium">No current notifications</span>
                        </div>
                    </div>
                </ul>
            </div>
    
<!-- menu dropdown -->
      <div class="d-flex align-items-center gap-4">

        <div class="btn-group">
          <button
            class="btn btn-secondary toggle-menu round-45 p-2 d-flex align-items-center justify-content-center bg-white rounded-circle"
            type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
            <iconify-icon icon="solar:hamburger-menu-line-duotone" class="menu-icon fs-8 text-dark"></iconify-icon>
          </button>

          <ul class="dropdown-menu dropdown-menu-end p-4">
            <div class="d-flex flex-column gap-6">
              <div class="hstack justify-content-between border-bottom pb-6">
                <p class="mb-0 fs-5 text-dark"> Menu </p>
                <button type="button" class="btn-close opacity-75" aria-label="Close"></button>
              </div>

              <div class="d-flex flex-column gap-3">
                <ul class="header-menu list-unstyled mb-0 d-flex flex-column gap-2">


                  <li class="header-item">
                    <a href="index" class="header-link  hstack gap-2 fs-7 fw-bold text-dark"><img
                        src="../assets/images/svgs/secondary-leaf.svg" alt="" width="20" height="20"
                        class="img-fluid animate-spin"> Home</a>
                  </li>

                  <li class="header-item">
                    <a href="profile" class="header-link hstack gap-2 fs-7 fw-bold text-dark"><img
                        src="../assets/images/svgs/secondary-leaf.svg" alt="" width="20" height="20"
                        class="img-fluid animate-spin"> My Profile </a>
                  </li>
                  <li class="header-item">
                    <a href="myBooks" class="header-link hstack gap-2 fs-7 fw-bold text-dark"><img
                        src="../assets/images/svgs/secondary-leaf.svg" alt="" width="20" height="20"
                        class="img-fluid animate-spin"> My Borrowed Books</a>
                  </li>
                  <li class="header-item">
                    <a href="<?= $eBookHref; ?>" class="header-link hstack gap-2 fs-7 fw-bold text-dark"><img
                        src="../assets/images/svgs/secondary-leaf.svg" alt="" width="20" height="20"
                        class="img-fluid animate-spin"> My E-Books</a>
                  </li>
                  <li class="header-item">
                    <a href="browseBooks" class="header-link hstack gap-2 fs-7 fw-bold text-dark"><img
                        src="../assets/images/svgs/secondary-leaf.svg" alt="" width="20" height="20"
                        class="img-fluid animate-spin"> Browse Books</a>
                  </li>
                  <li class="header-item">
                    <a href="messageHub" class="header-link hstack gap-2 fs-7 fw-bold text-dark"><img
                        src="../assets/images/svgs/secondary-leaf.svg" alt="" width="20" height="20"
                        class="img-fluid animate-spin"> Message Hub </a>
                  </li>
                  <li class="header-item">
                    <form action="/kmkdt-Library/app/controller/userController.php" method="POST">
                      <button type="submit" name="logoutButton"
                        class="btn btn-dark text-white fs-6 bg-dark px-3 py-2 w-100 hstack justify-content-center border-0">
                        <span>Log Out</span>
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
            </div>
        </div>
        </ul>
        </div>
       </div>
      </div>
    </div>
  </div>
</div>

</header>