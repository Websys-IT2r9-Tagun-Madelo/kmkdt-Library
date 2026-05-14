<?php
require_once dirname(__DIR__, 2) . '/app/middleware/user_auth.php';

$user = getUserById($conn, $currentUserId);
$stats = getUserStats($conn, $currentUserId);

$fullName = $user['fullName'] ?? 'User';
$myBooks = getMyBooks($conn, $currentUserId);

include('./includes/header.php');
include('./includes/tsbar.php');
?>

<!-- Banner Section -->
<div class="page-wrapper overflow-hidden">
    <!-- Profile Header Section -->
    <section class="banner-section banner-inner-section position-relative overflow-hidden d-flex align-items-end"
        style="background-image: url('assets/images/backgrounds/ProfileBg.jpg'); min-height: 350px; background-size: cover;">
        <div class="container">
            <div class="d-flex flex-column gap-4 pb-5 pb-xl-10 position-relative z-1">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-4" data-aos="fade-up">
                            <div class="bg-primary d-flex align-items-center justify-content-center overflow-hidden border border-4 border-white shadow-sm"
                                style="width: 150px; height: 150px; flex-shrink: 0; border-radius: 15px;">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($fullName); ?>&background=57cb57&color=fff&size=150"
                                    alt="Profile" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div>
                                <h1 class="mb-1 text-white fw-bold" style="font-size: 2.5rem;">
                                    <?php echo htmlspecialchars($fullName); ?>
                                </h1>
                                <p class="mb-0 text-white text-opacity-75 fs-4">Member ID:
                                    #<?php echo $currentUserId; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="profile-content py-5">
        <div class="container">
            <!-- Left Sidebar -->
            <div class="row g-5">
                <div class="col-lg-4">
                    <div class="p-4 rounded-4 border bg-white shadow-sm">
                        <h4 class="border-bottom pb-3 mb-4">Library Overview</h4>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Borrowed</span>
                            <span class="fw-bold"
                                style="color: #57cb57;"><?php echo $stats['total_borrowed'] ?? 0; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Currently Holding</span>
                            <span class="fw-bold"
                                style="color: #57cb57;"><?php echo $stats['current_holdings'] ?? 0; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Returned</span>
                            <span class="fw-bold"
                                style="color: #57cb57;"><?php echo $stats['total_returned'] ?? 0; ?></span>
                        </div>
                        <hr>
                        
                        <button class="btn rounded-pill w-100 fw-bold text-dark" style="background-color: #57cb57;"
                            data-bs-toggle="modal" data-bs-target="#fullUpdateModal">
                            Update Account
                        </button>
                    </div>
                </div>

                
                <div class="col-lg-8">
                    <h2 class="mb-4">Current Borrowed Books</h2>
                    <?php if ($myBooks && $myBooks->num_rows > 0): ?>
                        <?php while ($book = $myBooks->fetch_assoc()): ?>
                            <div class="p-4 rounded-4 border-start border-5 mb-3 d-flex justify-content-between align-items-center shadow-sm bg-white"
                                style="border-color: #57cb57 !important;">
                                <div>
                                    <h4 class="mb-1"><?php echo htmlspecialchars($book['title']); ?></h4>
                                    <p class="mb-0 text-muted small">Category: <?php echo htmlspecialchars($book['genre']); ?>
                                    </p>
                                </div>
                                <a href="MBB" class="btn rounded-pill px-4 fw-bold"
                                    style="background-color: #57cb57; color: #000;">Go to MBB</a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="p-5 text-center border rounded-4 bg-light">
                            <p class="text-white-50 mb-0">No active book loans found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- FULL UPDATE MODAL -->
<div class="modal fade" id="fullUpdateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="/kmkdt-Library/app/controller/process/process_update.php" method="POST"
            class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="fw-bold">Edit Profile & Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Full Name</label>
                        <input type="text" name="fullName" class="form-control"
                            value="<?php echo htmlspecialchars($fullName); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Username</label>
                        <input type="text" name="username" class="form-control"
                            value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="emailAddress" class="form-control"
                            value="<?php echo htmlspecialchars($user['emailAddress'] ?? ''); ?>" required>
                    </div>

                    <div class="col-12 mt-4">
                        <p class="fw-bold mb-2 border-bottom">Address Details</p>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="street" class="form-control" placeholder="Street"
                            value="<?php echo htmlspecialchars($user['street'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="barangay" class="form-control" placeholder="Barangay"
                            value="<?php echo htmlspecialchars($user['barangay'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="city" class="form-control" placeholder="City"
                            value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                    </div>

                    <div class="col-12 mt-4">
                        <p class="fw-bold mb-2 border-bottom text-danger">Security</p>
                    </div>
                    <div class="col-md-6">
                        <input type="password" name="password" class="form-control" placeholder="New Password">
                    </div>
                    <div class="col-md-6">
                        <input type="password" name="confirmPassword" class="form-control"
                            placeholder="Confirm New Password">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="submit" class="btn btn-dark px-5 rounded-pill fw-bold"
                    style="background-color: #32cd32; border: none; color: black;">
                    Update Everything
                </button>
            </div>
        </form>
    </div>
</div>

<?php include('./includes/footer.php'); ?>