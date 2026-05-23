<?php
require_once dirname(__DIR__, 2) . '/app/middleware/userAuth.php'; 
require_once dirname(__DIR__, 2) . '/app/config/config.php'; 

// These functions will now be defined and run correctly
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
    <?php endif; ?>

    <section class="banner-section banner-inner-section position-relative overflow-hidden d-flex align-items-end profile-banner">
        <div class="container">
            <div class="d-flex flex-column gap-4 pb-5 pb-xl-10 position-relative z-1">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-4" data-aos="fade-up">
                            <div class="bg-primary d-flex align-items-center justify-content-center overflow-hidden border border-4 border-white shadow-sm avatar-container">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($fullName); ?>&background=57cb57&color=fff&size=150"
                                     alt="Profile" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div>
                                <h1 class="mb-1 text-white fw-bold profile-name">
                                    <?php echo htmlspecialchars($fullName); ?>
                                </h1>
                                <p class="mb-0 text-white text-opacity-75 fs-4">Member ID: #<?php echo $currentUserId; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="profile-content py-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-lg-4">
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
                        <button class="btn rounded-pill w-100 fw-bold text-dark btn-update-account"
                                data-bs-toggle="modal" data-bs-target="#fullUpdateModal">
                            Update Account
                        </button>
                    </div>
                </div>

                <div class="col-lg-8">
                    <h2 class="mb-4 fw-bold borrowed-books-title">My Borrowed Books</h2>
                    <?php if ($myBooks && $myBooks->num_rows > 0): ?>
                        <?php while ($book = $myBooks->fetch_assoc()): ?>
                            <div class="p-4 rounded-4 border-start border-5 mb-3 d-flex justify-content-between align-items-center shadow-sm bg-white book-loan-card">
                                <div>
                                    <h4 class="mb-2 fw-bold text-dark"><?php echo htmlspecialchars($book['title']); ?></h4>
                                    
                                    <div class="mb-2 d-flex gap-1 align-items-center">
                                        <span class="badge rounded-pill text-uppercase category-badge">
                                            <?= htmlspecialchars($book['category'] ?? 'General'); ?>
                                        </span>
                                        
                                        <span class="badge rounded-pill text-uppercase genre-badge">
                                            <?= htmlspecialchars($book['genre'] ?? 'General'); ?>
                                        </span>
                                    </div>
                                    
                                    <p class="text-muted mb-0 small due-date-text">
                                        Due: <?= $book['due_date'] ?? 'N/A'; ?>
                                    </p>
                                </div>
                                
                                <div class="d-flex gap-2 align-items-center">
                                    <?php if(isset($book['penalty']) && $book['penalty'] > 0): ?>
                                        <button class="btn btn-success rounded-pill fw-bold px-4 btn-pay-penalty" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#paymentModal<?= $book['loan_id']; ?>">
                                            Pay ₱<?= number_format($book['penalty'], 2); ?>
                                        </button>
                                    <?php endif; ?>

                                    <a href="myBooks" class="btn rounded-pill px-4 fw-bold shadow-sm btn-go-books">Go to My Books</a>
                                </div>
                            </div>

                            <div class="modal fade" id="paymentModal<?= $book['loan_id']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content penalty-modal-content">
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title fw-bold">Confirm Penalty Clearance</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center py-4">
                                            <h2 class="fw-bold text-dark penalty-amount-display">₱<?= number_format($book['penalty'] ?? 0, 2); ?></h2>
                                            <p class="text-muted mb-4">Penalty fee calculated for <br><strong>"<?= htmlspecialchars($book['title']); ?>"</strong></p>
                                            
                                            <form action="../../app/controller/process/paymentProcess.php" method="POST">
                                                <input type="hidden" name="loan_id" value="<?= $book['loan_id']; ?>"> 
                                                <button type="submit" class="btn w-100 rounded-pill fw-bold py-2.5 text-white shadow-sm btn-confirm-payment">Confirm Payment</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="p-5 text-center border rounded-4 shadow-sm empty-loans-container">
                            <iconify-icon icon="lucide:book-check" class="mb-2"></iconify-icon>
                            <p class="m-0 fw-medium">No active book loans found.</p> 
                            <a href="browseBooks" class="text-primary fw-bold text-decoration-none d-inline-flex align-items-center gap-2 hover-lime"> Browse the Library</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="fullUpdateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form action="/kmkdt-Library/app/controller/process/updateProcess.php" method="POST"
                class="modal-content rounded-4 border-0 update-profile-modal" 
                onsubmit="return confirm('Are you sure you want to update your profile? This will modify your current account details.');">
                
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="fw-bold">Edit Profile & Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" name="fullName" class="form-control fst-italic"
                                value="<?php echo $_SESSION['authUser']['fullName'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Username</label>
                            <input type="text" name="username" class="form-control fst-italic"
                                value="<?php echo $_SESSION['authUser']['username'] ?? ''; ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Email Address</label>
                            <input type="email" name="emailAddress" class="form-control fst-italic"
                                value="<?php echo $_SESSION['authUser']['emailAddress'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="col-12 mt-4">
                            <p class="fw-bold mb-2 border-bottom">Address Details</p>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Street / House No.</label>
                            <input type="text" name="street" 
                                class="form-control fst-italic" 
                                value="<?php echo htmlspecialchars($user['street'] ?? ''); ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Barangay</label>
                            <input type="text" name="barangay" 
                                class="form-control fst-italic" 
                                value="<?php echo htmlspecialchars($user['barangay'] ?? ''); ?>">
                        </div>

                        <div class="col-md-4">  
                            <label class="form-label small fw-bold">City / Municipality</label>
                            <input type="text" name="city" 
                                class="form-control fst-italic" 
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
                    <button type="submit" class="btn btn-dark px-5 rounded-pill fw-bold text-white btn-submit-update">
                        Update Everything
                    </button>
                </div>
            </form>
        </div>
    </div>  

    <div class="container-xl px-4 mt-4 mb-5">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 p-4 payment-history-card">
                    <div class="d-flex align-items-center mb-4">
                        <div class="p-2 rounded-3 me-3 d-flex align-items-center justify-content-center history-icon-box">
                            <iconify-icon icon="lucide:history"></iconify-icon>
                        </div>
                        <div>
                            <h6 class="fw-bold m-0 text-dark history-header-title">Payment History</h6>
                            <p class="text-muted m-0 small history-header-sub">Audit logs of settled library penalties</p>
                        </div>
                    </div>

                    <div class="table-responsive w-100">
                        <table class="table table-borderless align-middle m-0 w-100 history-table">
                            <thead>
                                <tr class="text-muted fw-bold">
                                    <th class="pb-3 ps-0 text-start" style="width: 45%;">BOOK TITLE</th>
                                    <th class="pb-3 text-start" style="width: 35%;">DATE CLEARED</th>
                                    <th class="pb-3 text-end pe-0" style="width: 20%;">SETTLED</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $payments = getPaymentHistory($conn, $currentUserId);
                                if ($payments && $payments->num_rows > 0): 
                                    while ($pay = $payments->fetch_assoc()): 
                                ?>
                                    <tr>
                                        <td class="py-3 ps-0 fw-semibold text-dark text-start text-truncate">
                                            <?= htmlspecialchars($pay['title']); ?>
                                        </td>
                                        <td class="py-3 text-secondary text-start">
                                            <?= date('M d, Y h:i A', strtotime($pay['paid_at'])); ?>
                                        </td>
                                        <td class="py-3 text-end pe-0 fw-bold settled-amount-text">
                                            ₱<?= number_format($pay['amount_paid'], 2); ?>
                                        </td>
                                    </tr>
                                <?php 
                                    endwhile; 
                                else: 
                                ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-5 empty-history-box">
                                            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                                                <div class="rounded-circle p-3 mb-2 d-flex align-items-center justify-content-center icon-wrapper">
                                                    <iconify-icon icon="lucide:folder-open" class="text-muted"></iconify-icon>
                                                </div>
                                                <span class="fw-medium text-secondary d-block primary-msg">No payment records found</span>
                                                <span class="text-muted secondary-msg">Your penalty settlement logs will appear here.</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('./includes/footer.php'); ?>