<?php
include('../../app/middleware/admin.php');

define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . '/app');


require_once APP_PATH . '/config/config.php';
require_once APP_PATH . '/controller/adminController.php';

$adminId = $_SESSION['user_id'] ?? $_SESSION['authUser']['id'] ?? $_SESSION['authUser']['user_id'] ?? null;
$profile = getProcessedAdminProfile($conn, $adminId);

$fullName = $profile['fullName'];
$email    = $profile['emailAddress'];
$role     = $profile['role'];
$address  = $profile['address'];
$adminData = $profile['raw'];

include('./includes/header.php');
include('./includes/topbar.php');
include('./includes/sidebar.php');
?>

<div class="pagetitle">
  <h1>Profile</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item">Admin Info</li>
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
                        <img src="assets/img/admin.jpg" alt="Profile" class="rounded-circle img-fluid mb-3 profile-avatar-img">
                        <h4 class="fw-bold mb-1 text-dark"><?php echo $fullName; ?></h4>
                        <span class="badge text-uppercase mb-3 profile-role-badge">
                            <?php echo $role; ?>
                        </span>
                    </div>

                    <h4 class="border-bottom pb-3 mb-4 fw-bold text-dark profile-heading-title">Profile Mapping</h4>
                    
                    <div class="d-flex justify-content-between mb-3 small">
                        <span class="text-muted">Email Address</span>
                        <span class="fw-bold text-dark text-break ps-2"><?php echo $email; ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4 small">
                        <span class="text-muted">Primary Address</span>
                        <span class="fw-bold text-dark text-end text-break ps-2"><?php echo $address; ?></span>
                    </div>
                    
                    <hr class="text-muted opacity-25">
                    
                    <button class="btn btn-update-account rounded-pill w-100 fw-bold text-dark shadow-sm" 
                        data-bs-toggle="modal" data-bs-target="#fullUpdateModal">
                        Update Account
                    </button>
                </div>
            </div>

            <div class="col-lg-8">
                <h2 class="mb-4 fw-bold profile-snapshot-title">System Profile Snapshot</h2>
                
                <div class="p-4 rounded-4 border-start border-5 mb-3 d-flex flex-column gap-3 shadow-sm bg-white profile-snapshot-card">
                    
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                        <div>
                            <span class="text-muted d-block small uppercase fw-bold profile-identifier-label">SYSTEM IDENTIFIER</span>
                            <h5 class="mb-0 fw-bold text-dark">#<?php echo htmlspecialchars($adminId ?? 'UNKNOWN'); ?></h5>
                        </div>
                        <span class="badge rounded-pill text-uppercase profile-key-badge">
                            Account Key
                        </span>
                    </div>

                    <div class="row pt-1">
                        <div class="col-sm-4 text-muted fw-medium">Full Name</div>
                        <div class="col-sm-8 fw-bold text-dark"><?php echo $fullName; ?></div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 text-muted fw-medium">Assigned System Role</div>
                        <div class="col-sm-8 fw-bold text-dark text-uppercase profile-role-text"><?php echo $role; ?></div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 text-muted fw-medium">Active Contact Email</div>
                        <div class="col-sm-8 fw-bold text-dark"><?php echo $email; ?></div>
                    </div>

                    <div class="row pb-1">
                        <div class="col-sm-4 text-muted fw-medium">Primary Address Mapping</div>
                        <div class="col-sm-8 fw-bold text-dark"><?php echo $address; ?></div>
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
                            value="<?php echo $fullName; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Username</label>
                        <input type="text" name="username" class="form-control fst-italic"
                            value="<?php echo htmlspecialchars($adminData['username'] ?? ''); ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="emailAddress" class="form-control fst-italic"
                            value="<?php echo $email; ?>" required>
                    </div>
                    
                    <div class="col-12 mt-4">
                        <p class="fw-bold mb-2 border-bottom">Address Components</p>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Street</label>
                        <input type="text" name="street" class="form-control fst-italic" 
                            value="<?php echo htmlspecialchars($adminData['street'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Barangay</label>
                        <input type="text" name="barangay" class="form-control fst-italic" 
                            value="<?php echo htmlspecialchars($adminData['barangay'] ?? ''); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">City</label>
                        <input type="text" name="city" class="form-control fst-italic" 
                            value="<?php echo htmlspecialchars($adminData['city'] ?? ''); ?>">
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
                <button type="submit" class="btn btn-dark btn-submit-profile px-5 rounded-pill fw-bold text-white">
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
                icon: '<?php echo $_SESSION['code']; ?>', 
                title: '<?php echo $_SESSION['code'] === 'success' ? 'Completed!' : 'Error Encountered'; ?>',
                text: '<?php echo addslashes($_SESSION['message']); ?>',
                confirmButtonColor: '<?php echo $_SESSION['code'] === 'success' ? '#32cd32' : '#dc3545'; ?>', 
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