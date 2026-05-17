<?php
include('../../app/middleware/admin.php');

// Set root path constants
define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . '/app');

// Include core files
require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/controller/adminController.php';

// 2. Fetch Logged-in Admin Details Dynamically
$adminId = $_SESSION['user_id'] ?? null;
$adminData = [];

if ($adminId) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $adminId]);
    $adminData = $stmt->fetch() ?: [];
}

// Fallback values if database fields are missing or empty
$fullName = !empty($adminData['fullName']) ? htmlspecialchars($adminData['fullName']) : 'Admin User';
$email    = !empty($adminData['email']) ? htmlspecialchars($adminData['email']) : 'admin@example.com';
$phone    = !empty($adminData['phone']) ? htmlspecialchars($adminData['phone']) : 'N/A';
$address  = !empty($adminData['address']) ? htmlspecialchars($adminData['address']) : 'N/A';
$role     = !empty($adminData['role']) ? htmlspecialchars($adminData['role']) : 'Administrator';

include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

<div class="pagetitle">
  <h1>Profile</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="Index.php">Home</a></li>
      <li class="breadcrumb-item active">Profile</li>
    </ol>
  </nav>
</div>

<section class="section profile">
  <div class="row">
    <div class="col-xl-4">
      <div class="card">
        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
          <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle">
          <h2><?= $fullName ?></h2>
          <h3><?= $role ?></h3>
        </div>
      </div>
    </div>

    <div class="col-xl-8">
      <div class="card">
        <div class="card-body pt-3">
          <ul class="nav nav-tabs nav-tabs-bordered">
            <li class="nav-item">
              <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
            </li>
          </ul>

          <div class="tab-content pt-2">
            <div class="tab-pane fade show active profile-overview" id="profile-overview">
              <h5 class="card-title">Profile Details</h5>

              <div class="row mb-2">
                <div class="col-lg-3 col-md-4 label">Full Name</div>
                <div class="col-lg-9 col-md-8"><?= $fullName ?></div>
              </div>

              <div class="row mb-2">
                <div class="col-lg-3 col-md-4 label">Role</div>
                <div class="col-lg-9 col-md-8"><?= $role ?></div>
              </div>

              <div class="row mb-2">
                <div class="col-lg-3 col-md-4 label">Address</div>
                <div class="col-lg-9 col-md-8"><?= $address ?></div>
              </div>

              <div class="row mb-2">
                <div class="col-lg-3 col-md-4 label">Phone</div>
                <div class="col-lg-9 col-md-8"><?= $phone ?></div>
              </div>

              <div class="row mb-2">
                <div class="col-lg-3 col-md-4 label">Email</div>
                <div class="col-lg-9 col-md-8"><?= $email ?></div>
              </div>
            </div>

            <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
              <form action="../../app/controller/adminController.php" method="POST">
                <input type="hidden" name="action" value="updateProfile">
                
                <div class="row mb-3">
                  <label class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                  <div class="col-md-8 col-lg-9">
                    <img src="assets/img/profile-img.jpg" alt="Profile">
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="fullName" type="text" class="form-control" id="fullName" value="<?= $fullName ?>" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="Address" class="col-md-4 col-lg-3 col-form-label">Address</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="address" type="text" class="form-control" id="Address" value="<?= $address ?>">
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="phone" type="text" class="form-control" id="Phone" value="<?= $phone ?>">
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="email" type="email" class="form-control" id="Email" value="<?= $email ?>" required>
                  </div>
                </div>

                <div class="text-center">
                  <button type="submit" class="btn btn-success" style="background-color: #32cd32; border-color: #32cd32;">Save Changes</button>
                </div>
              </form>
            </div>

            <div class="tab-pane fade pt-3" id="profile-change-password">
              <form action="../../app/controller/adminController.php" method="POST">
                <input type="hidden" name="action" value="changePassword">

                <div class="row mb-3">
                  <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="password" type="password" class="form-control" id="currentPassword" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="newpassword" type="password" class="form-control" id="newPassword" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="renewpassword" type="password" class="form-control" id="renewPassword" required>
                  </div>
                </div>

                <div class="text-center">
                  <button type="submit" class="btn btn-success" style="background-color: #32cd32; border-color: #32cd32;">Change Password</button>
                </div>
              </form>
            </div>

          </div></div>
      </div>
    </div>
  </div>
</section>

<?php include('./includes/footer.php'); ?> 