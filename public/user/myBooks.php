<?php
require_once dirname(__DIR__, 2) . '/app/middleware/userAuth.php';

// Fetch the current user's active loan records safely from userController
$myBooks = getMyBooks($conn, $currentUserId);

include_once 'includes/header.php';
include_once 'includes/tsbar.php';
?>

<div class="page-wrapper overflow-hidden my-borrowed-books-page">

    <section class="banner-section banner-inner-section position-relative overflow-hidden d-flex align-items-end main-banner">
        <div class="container text-center">
            <div class="d-flex flex-column align-items-center gap-4 pb-5 pb-xl-10 position-relative z-1">
                <div class="d-flex align-items-center gap-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="primary-spinning-leaf"></div>
                    <p class="mb-0 text-white fs-5 text-opacity-70">
                        Manage your current loans and <span class="highlight-text">track due dates.</span>
                    </p>
                </div>
                <h1 class="mb-0 fs-16 text-white lh-1">My Borrowed Books</h1>
            </div>
        </div>
    </section>

    <section class="project py-5 py-lg-8 bg-light">
        <div class="container">

            <?php if (isset($_GET['status']) || isset($_GET['success']) || isset($_GET['error'])): ?>
                <div class="mb-5">
                    <?php 
                    $status = $_GET['status'] ?? '';
                    $success = $_GET['success'] ?? '';
                    $error = $_GET['error'] ?? '';
                    
                    if ($status === 'renewed'): ?>
                        <div class="alert alert-success rounded-4 border-0 shadow-sm p-3 mb-0">
                            <iconify-icon icon="lucide:check-circle" class="me-2 align-middle"></iconify-icon> Book renewed successfully! Your new due date has been updated.
                        </div>
                    <?php elseif ($status === 'limit_reached'): ?>
                        <div class="alert alert-warning rounded-4 border-0 shadow-sm p-3 mb-0">
                            <iconify-icon icon="lucide:alert-triangle" class="me-2 align-middle"></iconify-icon> Maximum renewal limit reached.
                        </div>
                    <?php elseif ($success === 'return_requested'): ?>
                        <div class="alert alert-success rounded-4 border-0 shadow-sm p-3 mb-0">
                            <iconify-icon icon="lucide:clock" class="me-2 align-middle"></iconify-icon> Return request submitted successfully! Pending administrator approval.
                        </div>
                    <?php elseif ($error === 'payment_required'): ?>
                        <div class="alert alert-danger rounded-4 border-0 shadow-sm p-3 mb-0">
                            <iconify-icon icon="lucide:shield-alert" class="me-2 align-middle"></iconify-icon> Return Blocked: Please settle your outstanding penalty late fee first.
                        </div>
                    <?php elseif (!empty($error)): ?>
                        <div class="alert alert-danger rounded-4 border-0 shadow-sm p-3 mb-0">
                            <iconify-icon icon="lucide:x-circle" class="me-2 align-middle"></iconify-icon> Unable to process request. Error Code: <?= htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($myBooks && $myBooks->num_rows > 0): ?>

                <div class="d-flex justify-content-between align-items-center mb-5 p-4 bg-white rounded-5 shadow-sm border-start border-5 active-loans-bar">
                    <div>
                        <h4 class="mb-1 fw-bold text-dark">Active Loans</h4>
                        <p class="mb-0 text-muted small">
                            You currently have <?= $myBooks->num_rows; ?> book(s) borrowed.
                        </p>
                    </div>
                    <a href="/kmkdt-Library/public/user/browseBooks" class="btn btn-lg btn-outline-secondary rounded-pill px-4 fw-bold btn-borrow-more d-inline-flex align-items-center gap-2">
                        <iconify-icon icon="lucide:plus-circle"></iconify-icon>
                        <span>Borrow More Books</span>
                    </a>
                </div>

                <div class="row g-5">
                    <?php while ($book = $myBooks->fetch_assoc()): ?>
                        <?php
                        $category = $book['category'] ?? 'General';
                        $loanStatus = strtolower($book['status'] ?? 'borrowed');

                        // SYSTEM CORE RULES: Check if the string contains 'return' or 'pending'
                        $isPendingReturn = (strpos($loanStatus, 'return') !== false || strpos($loanStatus, 'pending') !== false);

                        if (stripos($category, 'Online') !== false) {
                            $loanDays = 0;
                        } elseif (stripos($category, 'Reserve') !== false || stripos($category, 'Technology') !== false) {
                            $loanDays = 3;
                        } elseif (stripos($category, 'Research') !== false) {
                            $loanDays = 7;
                        } elseif (stripos($category, 'Non-Fiction') !== false) {
                            $loanDays = 14; 
                        } else {
                            $loanDays = 18;
                        }

                        // Determine due date mapping safely
                        if (!empty($book['due_date'])) {
                            $dueDate = strtotime($book['due_date']);
                        } else {
                            $borrowedAt = !empty($book['borrowed_at']) ? strtotime($book['borrowed_at']) : time();
                            $dueDate = strtotime("+$loanDays days", $borrowedAt);
                        }

                        $today = time();
                        
                        // If it's pending return approval, we lock evaluation so penalties freeze in place
                        $isOverdue = (!$isPendingReturn && (($today > $dueDate) || ($loanStatus === 'overdue')));

                        // Penalty calculation
                        $penalty = 0;
                        if ($isOverdue) {
                            $diff = $today - $dueDate;
                            $daysLate = ($diff > 0) ? ceil($diff / 86400) : 1;
                            $penalty = $daysLate * 5;
                        }

                        $renewalsUsed = $book['renewal_count'] ?? 0;
                        $renewalsLeft = 1 - $renewalsUsed;

                        // Dynamic Visual Styles
                        $cardStatusClass = 'status-on-time';
                        if ($isPendingReturn) {
                            $cardStatusClass = 'status-pending border-start border-warning border-5 bg-light-subtle'; 
                        } elseif ($isOverdue) {
                            $cardStatusClass = 'status-overdue';
                        }
                        ?>

                        <div class="col-12" data-aos="fade-up">
                            <div class="card border-0 shadow-lg rounded-5 overflow-hidden bg-white loan-card <?= $cardStatusClass; ?>">
                                <div class="card-body p-0">
                                    <div class="row g-0">

                                        <!-- Book Cover Section -->
                                        <div class="col-lg-4 col-md-5 bg-white d-flex align-items-center justify-content-center p-5 border-end">
                                            <img src="/kmkdt-Library/app/uploads/covers/<?= htmlspecialchars($book['cover_image'] ?? 'default.jpg'); ?>"
                                                 alt="Book Cover"
                                                 class="img-fluid rounded-4 shadow-lg book-cover-img">
                                        </div>

                                        <!-- Core Content & Info Area -->
                                        <div class="col-lg-8 col-md-7 p-4 p-lg-5 d-flex flex-column justify-content-center">
                                            
                                            <div class="d-flex justify-content-between align-items-start mb-4">
                                                <div>
                                                    <span class="badge px-3 py-2 rounded-pill text-uppercase fw-bold shadow-sm" 
                                                        style="background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; font-size: 0.75rem; letter-spacing: 0.5px;">
                                                        <?= htmlspecialchars($category); ?>
                                                    </span>
                                                    
                                                    <span class="badge px-3 py-2 rounded-pill text-uppercase fw-bold shadow-sm" 
                                                        style="background-color: #1e293b; color: #f8fafc; font-size: 0.75rem; letter-spacing: 0.5px;">
                                                        <?= htmlspecialchars($book['genre'] ?? 'General'); ?>
                                                    </span>
                                                                                                
                                                    <?php if ($isPendingReturn): ?>
                                                        <span class="badge mb-2 px-3 py-2 rounded-pill text-uppercase bg-warning text-dark border border-warning-subtle fw-bold animate-pulse">
                                                            <iconify-icon icon="lucide:clock" class="me-1 align-middle"></iconify-icon> Awaiting Admin Return Approval
                                                        </span>
                                                    <?php elseif ($isOverdue): ?>
                                                        <span class="badge mb-2 px-3 py-2 rounded-pill text-uppercase bg-danger text-white">
                                                            Overdue
                                                        </span>
                                                    <?php endif; ?>

                                                    <h2 class="display-5 fw-bold text-dark mt-2 book-title">
                                                        <?= htmlspecialchars($book['title']); ?>
                                                    </h2>
                                                </div>
                                            </div>

                                            <!-- Overdue Payment Modal Layout Definition -->
                                            <?php if ($isOverdue): ?>
                                            <div class="modal fade payment-modal" id="paymentModal<?= $book['loan_id']; ?>" tabindex="-1" aria-labelledby="paymentModalLabel<?= $book['loan_id']; ?>" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow">
                                                        <div class="modal-header border-0">
                                                            <h5 class="modal-title fw-bold text-dark">Settle Penalty Balance</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-center p-4">
                                                            <p class="text-muted">Confirm payment transaction processing for:</p>
                                                            <h4 class="fw-bold mb-3"><?= htmlspecialchars($book['title']); ?></h4>
                                                            <h2 class="fw-bold mb-4 modal-penalty-amount">₱<?= number_format($penalty, 2); ?></h2>
                                                            
                                                            <form action="/kmkdt-Library/app/controller/process/paymentProcess.php" method="POST">
                                                                <input type="hidden" name="loan_id" value="<?= intval($book['loan_id']); ?>">
                                                                <button type="submit" class="btn btn-lg w-100 rounded-pill fw-bold text-white shadow-sm modal-submit-btn">
                                                                    Confirm & Process Payment
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <!-- Metadata Stats Row Metrics Box -->
                                            <div class="row bg-dark rounded-4 p-4 mb-4 g-3 text-white">
                                                <div class="col-sm-4 border-end border-secondary">
                                                    <small class="text-uppercase fw-bold d-block mb-1 stat-label">Due Date</small>
                                                    <p class="mb-0 fw-bold h4 text-white"><?= date('M d, Y', $dueDate); ?></p>
                                                </div>
                                                <div class="col-sm-4 border-end border-secondary ps-sm-4">
                                                    <small class="text-uppercase fw-bold d-block mb-1 stat-label">Renewals</small>
                                                    <p class="mb-0 fw-bold h4 text-white"><?= $isPendingReturn ? '0' : $renewalsLeft; ?> Available</p>
                                                </div>
                                                <div class="col-sm-4 ps-sm-4">
                                                    <small class="text-uppercase fw-bold d-block mb-1 stat-label">Late Fee</small>
                                                    <p class="mb-0 fw-bold h4 text-white">₱<?= number_format($penalty, 2); ?></p>
                                                </div>
                                            </div>

                                            <!-- Dynamic Action Control Workflow Engine Area -->
                                            <div class="loan-actions-container d-flex flex-wrap gap-3 align-items-center">
                                                
                                                <?php if ($isPendingReturn): ?>
                                                    <!-- CASE 1: PENDING RETURN SYSTEM NOTICE -->
                                                    <div class="pulse-alert p-3 bg-warning-subtle text-warning-emphasis border border-warning-subtle d-flex align-items-center gap-3 transition-all w-100" style="border-radius: 16px;">
                                                        <div class="bg-warning text-white rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 42px; height: 42px;">
                                                            <iconify-icon icon="lucide:clock" class="fs-4"></iconify-icon>
                                                        </div>
                                                        <div>
                                                            <strong class="d-block mb-0.5 fs-6" style="color: #664d03; font-weight: 700;">Return Verification Processing</strong>
                                                            <span class="small text-secondary-emphasis d-block" style="line-height: 1.45; opacity: 0.9;">You have submitted this book for return tracking. Additional actions are paused until an administrator checks the physical copy back into storage inventory.</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <button class="btn btn-lg rounded-pill px-5 fw-bold d-flex align-items-center gap-2 shadow-sm" disabled 
                                                            style="cursor: not-allowed; background-color: #343a40; border-color: #343a40; color: #f8f9fa; font-size: 0.95rem; padding-top: 12px; padding-bottom: 12px;">
                                                        <iconify-icon icon="lucide:lock-keyhole" class="fs-5"></iconify-icon> Return Pending Approval
                                                    </button>

                                                <?php elseif ($isOverdue): ?>
                                                    <!-- CASE 2: OVERDUE DELINQUENT NOTICE RESTRICTION -->
                                                    <div class="p-3 bg-danger-subtle text-danger-emphasis border border-danger-subtle d-flex align-items-center gap-3 w-100" style="border-radius: 16px;">
                                                        <div class="bg-danger text-white rounded-3 d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 42px; height: 42px;">
                                                            <iconify-icon icon="lucide:alert-triangle" class="fs-4"></iconify-icon>
                                                        </div>
                                                        <div>
                                                            <strong class="d-block mb-0.5 fs-6" style="color: #842029; font-weight: 700;">Late Fine Balance Unsettled</strong>
                                                            <span class="small text-secondary-emphasis d-block" style="line-height: 1.45; opacity: 0.9;">Standard inventory check-in functions are temporarily restricted until outstanding penalty accumulation balances are cleared.</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <button type="button" class="btn btn-lg rounded-pill px-5 fw-bold btn-danger shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#paymentModal<?= $book['loan_id']; ?>" style="padding-top: 12px; padding-bottom: 12px; font-size: 0.95rem;">
                                                        <iconify-icon icon="lucide:credit-card" class="fs-5"></iconify-icon> Pay Fine (₱<?= number_format($penalty, 2); ?>)
                                                    </button>
                                                    
                                                    <form action="/kmkdt-Library/app/controller/process/returnProcess.php" method="POST" class="d-inline">
                                                        <input type="hidden" name="request_id" value="<?= intval($book['loan_id']); ?>">
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit" class="btn btn-lg btn-outline-secondary rounded-pill px-5 fw-bold" style="padding-top: 12px; padding-bottom: 12px; font-size: 0.95rem;">
                                                            Return Book
                                                        </button>
                                                    </form>

                                                <?php else: ?>
    
                                                    <?php if ($renewalsLeft > 0): ?>
                                                        <a href="/kmkdt-Library/app/controller/process/renewProcess.php?id=<?= $book['loan_id']; ?>" 
                                                           class="btn btn-lg rounded-pill px-5 fw-bold shadow-sm btn-primary-theme"
                                                           onclick="return confirm('Renew this book for another <?= $loanDays; ?> days?');">
                                                            Renew Now
                                                        </a>
                                                    <?php endif; ?>

                                                    <form action="/kmkdt-Library/app/controller/process/returnProcess.php" method="POST" class="d-inline"
                                                          onsubmit="return confirm('Submit a return request to the library admin? Once sent, you cannot undo this until approved.');">
                                                        <input type="hidden" name="request_id" value="<?= intval($book['loan_id']); ?>">
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit" class="btn btn-lg btn-outline-dark rounded-pill px-5 fw-bold">
                                                            <iconify-icon icon="lucide:log-out" style="transform: rotate(-90deg);" class="fs-5"></iconify-icon> Return Book
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

            <?php else: ?>

                <div class="col-12 text-center py-5">
                    <div class="bg-white p-5 rounded-5 shadow-sm border">
                        <iconify-icon icon="lucide:book-open" class="empty-state-icon"></iconify-icon>
                        <h2 class="mt-4 fw-bold text-dark">No books currently borrowed.</h2>
                        <a href="/kmkdt-Library/public/user/browseBooks" class="btn btn-primary rounded-pill px-5 py-3 mt-3 fw-bold btn-primary-theme">
                            Browse Books
                        </a>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </section>
</div>

<?php include('./includes/footer.php'); ?>