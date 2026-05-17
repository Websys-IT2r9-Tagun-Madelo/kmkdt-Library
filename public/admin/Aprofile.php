<?php
// CRITICAL FIX: Initialize session parameters if not already active to read logged-in tracking keys
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(0, '/'); 
    session_start();
}

include('../../app/middleware/admin.php');

// Set root path constants
define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . '/app');

// Include core files
require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/controller/adminController.php';

// 2. Fetch Logged-in Admin Details Dynamically using Parameterized MySQLi
$adminId = $_SESSION['user_id'] ?? $_SESSION['authUser']['id'] ?? $_SESSION['authUser']['user_id'] ?? null;
$adminData = [];

if ($adminId) {
    // Using a parameterized mysqli prepared statement to keep your database secure
    $stmt = mysqli_prepare($conn, "SELECT * FROM user WHERE id = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $adminId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $adminData = mysqli_fetch_assoc($result) ?: [];
        mysqli_stmt_close($stmt);
    }
}

// Fallback values synchronized perfectly with your specific CREATE TABLE schema structural names
$fullName = !empty($adminData['fullName']) ? htmlspecialchars($adminData['fullName']) : 'Admin User';
$email    = !empty($adminData['emailAddress']) ? htmlspecialchars($adminData['emailAddress']) : 'admin@example.com';
$role     = !empty($adminData['role']) ? htmlspecialchars($adminData['role']) : 'Administrator';

// Reconstruct your structured address segments back into a single clean display string
$street   = !empty($adminData['street']) ? htmlspecialchars($adminData['street']) : '';
$barangay = !empty($adminData['barangay']) ? htmlspecialchars($adminData['barangay']) : '';
$city     = !empty($adminData['city']) ? htmlspecialchars($adminData['city']) : '';

$addressArray = array_filter([$street, $barangay, $city]);
$address  = !empty($addressArray) ? implode(', ', $addressArray) : 'N/A';

include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="pagetitle">
  <h1>Profile</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item">Admin Hub</li>
      <li class="breadcrumb-item active">Profile</li>
    </ol>
  </nav>
</div>

<section class="profile-content py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <div class="p-4 rounded-4 border bg-white shadow-sm">
                    <div class="text-center mb-4">
                        <img src="assets/img/admin.jpg" alt="Profile" class="rounded-circle img-fluid mb-3" style="max-width: 120px; border: 3px solid #22c55e;">
                        <h4 class="fw-bold mb-1 text-dark"><?= $fullName ?></h4>
                        <span class="badge text-uppercase mb-3" style="background-color: rgba(50, 205, 50, 0.1); color: #57cb57; font-size: 0.75rem; font-weight: 700; padding: 6px 12px;">
                            <?= $role ?>
                        </span>
                    </div>

                    <h4 class="border-bottom pb-3 mb-4 fw-bold text-dark" style="font-size: 1.25rem;">Profile Mapping</h4>
                    
                    <div class="d-flex justify-content-between mb-3 small">
                        <span class="text-muted">Email Address</span>
                        <span class="fw-bold text-dark text-break ps-2"><?= $email ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4 small">
                        <span class="text-muted">Primary Address</span>
                        <span class="fw-bold text-dark text-end text-break ps-2"><?= $address ?></span>
                    </div>
                    
                    <hr class="text-muted opacity-25">
                    
                    <button class="btn rounded-pill w-100 fw-bold text-dark shadow-sm" style="background-color: #57cb57; border: none;"
                        data-bs-toggle="modal" data-bs-target="#fullUpdateModal">
                        Update Account
                    </button>
                </div>
            </div>

            <div class="col-lg-8">
                <h2 class="mb-4 fw-bold" style="color: #22c55e;">System Profile Snapshot</h2>
                
                <div class="p-4 rounded-4 border-start border-5 mb-3 d-flex flex-column gap-3 shadow-sm bg-white"
                    style="border-color: #22c55e !important;">
                    
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <div>
                            <span class="text-muted d-block small uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">SYSTEM IDENTIFIER</span>
                            <h5 class="mb-0 fw-bold text-dark">#<?= htmlspecialchars($adminId ?? 'UNKNOWN') ?></h5>
                        </div>
                        <span class="badge rounded-pill text-uppercase"
                            style="background-color: rgba(50, 205, 50, 0.1); color: #57cb57; font-size: 0.7rem; font-weight: 700; padding: 4px 10px;">
                            Account Key
                        </span>
                    </div>

                    <div class="row pt-1">
                        <div class="col-sm-4 text-muted fw-medium">Full Name</div>
                        <div class="col-sm-8 fw-bold text-dark"><?= $fullName ?></div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 text-muted fw-medium">Assigned System Role</div>
                        <div class="col-sm-8 fw-bold text-dark text-uppercase" style="letter-spacing: 0.3px;"><?= $role ?></div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 text-muted fw-medium">Active Contact Email</div>
                        <div class="col-sm-8 fw-bold text-dark"><?= $email ?></div>
                    </div>

                    <div class="row pb-1">
                        <div class="col-sm-4 text-muted fw-medium">Primary Address Mapping</div>
                        <div class="col-sm-8 fw-bold text-dark"><?= $address ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="fullUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="/kmkdt-Library/app/controller/process/adminUpdateProcess.php" method="POST"
            class="modal-content rounded-4 border-0" 
            onsubmit="return confirm('Are you sure you want to update your profile? This will modify your current administrative account details.');">
            
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold">Edit Profile & Address Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4">
                <input type="hidden" name="action" value="updateProfileEverything">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Full Name</label>
                        <input type="text" name="fullName" class="form-control fst-italic"
                            value="<?= $fullName ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Username</label>
                        <input type="text" name="username" class="form-control fst-italic"
                            value="<?= htmlspecialchars($adminData['username'] ?? '') ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="emailAddress" class="form-control fst-italic"
                            value="<?= $email ?>" required>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <p class="fw-bold mb-2 border-bottom">Address Components</p>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Street</label>
                        <input type="text" name="street" class="form-control fst-italic" 
                            value="<?= htmlspecialchars($adminData['street'] ?? '') ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Barangay</label>
                        <input type="text" name="barangay" class="form-control fst-italic" 
                            value="<?= htmlspecialchars($adminData['barangay'] ?? '') ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">City</label>
                        <input type="text" name="city" class="form-control fst-italic" 
                            value="<?= htmlspecialchars($adminData['city'] ?? '') ?>">
                    </div>

                    <div class="col-12 mt-4">
                        <p class="fw-bold mb-2 border-bottom text-danger">Security Verification</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">New Password (Leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" placeholder="New Password">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-muted">Confirm New Password</label>
                        <input type="password" name="confirmPassword" class="form-control" placeholder="Confirm New Password">
                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-0 p-4">
                <button type="submit" class="btn btn-dark px-5 rounded-pill fw-bold text-white"
                    style="background-color: #22c55e; border: none;">
                    Update Everything
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_SESSION['message']) && isset($_SESSION['code'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: '<?= $_SESSION['code']; ?>', 
                title: '<?= $_SESSION['code'] === 'success' ? 'Completed!' : 'Error Encountered'; ?>',
                text: '<?= addslashes($_SESSION['message']); ?>',
                confirmButtonColor: '<?= $_SESSION['code'] === 'success' ? '#32cd32' : '#dc3545'; ?>', 
                timer: 3200,
                timerProgressBar: true
            });
        });
    </script>
    <?php 
    unset($_SESSION['message']); 
    unset($_SESSION['code']); 
    ?>
<?php endif; ?>

<?php include('./includes/footer.php'); ?>