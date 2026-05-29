<?php
require_once dirname(__DIR__, 2) . '/app/middleware/userAuth.php'; 
require_once dirname(__DIR__, 2) . '/app/config/config.php'; 

$myBooks = getMyBooks($conn, $currentUserId); 
$user = getUserById($conn, $currentUserId);
$stats = getUserStats($conn, $currentUserId);

$fullName = $user['fullName'] ?? 'User';

include('./includes/header.php');
include('./includes/tsbar.php');
?>

<div class="page-wrapper overflow-hidden">
    <?php if (isset($_GET['status']) && $_GET['status'] == 'paid'): ?>
        <div class="container mt-3">
            <div class="alert alert-success border-0 shadow-sm text-white fw-bold d-flex align-items-center alert-success-custom">
                <iconify-icon icon="lucide:check-circle" class="fs-4 me-2"></iconify-icon>
                Payment Received! Your book penalty has been cleared.
            </div>
        </div>
    <?php elseif (isset($_GET['status']) && $_GET['status'] == 'payment_submitted'): ?>
        <div class="container mt-3">
            <div class="alert alert-warning border-0 shadow-sm text-dark fw-bold d-flex align-items-center alert-warning-custom" style="background-color: #fff3cd; border-left: 5px solid #ffc107 !important;">
                <iconify-icon icon="lucide:clock" class="fs-4 me-2 text-warning"></iconify-icon>
                Payment Request Submitted! Awaiting admin verification confirmation.
            </div>
        </div>
    <?php endif; ?>

    <section class="banner-section banner-inner-section position-relative overflow-hidden d-flex align-items-end profile-banner">
        <div class="container">
            <div class="d-flex flex-column gap-4 pb-5 pb-xl-10 position-relative z-1">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-4" data-aos="fade-up">
                            <div class="bg-primary d-flex align-items-center justify-content-center overflow-hidden border border-4 border-white shadow-sm avatar-container">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($fullName); ?>&background=57cb57&color=fff&size=150" alt="Profile" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div>
                                <h1 class="mb-1 text-white fw-bold profile-name"><?php echo htmlspecialchars($fullName); ?></h1>
                                <p class="mb-0 text-white text-opacity-75 fs-4">Member ID: #<?php echo $currentUserId; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="profile-content pt-5 pb-5">
        <div class="container">
            <div class="row g-4">
                
                <!-- LEFT SIDEBAR: Overview & Payment Logs -->
                <div class="col-lg-4">
                    <!-- Library Overview Widget -->
                    <div class="p-4 rounded-4 border bg-white shadow-sm overview-card">
                        <h4 class="border-bottom pb-3 mb-4 fw-bold">Library Overview</h4>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Borrowed</span>
                            <span class="fw-bold total-borrowed-val"><?php echo $stats['total_borrowed'] ?? 0; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Currently Holding</span>
                            <span class="fw-bold current-holdings-val"><?php echo $stats['current_holdings'] ?? 0; ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Total Returned</span>
                            <span class="fw-bold total-returned-val"><?php echo $stats['total_returned'] ?? 0; ?></span>
                        </div>
                        <hr>
                        <button class="btn rounded-pill w-100 fw-bold text-dark btn-update-account" data-bs-toggle="modal" data-bs-target="#fullUpdateModal">
                            Update Account
                        </button>
                    </div>

                    
                    <div class="card border-0 shadow-sm rounded-4 p-4 payment-history-card mt-4 bg-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-2 bg-light rounded-3 me-3 text-success d-flex align-items-center justify-content-center history-icon-box" style="width: 40px; height: 40px;">
                                <iconify-icon icon="lucide:history" class="fs-5"></iconify-icon>
                            </div>
                            <div>
                                <h6 class="fw-bold m-0 text-dark history-header-title">Payment History</h6>
                                <p class="text-muted m-0 extra-small" style="font-size: 0.75rem;">Settled library penalties</p>
                            </div>
                        </div>

                        <div class="history-list-wrapper mt-2" style="max-height: 320px; overflow-y: auto;">
                            <?php 
                            $payments = getPaymentHistory($conn, $currentUserId);
                            if ($payments && $payments->num_rows > 0): 
                                while ($pay = $payments->fetch_assoc()): 
                            ?>
                                <div class="d-flex justify-content-between align-items-center py-2.5 border-bottom last-border-0">
                                    <div class="flex-grow-1 min-w-0 pe-2">
                                        <div class="fw-semibold text-dark text-truncate small mb-0">
                                            <?= htmlspecialchars($pay['title']); ?>
                                        </div>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">
                                            <?= date('M d, Y | g:i A', strtotime($pay['paid_at'])); ?>
                                        </small>
                                        <span class="text-success d-inline-flex align-items-center gap-1 fw-medium" style="font-size: 0.68rem; letter-spacing: 0.3px;">
                                            <iconify-icon icon="lucide:check-circle-2" class="text-success" style="font-size: 0.75rem;"></iconify-icon> Penalty Cleared
                                        </span>
                                    </div>
                                    <div class="text-end text-nowrap">
                                        <span class="text-success fw-bold settled-amount-text px-2 py-1.5" style="font-size: 0.8rem;">
                                            ₱<?= number_format($pay['amount_paid'], 2); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php 
                                endwhile; 
                            else: 
                            ?>
                                <div class="d-flex flex-column align-items-center justify-content-center py-5 my-3 text-muted">
                                    <iconify-icon icon="lucide:folder-open" class="mb-2 fs-3 opacity-50"></iconify-icon>
                                    <span class="small fw-medium text-secondary">No records found</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- RIGHT CONTENT PANEL: Borrowed Books Feed -->
                <div class="col-lg-8">
                    <div class="borrowed-books-section">
                        <h3 class="mb-4 fw-bold borrowed-books-title">My Borrowed Books</h3>
                        <?php if ($myBooks && $myBooks->num_rows > 0): ?>
                            <?php while ($book = $myBooks->fetch_assoc()): ?>
                                <div class="p-4 rounded-4 border-start border-5 mb-3 d-flex justify-content-between align-items-center shadow-sm bg-white book-loan-card">
                                    <div class="min-w-0 flex-grow-1 me-3">
                                        <h4 class="mb-2 fw-bold text-dark text-truncate h5"><?php echo htmlspecialchars($book['title']); ?></h4>
                                        <div class="mb-2 d-flex gap-1 align-items-center flex-wrap">
                                            <span class="badge rounded-pill text-uppercase category-badge"><?= htmlspecialchars($book['category'] ?? 'General'); ?></span>
                                            <span class="badge rounded-pill text-uppercase genre-badge"><?= htmlspecialchars($book['genre'] ?? 'General'); ?></span>
                                        </div>
                                        <p class="text-muted mb-0 small due-date-text">Due: <?= $book['due_date'] ?? 'N/A'; ?></p>
                                    </div>
                                    <div class="d-flex gap-2 align-items-center flex-shrink-0">
                                        <?php if(isset($book['penalty']) && $book['penalty'] > 0): ?>
                                            <?php if(isset($book['status']) && $book['status'] === 'payment_pending'): ?>
                                                <!-- Awaiting Admin action passive state display button replacement -->
                                                <button class="btn btn-secondary rounded-pill fw-bold px-3 btn-pay-penalty disabled" disabled style="background-color: #6c757d; border: none; font-size: 0.85rem;">
                                                    <iconify-icon icon="lucide:loader" class="me-1 align-middle"></iconify-icon> Pending Verification
                                                </button>
                                            <?php else: ?>
                                                <!-- Active trigger modal pay penalty state button -->
                                                <button class="btn btn-success rounded-pill fw-bold px-4 btn-pay-penalty" data-bs-toggle="modal" data-bs-target="#paymentModal<?= $book['loan_id']; ?>">
                                                    Pay ₱<?= number_format($book['penalty'], 2); ?>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <a href="myBooks" class="btn rounded-pill px-4 fw-bold shadow-sm btn-go-books">Go to My Books</a>
                                    </div>
                                </div>

                                <!-- Penalty Handling Modal Window Setup Container -->
                                <div class="modal fade" id="paymentModal<?= $book['loan_id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content penalty-modal-content">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title fw-bold">Confirm Penalty Clearance</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center py-4">
                                                <h2 class="fw-bold text-dark penalty-amount-display">₱<?= number_format($book['penalty'] ?? 0, 2); ?></h2>
                                                <p class="text-muted mb-4">You are declaring that you have settled this fine.<br><strong>"<?= htmlspecialchars($book['title']); ?>"</strong></p>
                                                
                                                <!-- Action form targeting the user process path -->
                                                <form action="../../app/controller/process/paymentProcess.php" method="POST">
                                                    <input type="hidden" name="loan_id" value="<?= $book['loan_id']; ?>"> 
                                                    <button type="submit" class="btn w-100 rounded-pill fw-bold py-2.5 text-white shadow-sm btn-confirm-payment" style="background-color: #57cb57; border: none;">
                                                        Submit Verification Request
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="p-5 text-center border rounded-4 shadow-sm bg-white empty-loans-container">
                                <iconify-icon icon="lucide:book-check" class="mb-2 fs-2 text-muted"></iconify-icon>
                                <p class="m-0 fw-medium">No active book loans found.</p> 
                                <a href="browseBooks" class="text-primary fw-bold text-decoration-none d-inline-flex align-items-center gap-2 mt-2">Browse the Library</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div> <!-- /col-lg-8 -->

            </div> <!-- /row -->
        </div> 
    </section>

    <!-- Account Details Modal Window -->
    <div class="modal fade" id="fullUpdateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form action="/kmkdt-Library/app/controller/process/updateProcess.php" method="POST" class="modal-content rounded-4 border-0 update-profile-modal" onsubmit="return confirm('Are you sure you want to update your profile? This will modify your current account details.');">
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="fw-bold">Edit Profile & Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" name="fullName" class="form-control fst-italic" value="<?php echo $_SESSION['authUser']['fullName'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Username</label>
                            <input type="text" name="username" class="form-control fst-italic" value="<?php echo $_SESSION['authUser']['username'] ?? ''; ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Email Address</label>
                            <input type="email" name="emailAddress" class="form-control fst-italic" value="<?php echo $_SESSION['authUser']['emailAddress'] ?? ''; ?>" required>
                        </div>
                        <div class="col-12 mt-4">
                            <p class="fw-bold mb-2 border-bottom">Address Details</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Street / House No.</label>
                            <input type="text" name="street" class="form-control fst-italic" value="<?php echo htmlspecialchars($user['street'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Barangay</label>
                            <input type="text" name="barangay" class="form-control fst-italic" value="<?php echo htmlspecialchars($user['barangay'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4">  
                            <label class="form-label small fw-bold">City / Municipality</label>
                            <input type="text" name="city" class="form-control fst-italic" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                        </div>
                        <div class="col-12 mt-4">
                            <p class="fw-bold mb-2 border-bottom text-danger">Security</p>
                        </div>
                        <div class="col-md-6">
                            <input type="password" name="password" class="form-control" placeholder="New Password">
                        </div>
                        <div class="col-md-6">
                            <input type="password" name="confirmPassword" class="form-control" placeholder="Confirm New Password">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4">
                    <button type="submit" class="btn btn-dark px-5 rounded-pill fw-bold text-white btn-submit-update">Update Everything</button>
                </div>
            </form>
        </div>
    </div>  

</div>

<?php include('./includes/footer.php'); ?>