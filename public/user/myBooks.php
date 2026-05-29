<?php
require_once dirname(__DIR__, 2) . '/app/middleware/userAuth.php';

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
                        Manage your current loans and
                        <span class="highlight-text">track due dates.</span>
                    </p>
                </div>

                <h1 class="mb-0 fs-16 text-white lh-1">
                    My Borrowed Books
                </h1>

            </div>
        </div>
    </section>

    <section class="project py-5 py-lg-8 bg-light">
        <div class="container">

            <?php if (isset($_GET['status'])): ?>
                <div class="mb-5">
                    <?php if ($_GET['status'] == 'renewed'): ?>
                        <div class="alert alert-success rounded-4 border-0 shadow-sm p-3">
                            Book renewed successfully! Your new due date has been updated.
                        </div>
                    <?php elseif ($_GET['status'] == 'limit_reached'): ?>
                        <div class="alert alert-warning rounded-4 border-0 shadow-sm p-3">
                            Maximum renewal limit reached.
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

                        <a href="/kmkdt-Library/public/user/browseBooks" class="btn btn-lg btn-outline-secondary rounded-pill px-3 fw-bold">
                            <iconify-icon icon="lucide:plus-circle"></iconify-icon>
                            Borrow More Books
                        </a>  
                </div>

                <div class="row g-5">
                    <?php while ($book = $myBooks->fetch_assoc()): ?>

                        <?php
                        
                        $category = $book['category'] ?? 'General';

                       
                        if (stripos($category, 'Online') !== false) {
                            $loanDays = 0;
                        } elseif (
                            stripos($category, 'Reserve') !== false ||
                            stripos($category, 'Technology') !== false
                        ) {
                            $loanDays = 3;
                        } elseif (stripos($category, 'Research') !== false) {
                            $loanDays = 7;
                        } elseif (stripos($category, 'Non-Fiction') !== false) {
                            $loanDays = 14; 
                        } elseif (stripos($category, 'Fiction') !== false) {
                            $loanDays = 18;
                        } else {
                            $loanDays = 18;
                        }


                        // Due date
                        if (!empty($book['due_date'])) {
                            $dueDate = strtotime($book['due_date']);
                        } else {
                            $borrowedAt = strtotime($book['borrowed_at']);
                            $dueDate = strtotime("+$loanDays days", $borrowedAt);
                        }

                        $today = time();
                        $isOverdue = $today > $dueDate;

                        // Penalty
                        $penalty = $isOverdue
                            ? ceil(($today - $dueDate) / 86400) * 5
                            : 0;

                        $renewalsUsed = $book['renewal_count'] ?? 0;
                        $renewalsLeft = 1 - $renewalsUsed;
                        ?>

                        <div class="col-12" data-aos="fade-up">
                            <div class="card border-0 shadow-lg rounded-5 overflow-hidden bg-white loan-card <?= $isOverdue ? 'status-overdue' : 'status-on-time'; ?>">
                                <div class="card-body p-0">
                                    <div class="row g-0">

                                        <div class="col-lg-4 col-md-5 bg-white d-flex align-items-center justify-content-center p-5 border-end">
                                            <img src="/kmkdt-Library/app/uploads/covers/<?= htmlspecialchars($book['cover_image'] ?? 'default.jpg'); ?>"
                                                 alt="Book Cover"
                                                 class="img-fluid rounded-4 shadow-lg book-cover-img">
                                        </div>

                                        <div class="col-lg-8 col-md-7 p-4 p-lg-5 d-flex flex-column justify-content-center">
                                            
                                            <div class="d-flex justify-content-between align-items-start mb-4">
                                                <div>
                                                    <span class="badge mb-2 px-3 py-2 rounded-pill text-uppercase theme-badge category-badge">
                                                        <?= htmlspecialchars($category); ?>
                                                    </span>
                                                    <span class="badge mb-2 px-3 py-2 rounded-pill text-uppercase theme-badge">
                                                        <?= htmlspecialchars($book['genre'] ?? 'General'); ?>
                                                    </span>
                                                    <h2 class="display-5 fw-bold text-dark mt-2 book-title">
                                                        <?= htmlspecialchars($book['title']); ?>
                                                    </h2>
                                                </div>
                                            </div>

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
                                                                <input type="hidden" name="loan_id" value="<?= $book['loan_id']; ?>">
                                                                <button type="submit" class="btn btn-lg w-100 rounded-pill fw-bold text-white shadow-sm modal-submit-btn">
                                                                    Confirm & Process Payment
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row bg-dark rounded-4 p-4 mb-4 g-3 text-white">
                                                <div class="col-sm-4 border-end border-secondary">
                                                    <small class="text-uppercase fw-bold d-block mb-1 stat-label">Due Date</small>
                                                    <p class="mb-0 fw-bold h4 text-white"><?= date('M d, Y', $dueDate); ?></p>
                                                </div>

                                                <div class="col-sm-4 border-end border-secondary ps-sm-4">
                                                    <small class="text-uppercase fw-bold d-block mb-1 stat-label">Renewals</small>
                                                    <p class="mb-0 fw-bold h4 text-white"><?= $renewalsLeft; ?> Available</p>
                                                </div>

                                                <div class="col-sm-4 ps-sm-4">
                                                    <small class="text-uppercase fw-bold d-block mb-1 stat-label">Late Fee</small>
                                                    <p class="mb-0 fw-bold h4 text-white">₱<?= number_format($penalty, 2); ?></p>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-wrap gap-3 mt-4">
                                                <?php if ($isOverdue): ?>
                                                    <div class="w-100 mt-2">
                                                        <small class="text-danger fw-bold">
                                                            <iconify-icon icon="lucide:alert-circle"></iconify-icon> 
                                                            Return disabled until penalty is settled.
                                                        </small>
                                                    </div>
                                                    
                                                    <button type="button" class="btn btn-lg rounded-pill px-5 fw-bold btn-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#paymentModal<?= $book['loan_id']; ?>">
                                                        <iconify-icon icon="lucide:credit-card" class="me-2"></iconify-icon>
                                                        Pay Fee (₱<?= number_format($penalty, 2); ?>)
                                                    </button>

                                                    <button class="btn btn-lg btn-outline-secondary rounded-pill px-5 fw-bold" onclick="alert('Action Blocked: You must settle the outstanding penalty of ₱<?= number_format($penalty, 2); ?> before this book can be returned.');">
                                                        Return Book
                                                    </button>
                                                <?php else: ?>
                                                    <?php if ($renewalsLeft > 0): ?>
                                                        <a href="/kmkdt-Library/app/controller/process/renewProcess.php?id=<?= $book['loan_id']; ?>" 
                                                           class="btn btn-lg rounded-pill px-5 fw-bold shadow-sm btn-primary-theme"
                                                           onclick="return confirm('Renew this book for another <?= $loanDays; ?> days?');">
                                                            Renew Now
                                                        </a>
                                                    <?php endif; ?>

                                                    <a href="/kmkdt-Library/app/controller/process/returnProcess.php?id=<?= $book['loan_id']; ?>" 
                                                       class="btn btn-lg btn-outline-dark rounded-pill px-5 fw-bold"
                                                       onclick="return confirm('Ready to return this book?');">
                                                        Return Book
                                                    </a>
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
                        
                        <a href="/kmkdt-Library/public/user/browseBooks" 
                           class="btn btn-primary rounded-pill px-5 py-3 mt-3 fw-bold btn-primary-theme">
                            Browse Books
                        </a>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </section>
</div>

<?php include('./includes/footer.php'); ?>