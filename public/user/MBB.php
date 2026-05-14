<?php
require_once dirname(__DIR__, 2) . '/app/middleware/user_auth.php';

$myBooks = getMyBooks($conn, $currentUserId);

include_once 'includes/header.php';
include_once 'includes/tsbar.php';
?>

<div class="page-wrapper overflow-hidden">
    <section class="banner-section banner-inner-section position-relative overflow-hidden d-flex align-items-end"
        style="background-image: url('assets/images/backgrounds/MMB.jpg'); min-height: 400px; background-size: cover;">
        <div class="container text-center">
            <div class="d-flex flex-column align-items-center gap-4 pb-5 pb-xl-10 position-relative z-1">
                <div class="d-flex align-items-center gap-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="primary-spinning-leaf"></div>
                    <p class="mb-0 text-white fs-5 text-opacity-70">
                        Manage your current loans and <span style="color: #57cb57;"> track due dates.</span>.
                    </p>
                </div>
                <h1 class="mb-0 fs-16 text-white lh-1">My Borrowed Books</h1>
            </div>
        </div>
    </section>

    <section class="project py-5 py-lg-8 bg-light">
        <div class="container">
            <?php if (isset($_GET['status'])): ?>
                <div class="mb-5">
                    <?php if ($_GET['status'] == 'renewed'): ?>
                        <div class="alert alert-success rounded-4 border-0 shadow-sm p-3">Book renewed successfully! Your new due date has been updated.</div>
                    <?php elseif ($_GET['status'] == 'limit_reached'): ?>
                        <div class="alert alert-warning rounded-4 border-0 shadow-sm p-3">Maximum renewal limit reached for this book.</div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="row g-5">
                <?php if ($myBooks && $myBooks->num_rows > 0): ?>
                    <?php while ($book = $myBooks->fetch_assoc()): ?>
                        <?php
                        $category = $book['genre'] ?? 'Fiction';
                        $loanDays = (stripos($category, 'Fiction') !== false) ? 30 : 14;
                        if (stripos($category, 'Reserve') !== false) { $loanDays = 3; }

                        // Logic: Renewal works by resetting the loan period from the 'borrowed_at' date
                        $dueDate = strtotime($book['borrowed_at'] . " + $loanDays days");
                        $today = time();
                        $isOverdue = $today > $dueDate;
                        $penalty = $isOverdue ? ceil(($today - $dueDate) / 86400) * 5 : 0;

                        $renewalsUsed = $book['renewal_count'] ?? 0;
                        $renewalsLeft = 2 - $renewalsUsed;
                        ?>

                        <div class="col-12" data-aos="fade-up">
                            <div class="card border-0 shadow-lg rounded-5 overflow-hidden bg-white"
                                style="border-left: 12px solid <?php echo $isOverdue ? '#ff4d4d' : '#57cb57'; ?> !important;">
                                <div class="card-body p-0">
                                    <div class="row g-0">
                                        <div class="col-lg-4 col-md-5 bg-white d-flex align-items-center justify-content-center p-5 border-end">
                                            <img src="assets/images/books/<?php echo htmlspecialchars($book['cover_image'] ?? 'default.jpg'); ?>"
                                                alt="Book" class="img-fluid rounded-4 shadow-lg"
                                                style="max-height: 350px; width: auto; object-fit: contain;">
                                        </div>

                                        <div class="col-lg-8 col-md-7 p-4 p-lg-5 d-flex flex-column justify-content-center">
                                            <div class="d-flex justify-content-between align-items-start mb-4">
                                                <div>
                                                    <span class="badge mb-2 px-3 py-2 rounded-pill text-uppercase"
                                                        style="background-color: rgba(50, 205, 50, 0.1); color: #57cb57; font-weight: 700;">
                                                        <?php echo htmlspecialchars($category); ?>
                                                    </span>
                                                    <h2 class="display-5 fw-bold text-dark mt-2" style="font-size: 2.2rem;">
                                                        <?php echo htmlspecialchars($book['title']); ?>
                                                    </h2>
                                                </div>
                                                <div class="pt-2">
                                                    <?php if ($isOverdue): ?>
                                                        <span class="badge bg-danger rounded-pill px-4 py-2 fs-6">OVERDUE</span>
                                                    <?php else: ?>
                                                        <span class="badge rounded-pill px-4 py-2 fs-6 text-white"
                                                            style="background-color: #57cb57;">ON TIME</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <div class="row bg-dark rounded-4 p-4 mb-4 g-3 text-white">
                                                <div class="col-sm-4 border-end border-secondary">
                                                    <small class="text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem; color: #57cb57;">Due Date</small>
                                                    <p class="mb-0 fw-bold h4 <?php echo $isOverdue ? 'text-danger' : 'text-white'; ?>">
                                                        <?php echo date('M d, Y', $dueDate); ?>
                                                    </p>
                                                </div>
                                                <div class="col-sm-4 border-end border-secondary ps-sm-4">
                                                    <small class="text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem; color: #57cb57;">Renewals</small>
                                                    <p class="mb-0 fw-bold h4 text-white"><?php echo $renewalsLeft; ?> Available</p>
                                                </div>
                                                <div class="col-sm-4 ps-sm-4">
                                                    <small class="text-uppercase fw-bold d-block mb-1" style="font-size: 0.7rem; color: #57cb57;">Late Fee</small>
                                                    <p class="mb-0 fw-bold h4 <?php echo $penalty > 0 ? 'text-danger' : 'text-white'; ?>">
                                                        ₱<?php echo $penalty; ?>
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-wrap align-items-center gap-3">
                                                <?php if ($renewalsLeft > 0): ?>
                                                    <a href="/kmkdt-Library/app/controller/process/renewProcess.php?id=<?php echo $book['id']; ?>"
                                                        class="btn btn-lg rounded-pill px-5 fw-bold"
                                                        style="background-color: #57cb57; color: #000; border: none;"
                                                        onclick="return confirm('Renew this book for another <?php echo $loanDays; ?> days? You have <?php echo $renewalsLeft; ?> renewal(s) remaining.');">
                                                        Renew Now
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-lg rounded-pill px-5 fw-bold btn-secondary" disabled>Renew Locked</button>
                                                <?php endif; ?>

                                                <a href="/kmkdt-Library/app/controller/process/returnProcess.php?id=<?php echo $book['id']; ?>"
                                                    class="btn btn-lg btn-outline-dark rounded-pill px-5 fw-bold"
                                                    onclick="return confirm('Ready to return this book?');">
                                                    Return Book
                                                </a>

                                                <!-- Borrow More Button -->
                                                <a href="/kmkdt-Library/public/user/BrowBoks" 
                                                   class="btn btn-lg btn-link text-decoration-none fw-bold">
                                                   <iconify-icon icon="lucide:plus-circle" class="align-middle me-1"></iconify-icon> Borrow More
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <div class="bg-white p-5 rounded-5 shadow-sm border">
                            <iconify-icon icon="lucide:book-open" style="font-size: 5rem; color: #57cb57;"></iconify-icon>
                            <h2 class="mt-4 fw-bold text-dark">No books currently borrowed.</h2>
                            <a href="/kmkdt-Library/public/user/BrowseBooks" class="btn btn-primary rounded-pill px-5 py-3 mt-3 fw-bold"
                                style="background-color: #57cb57; color: #000; border: none;">
                                Browse Books</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php include('./includes/footer.php'); ?>